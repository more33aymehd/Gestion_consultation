<?php
header('Content-Type: application/json');

$description = $_POST['description'] ?? '';

if (empty($description)) {
    echo json_encode(['error' => 'Description vide']);
    exit;
}

// 🔐 Clé API OpenRouter
$apiKey = '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'; // Remplace avec ta vraie clé API OpenRouter

// 🧠 Prompt pour guider l'IA
$prompt = "Tu es un assistant médical. À partir du texte d’un patient, indique uniquement la spécialité médicale la plus adaptée (parmi : cardiologie, dermatologie, pneumologie, neurologie, gynécologie, ophtalmologie, orthopédie, gastro-entérologie, psychiatrie, généraliste). Ne donne rien d'autre que la spécialité exacte en un mot.
Texte du patient : \"$description\"";

$data = [
    "model" => "openai/gpt-3.5-turbo", // ou autre modèle comme mistralai/mistral-7b-instruct
    "messages" => [
        ["role" => "user", "content" => $prompt]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://openrouter.ai/api/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey",
    "HTTP-Referer: http://localhost", // obligatoire pour OpenRouter
    "X-Title: Detecteur Specialite"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

$specialite = strtolower(trim($result['choices'][0]['message']['content'] ?? ''));

// Sécuriser si IA répond mal
$specialites_valides = ['cardiologie', 'dermatologie', 'pneumologie', 'neurologie', 'gynécologie', 'ophtalmologie', 'orthopédie', 'gastro-entérologie', 'psychiatrie', 'généraliste'];
if (!in_array($specialite, $specialites_valides)) {
    $specialite = ''; // invalide ou réponse floue
}

echo json_encode(['specialite' => $specialite]);
