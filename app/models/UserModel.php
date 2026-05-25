<?php
require_once BASE_PATH . '/core/Model.php';

class UserModel extends Model {

    public function findByEmail(string $email): ?array {
        $result = $this->query('SELECT * FROM users WHERE email = ?', [$email], 's');
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function findById(int $id): ?array {
        $result = $this->query('SELECT * FROM users WHERE id = ?', [$id], 'i');
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function create(string $username, string $email, string $password): int|false {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $ok = $this->execute(
            'INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())',
            [$username, $email, $hash],
            'sss'
        );
        return $ok ? $this->lastInsertId() : false;
    }

    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    public function emailExists(string $email): bool {
        $result = $this->query('SELECT id FROM users WHERE email = ?', [$email], 's');
        return $result && $result->num_rows > 0;
    }

    public function usernameExists(string $username): bool {
        $result = $this->query('SELECT id FROM users WHERE username = ?', [$username], 's');
        return $result && $result->num_rows > 0;
    }

    public function updatePassword(int $id, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        return $this->execute('UPDATE users SET password = ? WHERE id = ?', [$hash, $id], 'si');
    }

    public function updateUsername(int $id, string $username): bool {
        return $this->execute('UPDATE users SET username = ? WHERE id = ?', [$username, $id], 'si');
    }
}
