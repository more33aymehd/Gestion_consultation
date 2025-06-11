<?php
$translations = [
    'fr' => [
        'welcome' => 'Bienvenue',
        'book_appointment' => 'Prendre un rendez-vous',
        'view_records' => 'Voir mon dossier médical',
        'logout' => 'Déconnexion',
        'register' => 'Inscription',
        'login' => 'Connexion',
        'dashboard' => 'Tableau de bord',
        'our_services' => 'Nos services',
        'start_now' => 'Commencer maintenant',
    ],
    'en' => [
        'welcome' => 'Welcome',
        'book_appointment' => 'Book an Appointment',
        'view_records' => 'View My Medical Records',
        'logout' => 'Logout',
        'register' => 'Register',
        'login' => 'Login',
        'dashboard' => 'Dashboard',
        'our_services' => 'Our Services',
        'start_now' => 'Start Now',
    ],
    'sw' => [
        'welcome' => 'Karibu',
        'book_appointment' => 'Weka Muda wa Kukutana',
        'view_records' => 'Angalia Rekodi Zangu za Matibabu',
        'logout' => 'Toka',
        'register' => 'Jisajili',
        'login' => 'Ingia',
        'dashboard' => 'Dashibodi',
        'our_services' => 'Huduma Zetu',
        'start_now' => 'Anza Sasa',
    ],
];

function getTranslation($key, $lang = 'fr') {
    global $translations;
    return isset($translations[$lang][$key]) ? $translations[$lang][$key] : $key;
}
?>