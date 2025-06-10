CREATE DATABASE IF NOT EXISTS gestion_sante;
USE gestion_sante;

CREATE TABLE patients (
    id_patient INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    age INT CHECK (age >= 0),
    sexe ENUM('M', 'F') NOT NULL,
    adresse TEXT,
    email VARCHAR(150) UNIQUE,
    telephone VARCHAR(20),
    maladie TEXT,
    statut ENUM('actif', 'inactif', 'décédé') DEFAULT 'actif',
    photo VARCHAR(255),
    mot_de_passe VARCHAR(255) NOT NULL
);

CREATE TABLE admins (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL
);

CREATE TABLE medecins (
    id_medecin INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    adresse TEXT,
    telephone VARCHAR(20),
    specialite VARCHAR(100),
    affiliation VARCHAR(150),
    photo VARCHAR(255),
    mot_de_passe VARCHAR(255) NOT NULL,
    tarif DECIMAL(10,2)
);

CREATE TABLE pharmacies (
    id_pharmacy INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    adresse TEXT,
    telephone VARCHAR(20)
);

CREATE TABLE hopitaux (
    id_hopital INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    adresse TEXT,
    telephone VARCHAR(20)
);

CREATE TABLE medicaments (
    id_medicament INT AUTO_INCREMENT PRIMARY KEY,
    id_pharmacy INT,
    nom VARCHAR(150) NOT NULL,
    quantite INT DEFAULT 0,
    prix DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (id_pharmacy) REFERENCES pharmacies(id_pharmacy) ON DELETE SET NULL
);

CREATE TABLE messages (
    id_message INT AUTO_INCREMENT PRIMARY KEY,
    id_patient INT,
    id_medicament INT,
    contenu TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('envoyé', 'lu', 'supprimé') DEFAULT 'envoyé',
    type_expediteur ENUM('patient', 'medecin', 'admin'),
    type_destinataire ENUM('patient', 'medecin', 'admin'),
    FOREIGN KEY (id_patient) REFERENCES patients(id_patient) ON DELETE CASCADE,
    FOREIGN KEY (id_medicament) REFERENCES medicaments(id_medicament) ON DELETE SET NULL
);

CREATE TABLE consultations (
    id_consultation INT AUTO_INCREMENT PRIMARY KEY,
    id_patient INT,
    id_medecin INT,
    contenu TEXT,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    prix DECIMAL(10,2),
    FOREIGN KEY (id_patient) REFERENCES patients(id_patient) ON DELETE CASCADE,
    FOREIGN KEY (id_medecin) REFERENCES medecins(id_medecin) ON DELETE CASCADE
);

CREATE TABLE commandes (
    id_commande INT AUTO_INCREMENT PRIMARY KEY,
    id_patient INT,
    id_medicament INT,
    id_pharmacy INT,
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_patient) REFERENCES patients(id_patient) ON DELETE CASCADE,
    FOREIGN KEY (id_medicament) REFERENCES medicaments(id_medicament) ON DELETE SET NULL,
    FOREIGN KEY (id_pharmacy) REFERENCES pharmacies(id_pharmacy) ON DELETE SET NULL
);

CREATE TABLE groupes (
    id_groupe INT AUTO_INCREMENT PRIMARY KEY,
    nom_groupe VARCHAR(100) NOT NULL,
    type VARCHAR(50),
    description TEXT,
    id_medecin INT,
    photo VARCHAR(255),
    FOREIGN KEY (id_medecin) REFERENCES medecins(id_medecin) ON DELETE SET NULL
);

CREATE TABLE membres_groupe (
    id_groupe INT,
    id_patient INT,
    PRIMARY KEY (id_groupe, id_patient),
    FOREIGN KEY (id_groupe) REFERENCES groupes(id_groupe) ON DELETE CASCADE,
    FOREIGN KEY (id_patient) REFERENCES patients(id_patient) ON DELETE CASCADE
);