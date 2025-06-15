<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$receiver_id = $_GET["receiver_id"] ?? null;

$users = $pdo->prepare("SELECT id, username FROM users WHERE id != ?");
$users->execute([$user_id]);
$all_users = $users->fetchAll();

if ($receiver_id) {
    $contact = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $contact->execute([$receiver_id]);
    $contact_name = $contact->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Chat Privé</title>
  <style>
    body { font-family: Arial; background: #f0f2f5; }
    .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
    .chat-box { height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; background: #fafafa; }
    .message { margin: 5px 0; padding: 8px 12px; border-radius: 15px; max-width: 80%; }
    .sent { background-color: #dcf8c6; align-self: flex-end; text-align: right; }
    .received { background-color: #ffffff; align-self: flex-start; text-align: left; }
    .chat-wrapper { display: flex; flex-direction: column; gap: 5px; }
  </style>
</head>
<body>

<div class="container">
  <h2>Messagerie</h2>
  <form id="receiverForm">
    <label>Choisir un contact :</label>
    <select id="receiverSelect" name="receiver_id">
      <option value="">-- Choisir --</option>
      <?php foreach ($all_users as $u): ?>
        <option value="<?= $u['id'] ?>" <?= $u['id'] == $receiver_id ? 'selected' : '' ?>><?= htmlspecialchars($u['username']) ?></option>
      <?php endforeach; ?>
    </select>
  </form>

  <?php if ($receiver_id): ?>
    <h3>Conversation avec <?= htmlspecialchars($contact_name) ?></h3>
    <div class="chat-box" id="chatBox"></div>
    <form id="messageForm">
      <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
      <textarea name="message" id="message" placeholder="Ton message..." required style="width:100%;"></textarea><br>
      <button type="submit">Envoyer</button>
    </form>
  <?php else: ?>
    <p>Sélectionne un contact pour démarrer une discussion.</p>
  <?php endif; ?>
</div>

<script>
const chatBox = document.getElementById("chatBox");
const messageForm = document.getElementById("messageForm");
const messageInput = document.getElementById("message");
const receiverSelect = document.getElementById("receiverSelect");

receiverSelect.addEventListener("change", function () {
  const id = this.value;
  if (id) window.location.href = "chat.php?receiver_id=" + id;
});

function loadMessages() {
  fetch("load_messages.php?receiver_id=<?= $receiver_id ?>")
    .then(res => res.text())
    .then(html => {
      chatBox.innerHTML = html;
      chatBox.scrollTop = chatBox.scrollHeight;
    });
}

if (messageForm) {
  messageForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(messageForm);
    fetch("send.php", {
      method: "POST",
      body: formData
    }).then(() => {
      messageInput.value = "";
      loadMessages();
    });
  });

  loadMessages();
  setInterval(loadMessages, 3000);
}
</script>
</body>
</html>
