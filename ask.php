<?php

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$question = $data["question"] ?? "";

if (!$question) {
    echo json_encode(["error" => "No question"]);
    exit;
}

// Optional: restrict to website content
$prompt = "Answer only using our website information. 
If unrelated, say: 'This question is not related to our website.'

Question: " . $question;

$postData = json_encode([
    "model" => "cpp-website-assistant",
    "prompt" => $prompt,
    "stream" => false
]);

$ch = curl_init("http://localhost:11434/api/generate");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["error" => curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

echo $response;