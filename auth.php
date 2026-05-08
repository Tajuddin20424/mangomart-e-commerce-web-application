<?php
// includes/auth.php
// Authentication helper functions

require_once __DIR__ . '/../config/database.php';

class Auth {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    public function register($name, $email, $password, $phone = '') {
        // Validate inputs
        if (empty($name)) return ['success' => false, 'message' => 'Name is required'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return ['success' => false, 'message' => 'Valid email is required'];
        if (strlen($password) < 6) return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        
        // Check if email exists
        $check = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
        
        if ($check->num_rows > 0) {
            $check->close();
            return ['success' => false, 'message' => 'Email already registered'];
        }
        $check->close();
        
        // Create user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $phone);
        
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $stmt->close();
            return ['success' => true, 'message' => 'Registration successful', 'user' => ['id' => $user_id, 'name' => $name, 'email' => $email]];
        }
        
        $stmt->close();
        return ['success' => false, 'message' => 'Registration failed'];
    }
    
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $stmt->close();
                return ['success' => true, 'message' => 'Login successful', 'user' => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']]];
            }
            $stmt->close();
            return ['success' => false, 'message' => 'Invalid password'];
        }
        
        $stmt->close();
        return ['success' => false, 'message' => 'Email not found'];
    }
    
    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) return null;
        
        $stmt = $this->conn->prepare("SELECT id, name, email, phone, address FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        return $user;
    }
}
?>