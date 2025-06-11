<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT m.message, m.timestamp, u.username AS sender
                       FROM messages m
                       JOIN users u ON m.sender_id = u.id
                       WHERE m.receiver_id = ?
                       ORDER BY m.timestamp DESC");
$stmt->execute([$_SESSION["user_id"]]);
$messages = $stmt->fetchAll();

foreach ($messages as $msg) {
    echo "<p><strong>{$msg['sender']}</strong>: {$msg['message']}<br><small>{$msg['timestamp']}</small></p><hr>";
}
?>
