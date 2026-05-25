<?php
require_once BASE_PATH . '/core/Model.php';

class FantasyTeamModel extends Model {

    public function getTeamByUser(int $userId, int $leagueId): ?array {
        $result = $this->query(
            'SELECT * FROM fantasy_teams WHERE user_id=? AND league_id=?',
            [$userId, $leagueId], 'ii'
        );
        if ($result && $result->num_rows > 0) return $result->fetch_assoc();
        return null;
    }

    public function getAllTeamsByUser(int $userId): array {
        $result = $this->query(
            'SELECT ft.*, l.name AS league_name
             FROM fantasy_teams ft
             JOIN leagues l ON ft.league_id = l.id
             WHERE ft.user_id = ?
             ORDER BY ft.created_at DESC',
            [$userId], 'i'
        );
        $teams = [];
        if ($result) while ($row = $result->fetch_assoc()) $teams[] = $row;
        return $teams;
    }

    public function createTeam(int $userId, int $leagueId, string $teamName): int|false {
        $ok = $this->execute(
            'INSERT INTO fantasy_teams (user_id, league_id, team_name, total_points, budget_used, created_at)
             VALUES (?, ?, ?, 0, 0, NOW())',
            [$userId, $leagueId, $teamName], 'iis'
        );
        return $ok ? $this->lastInsertId() : false;
    }

    public function updateTeam(int $teamId, float $budgetUsed): bool {
        return $this->execute(
            'UPDATE fantasy_teams SET budget_used=?, updated_at=NOW() WHERE id=?',
            [$budgetUsed, $teamId], 'di'
        );
    }

    public function getTeamSelections(int $teamId): array {
        $result = $this->query(
            'SELECT * FROM fantasy_selections WHERE team_id=?',
            [$teamId], 'i'
        );
        $selections = ['drivers' => [], 'constructors' => []];
        if (!$result) return $selections;
        while ($row = $result->fetch_assoc()) {
            if ($row['type'] === 'driver') $selections['drivers'][]      = $row['item_id'];
            else                           $selections['constructors'][] = $row['item_id'];
        }
        return $selections;
    }

    public function clearSelections(int $teamId): bool {
        $this->execute('DELETE FROM fantasy_driver_points WHERE team_id=?', [$teamId], 'i');
        return $this->execute('DELETE FROM fantasy_selections WHERE team_id=?', [$teamId], 'i');
    }

    public function addSelection(int $teamId, string $type, string $itemId, float $price): bool {
        return $this->execute(
            'INSERT INTO fantasy_selections (team_id, type, item_id, price) VALUES (?,?,?,?)',
            [$teamId, $type, $itemId, $price], 'issd'
        );
    }

    public function getRanking(?int $leagueId = null): array {
        if ($leagueId) {
            $result = $this->query(
                'SELECT ft.*, u.username FROM fantasy_teams ft
                 JOIN users u ON ft.user_id = u.id
                 WHERE ft.league_id = ?
                 ORDER BY ft.total_points DESC LIMIT 5',
                [$leagueId], 'i'
            );
        } else {
            $result = $this->query(
                'SELECT ft.*, u.username, l.name AS league_name
                 FROM fantasy_teams ft
                 JOIN users u ON ft.user_id = u.id
                 JOIN leagues l ON ft.league_id = l.id
                 ORDER BY ft.total_points DESC LIMIT 5'
            );
        }
        $ranking = [];
        if ($result) while ($row = $result->fetch_assoc()) $ranking[] = $row;
        return $ranking;
    }

    public function updatePoints(int $teamId, float $points): bool {
        return $this->execute(
            'UPDATE fantasy_teams SET total_points=total_points+? WHERE id=?',
            [$points, $teamId], 'di'
        );
    }

    public function isRoundScored(string $season, int $round, string $type): bool {
        $result = $this->query(
            'SELECT 1 FROM scored_rounds WHERE season=? AND round=? AND type=?',
            [$season, $round, $type], 'sis'
        );
        return $result && $result->num_rows > 0;
    }

    public function markRoundScored(string $season, int $round, string $type): bool {
        $this->execute(
            'INSERT IGNORE INTO scored_rounds (season, round, type, scored_at) VALUES (?,?,?,NOW())',
            [$season, $round, $type], 'sis'
        );
        return $this->affectedRows() > 0;
    }

    // Atomic claim: returns true only if THIS call inserted the row (no other process scored it)
    public function claimRoundScore(string $season, int $round, string $type): bool {
        return $this->markRoundScored($season, $round, $type);
    }

    public function deleteTeamByUser(int $userId, int $leagueId): void {
        $team = $this->getTeamByUser($userId, $leagueId);
        if (!$team) return;
        $id = $team['id'];
        $this->execute('DELETE FROM fantasy_driver_points WHERE team_id=?', [$id], 'i');
        $this->execute('DELETE FROM fantasy_selections WHERE team_id=?',    [$id], 'i');
        $this->execute('DELETE FROM fantasy_teams WHERE id=?',              [$id], 'i');
    }

    public function cleanupOrphanedTeams(): void {
        $this->execute(
            'DELETE ft FROM fantasy_teams ft
             LEFT JOIN leagues l ON ft.league_id = l.id
             WHERE l.id IS NULL'
        );
    }

    public function scoreRound(array $driverPoints, array $constructorPoints): array {
        $summary = [];
        $offset  = 0;
        $batch   = 5;

        do {
            $result = $this->query(
                'SELECT * FROM fantasy_teams LIMIT ' . $batch . ' OFFSET ' . $offset
            );
            $teams = [];
            if ($result) while ($row = $result->fetch_assoc()) $teams[] = $row;
            $offset += $batch;

            foreach ($teams as $team) {
                $selections = $this->getTeamSelections($team['id']);
                $pts = 0.0;
                foreach ($selections['drivers'] as $did) {
                    $p = $driverPoints[$did] ?? 0;
                    $pts += $p;
                    if ($p != 0) $this->upsertSelectionPoints($team['id'], $did, 'driver', $p);
                }
                foreach ($selections['constructors'] as $cid) {
                    $p = $constructorPoints[$cid] ?? 0;
                    $pts += $p;
                    if ($p != 0) $this->upsertSelectionPoints($team['id'], $cid, 'constructor', $p);
                }
                $this->updatePoints($team['id'], $pts);
                $summary[] = ['team_name' => $team['team_name'], 'points' => $pts];
            }
        } while (count($teams) === $batch);

        return $summary;
    }

    private function upsertSelectionPoints(int $teamId, string $itemId, string $type, float $pts): void {
        $this->execute(
            'INSERT INTO fantasy_driver_points (team_id, item_id, type, points) VALUES (?,?,?,?)
             ON DUPLICATE KEY UPDATE points = points + ?',
            [$teamId, $itemId, $type, $pts, $pts], 'issdd'
        );
    }

    public function getSelectionPoints(int $teamId): array {
        $result = $this->query(
            'SELECT item_id, type, points FROM fantasy_driver_points WHERE team_id=?',
            [$teamId], 'i'
        );
        $map = ['driver' => [], 'constructor' => []];
        if ($result) while ($row = $result->fetch_assoc()) {
            $map[$row['type']][$row['item_id']] = (float)$row['points'];
        }
        return $map;
    }
}
