<?php

namespace App;

class Settings {
    private \PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function get(): array {
        $stmt = $this->db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
        return $stmt->fetch() ?: [];
    }

    public function update(array $data): bool {
        $sql = "UPDATE settings SET 
                username = :username,
                allowed_extensions = :allowed_extensions,
                max_file_size = :max_file_size,
                updated_at = NOW()";

        $params = [
            ':username' => $data['username'],
            ':allowed_extensions' => $data['allowed_extensions'],
            ':max_file_size' => (int)($data['max_file_size'] * 1024 * 1024), // MB to bytes
            ':id' => $data['id']
        ];

        if (!empty($data['password'])) {
            $sql .= ", password = :password";
            $params[':password'] = md5($data['password']);
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function authenticate(string $username, string $password): bool {
        $stmt = $this->db->prepare("SELECT * FROM settings WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            error_log("User not found: " . $username);
            return false;
        }

        // MD5 ile şifre kontrolü
        return $user['password'] === md5($password);
    }
} 