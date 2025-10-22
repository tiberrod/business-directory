<?php
$url = "https://apploqic.my/index.php?endpoint=business";

$data = [
  "name" => "Testing Business",
  "contact" => "testing@example.com",
  "description" => "This is a test description."
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

echo $response;