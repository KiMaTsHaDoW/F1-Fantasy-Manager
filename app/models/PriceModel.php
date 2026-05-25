<?php
require_once BASE_PATH . '/core/Model.php';

class PriceModel extends Model {
    const FLOOR      = 4.0;
    const CEIL       = 35.0;
    const CLAMP      = 1.5;
    const PERF       = 0.05;
    const MKT        = 1.5;
    const DAMPENER   = 0.8;

    public function getPrice(string $itemId, string $type): ?float {
        $r = $this->query(
            'SELECT price FROM market_prices WHERE item_id=? AND item_type=?',
            [$itemId, $type], 'ss'
        );
        if ($r && $r->num_rows > 0) {
            return (float)$r->fetch_assoc()['price'];
        }
        return null;
    }

    public function setPrice(string $itemId, string $type, float $price): bool {
        $price = max(self::FLOOR, min(self::CEIL, round($price, 1)));
        return $this->execute(
            'INSERT INTO market_prices (item_id, item_type, price)
             VALUES (?,?,?)
             ON DUPLICATE KEY UPDATE price=VALUES(price), updated_at=NOW()',
            [$itemId, $type, $price], 'ssd'
        );
    }

    public function getAllPrices(): array {
        $r = $this->query('SELECT item_id, item_type, price FROM market_prices');
        $map = [];
        if ($r) while ($row = $r->fetch_assoc()) {
            $map[$row['item_type']][$row['item_id']] = (float)$row['price'];
        }
        return $map;
    }

    // action = 'buy' | 'sell', round = 0 means pending
    public function recordTransaction(string $itemId, string $type, string $action, int $userId): bool {
        return $this->execute(
            'INSERT INTO price_transactions (item_id, item_type, action, user_id)
             VALUES (?,?,?,?)',
            [$itemId, $type, $action, $userId], 'sssi'
        );
    }

    // Recalculate all prices using last race results + pending transactions.
    // Returns array of ['item_id' => ['old'=>x, 'new'=>y, 'delta'=>z]]
    public function recalculate(array $driverPoints, array $constructorPoints, int $round): array {
        $totalUsers = max(1, $this->getTotalUsers());
        $updated = [];

        foreach ($driverPoints as $id => $pts) {
            $price = $this->getPrice($id, 'driver');
            if ($price === null) continue;

            $delta    = $this->calcDelta($price, (float)$pts, $id, 'driver', $totalUsers, false);
            $newPrice = max(self::FLOOR, min(self::CEIL, round($price + $delta, 1)));
            $this->setPrice($id, 'driver', $newPrice);
            $this->saveHistory($id, 'driver', $newPrice, $round);
            $updated[$id] = ['old' => $price, 'new' => $newPrice, 'delta' => round($delta, 2), 'type' => 'driver'];
        }

        foreach ($constructorPoints as $id => $pts) {
            $price = $this->getPrice($id, 'constructor');
            if ($price === null) continue;

            $delta    = $this->calcDelta($price, (float)$pts, $id, 'constructor', $totalUsers, true);
            $newPrice = max(self::FLOOR, min(self::CEIL, round($price + $delta, 1)));
            $this->setPrice($id, 'constructor', $newPrice);
            $this->saveHistory($id, 'constructor', $newPrice, $round);
            $updated[$id] = ['old' => $price, 'new' => $newPrice, 'delta' => round($delta, 2), 'type' => 'constructor'];
        }

        // Mark all pending transactions as processed for this round
        $this->execute(
            'UPDATE price_transactions SET race_round=? WHERE race_round=0',
            [$round], 'i'
        );

        return $updated;
    }

    public function getPriceHistory(string $itemId, string $type, int $limit = 10): array {
        $r = $this->query(
            'SELECT price, race_round, calculated_at FROM price_history
             WHERE item_id=? AND item_type=?
             ORDER BY race_round DESC LIMIT ' . (int)$limit,
            [$itemId, $type], 'ss'
        );
        $history = [];
        if ($r) while ($row = $r->fetch_assoc()) {
            $history[] = $row;
        }
        return $history;
    }

    private function calcDelta(float $price, float $pts, string $id, string $type, int $totalUsers, bool $dampened): float {
        $expected  = $price * 0.7;
        $deltaPerf = self::PERF * ($pts - $expected);

        [$buys, $sells] = $this->getPendingTransactionCounts($id, $type);
        $deltaMkt = self::MKT * (($buys - $sells) / $totalUsers);

        $total = $deltaPerf + $deltaMkt;
        if ($dampened) $total *= self::DAMPENER;
        return max(-self::CLAMP, min(self::CLAMP, $total));
    }

    private function getPendingTransactionCounts(string $itemId, string $type): array {
        $r = $this->query(
            'SELECT action, COUNT(*) as cnt FROM price_transactions
             WHERE item_id=? AND item_type=? AND race_round=0
             GROUP BY action',
            [$itemId, $type], 'ss'
        );
        $buys = $sells = 0;
        if ($r) while ($row = $r->fetch_assoc()) {
            if ($row['action'] === 'buy') $buys = (int)$row['cnt'];
            else $sells = (int)$row['cnt'];
        }
        return [$buys, $sells];
    }

    private function getTotalUsers(): int {
        $r = $this->query('SELECT COUNT(*) as cnt FROM users');
        return $r ? (int)$r->fetch_assoc()['cnt'] : 1;
    }

    private function saveHistory(string $itemId, string $type, float $price, int $round): void {
        $this->execute(
            'INSERT INTO price_history (item_id, item_type, price, race_round) VALUES (?,?,?,?)',
            [$itemId, $type, $price, $round], 'ssdi'
        );
    }
}
