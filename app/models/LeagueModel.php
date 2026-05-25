<?php
require_once BASE_PATH . '/core/Model.php';

class LeagueModel extends Model {

    public function getPublicLeagues(): array {
        $result = $this->query(
            'SELECT l.*, u.username AS creator_name,
                    (SELECT COUNT(*) FROM league_members lm WHERE lm.league_id = l.id) AS member_count
             FROM leagues l
             JOIN users u ON l.creator_id = u.id
             WHERE l.is_public = 1
             ORDER BY l.created_at DESC'
        );
        $leagues = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) $leagues[] = $row;
        }
        return $leagues;
    }

    public function getMyLeagues(int $userId): array {
        $result = $this->query(
            'SELECT l.*, u.username AS creator_name,
                    (SELECT COUNT(*) FROM league_members lm WHERE lm.league_id = l.id) AS member_count
             FROM leagues l
             JOIN users u ON l.creator_id = u.id
             JOIN league_members lmem ON lmem.league_id = l.id
             WHERE lmem.user_id = ?
             ORDER BY lmem.joined_at DESC',
            [$userId], 'i'
        );
        $leagues = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) $leagues[] = $row;
        }
        return $leagues;
    }

    public function getById(int $id): ?array {
        $result = $this->query(
            'SELECT l.*, u.username AS creator_name,
                    (SELECT COUNT(*) FROM league_members lm WHERE lm.league_id = l.id) AS member_count
             FROM leagues l
             JOIN users u ON l.creator_id = u.id
             WHERE l.id = ?',
            [$id], 'i'
        );
        if ($result && $result->num_rows > 0) return $result->fetch_assoc();
        return null;
    }

    public function getByInviteCode(string $code): ?array {
        $result = $this->query(
            'SELECT * FROM leagues WHERE invite_code = ?',
            [$code], 's'
        );
        if ($result && $result->num_rows > 0) return $result->fetch_assoc();
        return null;
    }

    public function create(int $creatorId, string $name, string $description, bool $isPublic): int|false {
        $code = strtoupper(bin2hex(random_bytes(4)));
        $pub  = $isPublic ? 1 : 0;
        $ok   = $this->execute(
            'INSERT INTO leagues (name, description, creator_id, is_public, invite_code, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())',
            [$name, $description, $creatorId, $pub, $code], 'ssiss'
        );
        if (!$ok) return false;
        $leagueId = $this->lastInsertId();
        // El creador entra automáticamente
        $this->join($leagueId, $creatorId);
        return $leagueId;
    }

    public function isMember(int $leagueId, int $userId): bool {
        $result = $this->query(
            'SELECT 1 FROM league_members WHERE league_id = ? AND user_id = ?',
            [$leagueId, $userId], 'ii'
        );
        return $result && $result->num_rows > 0;
    }

    public function join(int $leagueId, int $userId): bool {
        return $this->execute(
            'INSERT IGNORE INTO league_members (league_id, user_id, joined_at) VALUES (?, ?, NOW())',
            [$leagueId, $userId], 'ii'
        );
    }

    public function leave(int $leagueId, int $userId): bool {
        return $this->execute(
            'DELETE FROM league_members WHERE league_id = ? AND user_id = ?',
            [$leagueId, $userId], 'ii'
        );
    }

    public function getRanking(int $leagueId): array {
        $result = $this->query(
            'SELECT ft.team_name, ft.total_points, ft.budget_used, u.username, u.id AS user_id
             FROM league_members lm
             JOIN users u ON lm.user_id = u.id
             LEFT JOIN fantasy_teams ft ON ft.user_id = lm.user_id AND ft.league_id = lm.league_id
             WHERE lm.league_id = ?
             ORDER BY ft.total_points DESC, u.username ASC LIMIT 5',
            [$leagueId], 'i'
        );
        $ranking = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) $ranking[] = $row;
        }
        return $ranking;
    }
}
