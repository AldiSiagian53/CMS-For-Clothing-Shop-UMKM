<?php
class Database {
    private $conn;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        $servername = "localhost";
        $username = "advcshop";
        $password = "osttamvan123";
        $dbname = "advcshop_product";
        
        try {
            $this->conn = new mysqli($servername, $username, $password, $dbname);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8mb4");
            
        } catch (Exception $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }
    
    public function getProducts() {
        $sql = "SELECT id, name, category, price, image, sizes, piece FROM products";
        $result = $this->conn->query($sql);
        
        if ($result === false) {
            throw new Exception("Error fetching products: " . $this->conn->error);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getOrders($filters = []) {
        $sql = "SELECT * FROM orders WHERE 1=1";
        $params = [];
        $types = "";
        
        if (!empty($filters['name'])) {
            $sql .= " AND name LIKE ?";
            $params[] = "%" . $filters['name'] . "%";
            $types .= "s";
        }
        
        if (!empty($filters['date'])) {
            $sql .= " AND date = ?";
            $params[] = $filters['date'];
            $types .= "s";
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function createOrder($name, $address, $items, $totalPrice, $status = 'pending') {
        $sql = "INSERT INTO orders (name, address, items_json, total_price, status, date) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        $itemsJson = json_encode($items);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param("sssds", $name, $address, $itemsJson, $totalPrice, $status);
        
        $result = $stmt->execute();
        
        if (!$result) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        return $result;
    }
    
    public function updateOrderStatus($orderName, $orderDate, $status) {
        $stmt = $this->conn->prepare("UPDATE orders SET status = ? WHERE name = ? AND date = ?");
        $stmt->bind_param("sss", $status, $orderName, $orderDate);
        return $stmt->execute();
    }
    
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
