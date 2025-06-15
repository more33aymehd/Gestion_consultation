<?php
session_start();
require 'db.php';

if (!isset($_SESSION["id_patient"])) {
    header("Location: connexion.php");
    exit;
}

$sender_id = $_SESSION["id_patient"];
$sender_type = 'patient';

$receiver_id = $_GET["receiver_id"] ?? null;
$receiver_type = 'medecin'; // par défaut médecin

// Récupérer la liste des médecins pour le select
$medecins = $pdo->query("SELECT id_medecin, nom FROM medecins ORDER BY nom")->fetchAll();

if ($receiver_id) {
    $stmt = $pdo->prepare("SELECT nom FROM medecins WHERE id_medecin = ?");
    $stmt->execute([$receiver_id]);
    $receiver_name = $stmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>Chat Patient - Médecin</title>
<style>
  body { font-family: Arial, sans-serif; background: #f0f2f5; }
  .container { max-width: 600px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 10px; }
  .chat-box { height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; background: #fafafa; display: flex; flex-direction: column; gap: 5px; }
  .message { max-width: 80%; padding: 8px 12px; border-radius: 15px; }
  .sent { background-color: #dcf8c6; align-self: flex-end; text-align: right; }
  .received { background-color: #ffffff; align-self: flex-start; text-align: left; }
</style>
</head>
<body>

<div class="container">
  <h2>Messagerie Patient - Médecin</h2>

  <label for="receiverSelect">Choisir un médecin :</label>
  <select id="receiverSelect" name="receiver_id">
    <option value="">-- Choisir --</option>
    <?php foreach ($medecins as $med): ?>
      <option value="<?= $med['id_medecin'] ?>" <?= ($med['id_medecin'] == $receiver_id) ? 'selected' : '' ?>>
        <?= htmlspecialchars($med['nom']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <?php if ($receiver_id): ?>
    <h3>Conversation avec <?= htmlspecialchars($receiver_name) ?></h3>
    <div class="chat-box" id="chatBox"></div>
    <form id="messageForm">
      <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
      <textarea name="message" id="message" placeholder="Écrire un message..." required style="width: 100%; height: 80px;"></textarea><br>
      <button type="submit">Envoyer</button>
    </form>
  <?php else: ?>
    <p>Veuillez choisir un médecin pour démarrer la conversation.</p>
  <?php endif; ?>
</div>

<script>
const receiverSelect = document.getElementById('receiverSelect');
const chatBox = document.getElementById('chatBox');
const messageForm = document.getElementById('messageForm');
const messageInput = document.getElementById('message');

receiverSelect.addEventListener('change', function() {
  const id = this.value;
  if (id) {
    window.location.href = 'chat.php?receiver_id=' + id;
  } else {
    chatBox.innerHTML = '';
    if (messageForm) messageForm.style.display = 'none';
  }
});

function loadMessages() {
  if (!<?= json_encode($receiver_id ? true : false) ?>) return;
  fetch('load_messages.php?receiver_id=<?= $receiver_id ?>&receiver_type=medecin')
    .then(response => response.text())
    .then(html => {
      chatBox.innerHTML = html;
      chatBox.scrollTop = chatBox.scrollHeight;
    });
}

if (messageForm) {
  messageForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(messageForm);
    fetch('send.php', {
      method: 'POST',
      body: formData
    }).then(() => {
      messageInput.value = '';
      loadMessages();
    });
  });

  loadMessages();
  setInterval(loadMessages, 3000);
}
</script>

</body>
</html>
