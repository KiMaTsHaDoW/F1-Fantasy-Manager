<?php
class Model {
    protected ?mysqli $db = null;

    protected function getDB(): mysqli {
        if ($this->db === null) {
            $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($this->db->connect_errno) {
                die('Error de conexión a la base de datos: ' . $this->db->connect_error);
            }
            $this->db->set_charset('utf8mb4');
        }
        return $this->db;
    }

    protected function query(string $sql, array $params = [], string $types = ''): mysqli_result|bool {
        $db = $this->getDB();
        if (empty($params)) {
            return $db->query($sql);
        }
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            die('Error en consulta preparada: ' . $db->error);
        }
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    protected function execute(string $sql, array $params = [], string $types = ''): bool {
        $db = $this->getDB();
        $stmt = $db->prepare($sql);
        if (!$stmt) return false;
        if (!empty($types) && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    protected function lastInsertId(): int {
        return $this->db->insert_id;
    }

    protected function affectedRows(): int {
        return $this->db?->affected_rows ?? 0;
    }

    public function __destruct() {
        if ($this->db) {
            $this->db->close();
        }
    }
}
