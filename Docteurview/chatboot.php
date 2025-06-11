<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Médical</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
        }
        .chat-container {
            margin-top: 30px;
            padding: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            display: inline-block;
        }
        input[type="text"] {
            padding: 10px;
            width: 60%;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #00BFFF;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s ease-in-out;
        }
        button:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(0, 191, 255, 0.7);
        }
        .info-text {
            font-size: 14px;
            color: #888;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <h2>Posez votre question ici...</h2>
        <form method="POST">
            <input type="text" name="user_input" placeholder="Votre question..." required>
            <button type="submit">Envoyer</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user_input = htmlspecialchars($_POST["user_input"]);
            $api_key = "VOTRE_CLE_API"; // Remplace avec ta vraie clé API
            $api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$api_key";
            
            $data = json_encode(["contents" => [["parts" => [["text" => $user_input]]]]]);
            
            $options = [
                "http" => [
                    "header"  => "Content-Type: application/json\r\n",
                    "method"  => "POST",
                    "content" => $data
                ]
            ];
            $context  = stream_context_create($options);
            $response = file_get_contents($api_url, false, $context);
            $response_data = json_decode($response, true);

            echo "<p><strong>Réponse :</strong> " . ($response_data["candidates"][0]["content"]["parts"][0]["text"] ?? "Aucune réponse disponible.") . "</p>";
        }
        ?>
    </div>

    <footer class="info-text">© 2025 - Tous droits réservés</footer>
</body>
</html>