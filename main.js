document.addEventListener('DOMContentLoaded', function() {
    // Animation du carousel
    const carousel = document.querySelector('.carousel');
    const images = document.querySelectorAll('.carousel img');
    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');
    const indicators = document.querySelectorAll('.indicator');
    
    let currentIndex = 0;
    const totalImages = images.length;
    let intervalId;
    
    function moveToIndex(index) {
        if (index < 0) index = totalImages - 1;
        if (index >= totalImages) index = 0;
        
        currentIndex = index;
        carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
        
        indicators.forEach((indicator, i) => {
            indicator.classList.toggle('active', i === currentIndex);
        });
    }
    
    function startAutoSlide() {
        intervalId = setInterval(() => {
            moveToIndex(currentIndex + 1);
        }, 5000);
    }
    
    // Initialisation
    moveToIndex(0);
    startAutoSlide();
    
    // Événements
    prevBtn.addEventListener('click', () => {
        clearInterval(intervalId);
        moveToIndex(currentIndex - 1);
        startAutoSlide();
    });
    
    nextBtn.addEventListener('click', () => {
        clearInterval(intervalId);
        moveToIndex(currentIndex + 1);
        startAutoSlide();
    });
    
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            clearInterval(intervalId);
            moveToIndex(index);
            startAutoSlide();
        });
    });
    
    carousel.addEventListener('mouseenter', () => clearInterval(intervalId));
    carousel.addEventListener('mouseleave', startAutoSlide);

    // Autres animations...
});




document.addEventListener('DOMContentLoaded', function() {
    const chatbotToggle = document.getElementById('chatbotToggle');
    const chatbotWindow = document.getElementById('chatbotWindow');
    const closeChat = document.querySelector('.close-chat');
    const sendMessageBtn = document.getElementById('sendMessage');
    const userMessageInput = document.getElementById('userMessage');
    const chatbotMessages = document.getElementById('chatbotMessages');
    
    // Ouvrir/fermer le chat
    chatbotToggle.addEventListener('click', function() {
        chatbotWindow.classList.toggle('active');
    });
    
    closeChat.addEventListener('click', function() {
        chatbotWindow.classList.remove('active');
    });
    
    // Envoyer un message
    function sendMessage() {
        const message = userMessageInput.value.trim();
        if (message === '') return;
        
        // Ajouter le message de l'utilisateur
        addMessage(message, 'user');
        userMessageInput.value = '';
        
        // Réponse de l'IA (simulée)
        setTimeout(() => {
            const responses = [
                "Je comprends votre préoccupation. Pouvez-vous me donner plus de détails ?",
                "Je vais vous aider avec cela. Un instant...",
                "C'est une question intéressante. Voici ce que je peux vous dire...",
                "Pour cette question, je vous recommande de consulter un spécialiste."
            ];
            const randomResponse = responses[Math.floor(Math.random() * responses.length)];
            addMessage(randomResponse, 'bot');
        }, 1000);
    }
    
    // Appuyer sur Entrée pour envoyer
    userMessageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    
    sendMessageBtn.addEventListener('click', sendMessage);
    
    // Ajouter un message au chat
    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', `${sender}-message`);
        messageDiv.innerHTML = `<p>${text}</p>`;
        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }
});