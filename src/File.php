<?php

namespace App;

class File {
    private \PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function upload(array $files): array {
        $uploadedFiles = [];
        $errors = [];

        // Check upload directory
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Get settings
        $settings = new Settings();
        $settingsData = $settings->get();
        $allowedExtensions = explode(',', $settingsData['allowed_extensions']);
        $maxFileSize = (int)$settingsData['max_file_size'];

        // Process files
        foreach ($files['tmp_name'] as $key => $tmpName) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                $fileName = $files['name'][$key];
                $fileSize = $files['size'][$key];
                $fileType = $files['type'][$key];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                // Check file type first
                if (!in_array($fileExtension, $allowedExtensions)) {
                    $errors[] = "File type of $fileName is not allowed. Allowed types: " . implode(', ', $allowedExtensions);
                    continue;
                }

                // Then check file size
                if ($fileSize > $maxFileSize) {
                    $errors[] = "File $fileName is too large. Maximum file size: " . Helper::formatFileSize($maxFileSize);
                    continue;
                }

                // Generate unique filename
                $newFileName = uniqid() . '.' . $fileExtension;
                $filePath = $uploadDir . $newFileName;

                // Move file
                if (move_uploaded_file($tmpName, $filePath)) {
                    // Save to database
                    $stmt = $this->db->prepare("
                        INSERT INTO files (filename, original_name, file_path, file_size, file_type, created_at)
                        VALUES (?, ?, ?, ?, ?, NOW())
                    ");

                    if ($stmt->execute([$newFileName, $fileName, $filePath, $fileSize, $fileType])) {
                        $uploadedFiles[] = [
                            'id' => $this->db->lastInsertId(),
                            'filename' => $newFileName,
                            'original_name' => $fileName,
                            'file_size' => $fileSize,
                            'file_type' => $fileType
                        ];
                    } else {
                        $errors[] = "Failed to save $fileName to database.";
                        unlink($filePath);
                    }
                } else {
                    $errors[] = "Failed to upload $fileName.";
                }
            } else {
                $errors[] = "Error uploading " . $files['name'][$key] . ".";
            }
        }

        return [
            'success' => empty($errors),
            'files' => $uploadedFiles,
            'errors' => $errors
        ];
    }

    public function getAll(string $search = '', string $sort = 'created_at', string $order = 'DESC'): array {
        $sql = "SELECT * FROM files";
        $params = [];

        if (!empty($search)) {
            $sql .= " WHERE original_name LIKE ?";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY $sort $order";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM files WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function delete(int $id): bool {
        $file = $this->getById($id);
        if (!$file) {
            return false;
        }

        // Delete file from disk
        $filePath = __DIR__ . '/../uploads/' . $file['filename'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from database
        $stmt = $this->db->prepare("DELETE FROM files WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getStats(): array {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total_files,
                COALESCE(SUM(file_size), 0) as total_size
            FROM files
        ");
        return $stmt->fetch();
    }
} 