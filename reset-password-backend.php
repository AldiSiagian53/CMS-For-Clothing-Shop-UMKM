<?php
session_start();
require_once 'koneksi.php';

class PasswordReset {
    private $conn;
    private $userId;
    
    public function __construct($connection) {
        $this->conn = $connection;
        $this->userId = $_SESSION['user_id'] ?? null;
    }
    
    private function validateRequest() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            throw new Exception("Invalid request method");
        }
        
        if (!$this->userId) {
            throw new Exception("User not authenticated");
        }
    }
    
    private function validateInputs($oldPassword, $newPassword, $confirmPassword) {
        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            throw new Exception("All fields are required");
        }
        
        if (strlen($newPassword) < 6) {
            throw new Exception("New password must be at least 6 characters long");
        }
        
        if (strpos($newPassword, ' ') !== false) {
            throw new Exception("Password cannot contain spaces");
        }
        
        if (!preg_match('/[A-Z]/', $newPassword)) {
            throw new Exception("Password must contain at least one uppercase letter");
        }
        
        if (!preg_match('/[0-9]/', $newPassword)) {
            throw new Exception("Password must contain at least one number");
        }
        
        if ($newPassword !== $confirmPassword) {
            throw new Exception("New password and confirmation do not match");
        }
    }
    
    private function verifyCurrentPassword($oldPassword) {
        $stmt = $this->conn->prepare("SELECT password FROM pembeli WHERE id = ? LIMIT 1");
        if (!$stmt) {
            throw new Exception("Database error");
        }
        
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if (!$user || !password_verify($oldPassword, $user['password'])) {
            throw new Exception("Current password is incorrect");
        }
    }
    
    private function updatePassword($newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => 12]);
        
        $stmt = $this->conn->prepare("UPDATE pembeli SET password = ?, updated_at = NOW() WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Database error");
        }
        
        $stmt->bind_param("si", $hashedPassword, $this->userId);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update password");
        }
        
        $stmt->close();
    }
    
    private function logPasswordChange() {
        $stmt = $this->conn->prepare("INSERT INTO password_change_log (user_id, changed_at, ip_address) VALUES (?, NOW(), ?)");
        if ($stmt) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $stmt->bind_param("is", $this->userId, $ipAddress);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    public function resetPassword() {
        try {
            $this->validateRequest();
            
            $oldPassword = $_POST['old-password'] ?? '';
            $newPassword = $_POST['new-password'] ?? '';
            $confirmPassword = $_POST['confirm-password'] ?? '';
            
            $this->validateInputs($oldPassword, $newPassword, $confirmPassword);
            $this->verifyCurrentPassword($oldPassword);
            $this->updatePassword($newPassword);
            $this->logPasswordChange();
            
            $_SESSION['success'] = "Password successfully updated";
            return json_encode(['status' => 'success', 'message' => 'Password successfully updated']);
            
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            http_response_code(400);
            return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}

// Handle the request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header('Content-Type: application/json');
    $passwordReset = new PasswordReset($conn);
    echo $passwordReset->resetPassword();
    exit;
}

// Redirect to reset password page if not a POST request
header("Location: /reset-password");
exit;
