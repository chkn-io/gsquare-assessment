<?php
$store = '';
$accessToken = '';
$productId = ''; // Replace with your product ID
$newValue = $_GET['value'] ?? 'abc';

// Step 1: Get all metafields for the product
$ch = curl_init("https://$store/admin/api/2024-04/products/$productId/metafields.json");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Shopify-Access-Token: $accessToken"
]);
$response = curl_exec($ch);
curl_close($ch);

$metafields = json_decode($response, true);

if (!$metafields || !isset($metafields['metafields'])) {
    die("❌ Could not fetch metafields.\n");
}

// Step 2: Find the warehouse_location metafield
$target = null;
foreach ($metafields['metafields'] as $meta) {
    if ($meta['namespace'] === 'custom' && $meta['key'] === 'warehouse_location') {
        $target = $meta;
        break;
    }
}

if (!$target) {
    die("❌ warehouse_location metafield not found.\n");
}

$metafieldId = $target['id'];

// Step 3: Update the value
$updateData = [
    'metafield' => [
        'id' => $metafieldId,
        'value' => $newValue,
        'type' => 'single_line_text_field'
    ]
];

$ch = curl_init("https://$store/admin/api/2024-04/metafields/$metafieldId.json");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Shopify-Access-Token: $accessToken",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));
$updateResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "✅ HTTP $httpCode - warehouse_location updated to '$newValue'.\n";