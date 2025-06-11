<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) exit;

$sender_id = $_SESSION["user_id"];
$receiver_id = $_POST["receiver_id"];
$message = trim($_POST["message"]);

if ($receiver_id && $message) {
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$sender_id, $receiver_id, $message]);
}
?>
