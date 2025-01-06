<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $checkoutData = json_decode($_POST['checkoutData'] ?? '', true);

    if (empty($name) || empty($address) || empty($checkoutData)) {
        throw new Exception('Missing required fields');
    }

    $db = new Database();
    $success = $db->createOrder(
        $name,
        $address,
        $checkoutData['items'],
        $checkoutData['totalPrice']
    );

    if (!$success) {
        throw new Exception('Failed to create order');
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>