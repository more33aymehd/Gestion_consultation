<?php
session_start();
require 'db.php';

if (!isset($_SESSION["id_patient"])) exit;

$sender_id = $_SESSION["id_patient"];
$sender_type = 'patient';

$receiver_id = $_GET["receiver_id"] ?? null;
$receiver_type = $_GET["receiver_type"] ?? 'medecin';

if (!$receiver_id) exit;

$stmt = $pdo->prepare("
    SELECT *
    FROM messages
    WHERE
      (sender_id = ? AND sender_type = ? AND receiver_id = ? AND receiver_type = ?)
      OR
      (sender_id = ? AND sender_type = ? AND receiver_id = ? AND receiver_type = ?)
    ORDER BY timestamp ASC
");
$stmt->execute([
    $sender_id, $sender_type,
    $receiver_id, $receiver_type,
    $receiver_id, $receiver_type,
    $sender_id, $sender_type
]);
$messages = $stmt->fetchAll();

foreach ($messages as $msg) {
    $class = ($msg['sender_id'] == $sender_id && $msg['sender_type'] == $sender_type) ? 'sent' : 'received';
    echo "<div class='message $class'>" . htmlspecialchars($msg['message']) . "</div>";
}
