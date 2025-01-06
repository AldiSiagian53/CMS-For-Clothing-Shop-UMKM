<?php
// Nonaktifkan output error PHP standar
error_reporting(0);
ini_set('display_errors', 0);

// Pastikan semua respons dalam format JSON
header('Content-Type: application/json');

// Fungsi untuk mengirim response JSON
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

try {
    include 'db.php';

    // Pengecekan koneksi database
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle CREATE
        if (isset($_POST['action']) && $_POST['action'] === 'create') {
            $name = $_POST['name'] ?? null;
            $category = $_POST['category'] ?? null;
            $price = $_POST['price'] ?? 0;
            $sizes = $_POST['sizes'] ?? null;
            $piece = $_POST['piece'] ?? 0;

            // Validasi input
            if (!$name || !$category || !$sizes || $price <= 0 || $piece <= 0) {
                throw new Exception("Invalid input. Please fill all required fields correctly.");
            }

            // Handle Image Upload
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $image = $uploadDir . basename($_FILES['image']['name']);
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $image)) {
                    throw new Exception("Failed to upload image");
                }
            }

            // Query untuk menambahkan produk
            $sql = "INSERT INTO products (name, category, price, image, sizes, piece) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }

            $stmt->bind_param("ssdssi", $name, $category, $price, $image, $sizes, $piece);

            if (!$stmt->execute()) {
                throw new Exception("Failed to create product: " . $stmt->error);
            }

            sendJsonResponse(["message" => "Product created successfully", "id" => $conn->insert_id]);
        }
    }

    // Handle READ
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);

        if ($result === false) {
            throw new Exception("Failed to fetch products: " . $conn->error);
        }

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        sendJsonResponse($products);
    }

    // Handle UPDATE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? null;
        $category = $_POST['category'] ?? null;
        $price = $_POST['price'] ?? 0;
        $sizes = $_POST['sizes'] ?? null;
        $piece = $_POST['piece'] ?? 0;

        if (!$id || !$name || !$category || !$sizes || $price <= 0 || $piece <= 0) {
            throw new Exception("Invalid input. Please fill all required fields correctly.");
        }

        // Handle Image Update
        $imageClause = "";
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $image = $uploadDir . basename($_FILES['image']['name']);
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $image)) {
                throw new Exception("Failed to upload image");
            }
            $imageClause = ", image = ?";
        }

        // Query untuk update produk
        $sql = "UPDATE products SET name = ?, category = ?, price = ?, sizes = ?, piece = ?" . 
                ($image ? ", image = ?" : "") . 
                " WHERE id = ?";
                
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        if ($image) {
            $stmt->bind_param("ssdssii", $name, $category, $price, $sizes, $piece, $image, $id);
        } else {
            $stmt->bind_param("ssdsii", $name, $category, $price, $sizes, $piece, $id);
        }

        if (!$stmt->execute()) {
            throw new Exception("Failed to update product: " . $stmt->error);
        }

        sendJsonResponse(["message" => "Product updated successfully"]);
    }

    // Handle DELETE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'] ?? null;

        if (!$id) {
            throw new Exception("Invalid input. ID is required");
        }

        // Query untuk menghapus produk
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            throw new Exception("Failed to delete product: " . $stmt->error);
        }

        sendJsonResponse(["message" => "Product deleted successfully"]);
    }

    // Jika request tidak sesuai
    throw new Exception("Invalid request method or action");

} catch (Exception $e) {
    sendJsonResponse(["error" => $e->getMessage()], 400);
}