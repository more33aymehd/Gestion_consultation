<?php
session_start();
require 'db.php';

if (!isset($_SESSION["id_patient"])) exit;

$sender_id = $_SESSION["id_patient"];
$sender_type = 'patient';

$receiver_id = $_POST["receiver_id"] ?? null;
$receiver_type = 'medecin';

$message = trim($_POST["message"] ?? '');

if ($receiver_id && $message !== '') {
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, sender_type, receiver_id, receiver_type, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$sender_id, $sender_type, $receiver_id, $receiver_type, $message]);
}
