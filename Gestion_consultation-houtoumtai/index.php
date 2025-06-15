<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services de santé</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="animation.css">
</head>
<body>
    <header class="header">
        <div class="logo">ON A PAS ENCORE EU DE NOM POUR LE SITE😐</div>
        <nav class="nav-links">
            <a href="#" class="nav-link">Inscription</a>
            <a href="#" class="nav-link">Connexion</a>
        </nav>
    </header>

    <div class="demi-page">
        <div class="hero-image"></div>
        <h1>Votre santé, notre priorité : une gestion simplifiée pour un suivi efficace.</h1>
        
        <div class="sections-container">
            <div class="service-card">
                <i class="fas fa-user-md card-icon"></i>
                <div class="card-content">
                    <span>Consulter un médecin</span>
                    <button>Consultation</button>
                </div>
            </div>

            <div class="service-card">
                <i class="fas fa-users card-icon"></i>
                <div class="card-content">
                    <span>Rejoindre un groupe</span>
                    <button>Explorer</button>
                </div>
            </div>

            <div class="service-card">
                <i class="fas fa-exclamation-triangle card-icon"></i>
                <div class="card-content">
                    <span>Signaler une urgence</span>
                    <button>Alerter</button>
                </div>
            </div>
        </div>
    </div>

   <div class="info-section">
        <img src="images/background-1.jpeg" alt="Consultation" class="consult-image">
        <div class="description">
            <h2>À propos de notre service</h2>
            <p>Nous offrons une plateforme complète pour gérer votre santé. Prenez rendez-vous avec des professionnels, rejoignez des groupes de soutien, et accédez à toutes les ressources nécessaires pour une gestion efficace de votre bien-être.</p>
            <a href=""><span class="consultaion-descrip">Consulter un médecin</span></a>
        </div>
    </div>

    <div class="second-info-section">
        <div class="description">
            <h2>Rejoindre notre communauté</h2>
            <p>Participez à nos groupes de consultation pour partager vos expériences et obtenir le soutien dont vous avez besoin. Ensemble, nous pouvons améliorer votre bien-être.</p>
            <a href=""><span class="rejoindre_groupe">Rejoindre un groupe</span></a>
        </div>
        <img src="images/groupe_consul_1.jpg" alt="Groupe Consultation" class="group-image">
    </div>

    <div class="third-info-section">
        <img src="images/alerte_1.jpg" alt="Alerte Médicale" class="alert-image">
        <div class="description">
            <h2>Alerte Médicale</h2>
            <p>Recevez des alertes médicales importantes et restez informé sur votre santé. Nous nous engageons à vous fournir les meilleures ressources pour votre bien-être.</p>
            <a href=""><span class="alert">Alerté un hôpital</span></a>
        </div>
    </div>

    <!-- Carousel après le 3ème div -->
    <div class="carousel-container">
        <div class="carousel">
            <img src="images/background-1.jpeg" alt="Image 1">
            <img src="images/groupe_consul_1.jpg" alt="Image 2">
            <img src="images/alerte_1.jpg" alt="Image 3">
        </div>
        <div class="carousel-controls">
            <button class="carousel-control prev">&lt;</button>
            <button class="carousel-control next">&gt;</button>
        </div>
        <div class="carousel-indicators">
            <div class="indicator active"></div>
            <div class="indicator"></div>
            <div class="indicator"></div>
        </div>
    </div>
<footer class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h3>Services</h3>
            <ul>
                <li><a href="#">Consultations</a></li>
                <li><a href="#">Groupes de soutien</a></li>
                <li><a href="#">Urgences</a></li>
                <li><a href="#">Télé-médecine</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h3>À propos</h3>
            <ul>
                <li><a href="#">Notre équipe</a></li>
                <li><a href="#">Missions</a></li>
                <li><a href="#">Partenaires</a></li>
                <li><a href="#">Carrières</a></li>
            </ul>
        </div>
        
        
        
        <div class="footer-section">
            <h3>Contact</h3>
            <p><i class="fas fa-map-marker-alt"></i>Yaoundé, Titi Garage</p>
            <p><i class="fas fa-phone"></i>+237 692 063 014</p>
            <p><i class="fas fa-envelope"></i>houtoumtaiboukarbertrand@gmail.com</p>
            
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; 2023 SantéService. Tous droits réservés.</p>
    </div>
</footer>

    <script src="main.js"></script>
    <!-- Chatbot IA -->
<div class="chatbot-container">
    <div class="chatbot-icon" id="chatbotToggle">
        <i class="fas fa-robot"></i>
    </div>
    
    <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-header">
            <h3>Assistante Santé IA</h3>
            <span class="close-chat"><i class="fas fa-times"></i></span>
        </div>
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="message bot-message">
                <p>Bonjour ! Je suis votre assistante santé. Comment puis-je vous aider aujourd'hui ?</p>
            </div>
        </div>
        <div class="chatbot-input">
            <input type="text" id="userMessage" placeholder="Tapez votre message...">
            <button id="sendMessage"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>
</body>
</html>