<?php
// webhook.php - handles POST requests from Shopify (orders/create)

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Optional: Verify webhook signature (recommended in production)
// file_put_contents('headers.log', json_encode(getallheaders(), JSON_PRETTY_PRINT));

if ($data) {
    $orderData = [
        'order_id' => $data['id'],
        'email' => $data['email'],
        'total_price' => $data['total_price'],
        'line_items' => array_map(function ($item) {
            return [
                'name' => $item['name'],
                'quantity' => $item['quantity']
            ];
        }, $data['line_items'])
    ];

    file_put_contents('orders_log.json', json_encode($orderData, JSON_PRETTY_PRINT));
    http_response_code(200); // Acknowledge receipt to Shopify
} else {
    http_response_code(400); // Bad Request
}
?>