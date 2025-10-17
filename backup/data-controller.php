<?php
// API URL
$apiUrl = "https://apploqic.my/index.php?endpoint=business";

// Fetch data from API
$response = @file_get_contents($apiUrl);

// Decode JSON response
$json = json_decode($response, true);

// Prepare $businesses array to be used in the main page
$businesses = [];

if ($json && isset($json['data']) && is_array($json['data'])) {
    foreach ($json['data'] as $item) {
        $businesses[] = [
            'id'      => $item['id'] ?? 0,
            'name'    => $item['business_name'] ?? 'Unknown Business',
            'image'   => $item['business_img_url'] ?? 'Unknown Image',
            'contact' => $item['business_contact'] ?? 'Unknown Contact',
        ];
    }
}
?>
