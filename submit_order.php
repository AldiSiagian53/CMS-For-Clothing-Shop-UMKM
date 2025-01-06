<?php
header('Content-Type: application/json'); // Set JSON response header

// Database configuration
$servername = "localhost";
$username = "advcshop";
$password = "osttamvan123";
$dbname = "advcshop_order";

// Error handling function
function sendError($message, $debug = null) {
    $response = [
        "success" => false,
        "message" => $message
    ];
    if ($debug !== null) {
        $response["debug"] = $debug;
    }
    echo json_encode($response);
    exit;
}

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    sendError("Database connection failed: " . $conn->connect_error);
}

// Check POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and validate required fields
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $total = $_POST['total'] ?? '0';

    // Format and validate price
    $price = str_replace(['Rp', '.', ' '], '', $total); // Remove "Rp" and thousand separators
    $price = str_replace(',', '.', $price); // Convert comma to decimal point
    if (!is_numeric($price)) {
        sendError("Invalid price format", ["received_price" => $price]);
    }
    $price = number_format((float)$price, 2, '.', ''); // Format to 2 decimal places

    // Additional fields
    $type = trim($_POST['type'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $date = date('Y-m-d');
    $status = 'In Progress';

    // Debugging
    error_log("Processed price: " . $price);

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO orders (name, address, date, price, status, type, size) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        sendError("Database prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sssdsss", 
        $name,
        $address,
        $date,
        $price,  // Ensure price is passed as double
        $status,
        $type,
        $size
    );

    // Execute the statement
    if ($stmt->execute()) {
        $orderId = $stmt->insert_id;
        echo json_encode([
            "success" => true,
            "message" => "Order created successfully",
            "data" => [
                "order_id" => $orderId,
                "name" => $name,
                "address" => $address,
                "price" => $price
            ]
        ]);
    } else {
        sendError("Failed to create order", ["sql_error" => $stmt->error]);
    }

    $stmt->close();
} else {
    sendError("Invalid request method. POST required.");
}

// Close connection
$conn->close();
