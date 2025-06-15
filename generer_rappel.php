<?php
// Clé API OpenRouter (à garder secrète)
$apiKey = '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';

$data = json_decode(file_get_contents("php://input"), true);
$ordonnance = $data['ordonnance'] ?? 'Prends ton médicament.';

$payload = [
    "model" => "mistral/mistral-7b-instruct",
    "messages" => [
        ["role" => "system", "content" => "Tu es une IA qui écrit des rappels médicaux personnalisés."],
        ["role" => "user", "content" => "Rédige un message amical pour rappeler à un patient : " . $ordonnance]
    ]
];

$ch = curl_init("https://openrouter.ai/api/v1/chat/completions");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$message = $result['choices'][0]['message']['content'] ?? "Rappel générique : prends ton médicament.";

echo json_encode(["message" => $message]);
?>
