<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) exit;

$user_id = $_SESSION["user_id"];
$receiver_id = $_GET["receiver_id"] ?? null;

if ($receiver_id) {
    $stmt = $pdo->prepare("
        SELECT sender_id, message, timestamp
        FROM messages
        WHERE (sender_id = ? AND receiver_id = ?)
           OR (sender_id = ? AND receiver_id = ?)
        ORDER BY timestamp ASC
    ");
    $stmt->execute([$user_id, $receiver_id, $receiver_id, $user_id]);
    $messages = $stmt->fetchAll();

    echo '<div class="chat-wrapper">';
    foreach ($messages as $msg) {
        $class = $msg['sender_id'] == $user_id ? 'sent' : 'received';
        echo "<div class='message $class'>" . htmlspecialchars($msg['message']) . "</div>";
    }
    echo '</div>';
}
?>
