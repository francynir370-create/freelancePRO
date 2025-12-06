-- Désactiver le mode strict temporairement (facultatif, pour compatibilité)
SET sql_mode = '';

-- Suppression des tables (dans l'ordre inverse des dépendances)
DROP TABLE IF EXISTS Candidature;
DROP TABLE IF EXISTS Offre;
DROP TABLE IF EXISTS ProfilFreelance;
DROP TABLE IF EXISTS Entreprise;
DROP TABLE IF EXISTS Utilisateur;

-- Création de la base (au cas où)
CREATE DATABASE IF NOT EXISTS freelancepro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE freelancepro;

-- Table Utilisateur
CREATE TABLE Utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('freelance', 'entreprise') NOT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table ProfilFreelance
CREATE TABLE ProfilFreelance (
    id_utilisateur INT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL DEFAULT '',
    prenom VARCHAR(100) NOT NULL DEFAULT '',
    bio TEXT,
    competences JSON,
    tarif_horaire DECIMAL(10,2),
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table Entreprise
CREATE TABLE Entreprise (
    id_utilisateur INT PRIMARY KEY,
    nom_entreprise VARCHAR(255) NOT NULL DEFAULT '',
    secteur VARCHAR(100),
    logo VARCHAR(255),
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table Offre
CREATE TABLE Offre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_entreprise INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    budget_min DECIMAL(10,2),
    budget_max DECIMAL(10,2),
    duree_estimee VARCHAR(50),
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('ouvert', 'ferme', 'en_cours') DEFAULT 'ouvert',
    FOREIGN KEY (id_entreprise) REFERENCES Entreprise(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table Candidature
CREATE TABLE Candidature (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_offre INT NOT NULL,
    id_freelance INT NOT NULL,
    message_motivation TEXT,
    date_candidature DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'acceptee', 'refusee') DEFAULT 'en_attente',
    FOREIGN KEY (id_offre) REFERENCES Offre(id) ON DELETE CASCADE,
    FOREIGN KEY (id_freelance) REFERENCES ProfilFreelance(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===================================================================
-- 🧪 DONNÉES DE TEST
-- ===================================================================

-- 🔐 Mot de passe par défaut pour tous les comptes : "password"
-- (hash généré avec password_hash('password', PASSWORD_DEFAULT) en PHP)

-- =============== ENTREPRISES ===============
INSERT INTO Utilisateur (email, mot_de_passe, role) VALUES
('contact@techsolutions.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'entreprise'),
('recrutement@webcrea.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'entreprise'),
('jobs@innovdata.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'entreprise');

-- Récupérer les ID (MySQL permet de le faire ainsi pour l'insertion immédiate)
INSERT INTO Entreprise (id_utilisateur, nom_entreprise, secteur) VALUES
(1, 'TechSolutions', 'Développement Informatique'),
(2, 'WebCrea Agency', 'Design & Web'),
(3, 'InnovData', 'Data & IA');

-- =============== FREELANCES (optionnel mais utile pour tester les candidatures) ===============
INSERT INTO Utilisateur (email, mot_de_passe, role) VALUES
('marie.dupont@freelance.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'freelance'),
('thomas.lemoine@dev.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'freelance'),
('sarah.kim@design.pro', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'freelance');

INSERT INTO ProfilFreelance (id_utilisateur, nom, prenom, bio, competences, tarif_horaire) VALUES
(4, 'Dupont', 'Marie', 'Data Scientist passionnée par le machine learning et la visualisation.', '["Python", "Pandas", "Scikit-learn", "SQL", "Tableau"]', 50.00),
(5, 'Lemoine', 'Thomas', 'Développeur full-stack React/Node.js avec 5 ans d\'expérience.', '["JavaScript", "React", "Node.js", "MongoDB", "Docker"]', 60.00),
(6, 'Kim', 'Sarah', 'Designer UI/UX spécialisé dans les applications mobiles intuitives.', '["Figma", "Adobe XD", "Prototypage", "Design System", "Accessibilité"]', 45.00);

-- =============== OFFRES DISPONIBLES ===============
INSERT INTO Offre (id_entreprise, titre, description, budget_min, budget_max, duree_estimee, statut) VALUES

-- TechSolutions
(1, 'Développeur Full-Stack React/Node.js', 
 'Nous recherchons un développeur full-stack pour moderniser notre application SaaS. Stack : React, Node.js, MongoDB, Docker. Expérience avec les APIs REST et WebSockets requise.',
 2500.00, 4000.00, '3 mois (~20h/semaine)', 'ouvert'),

(1, 'Ingénieur DevOps AWS',
 'Mise en place d’une infrastructure CI/CD sur AWS pour nos microservices. Compétences requises : Terraform, Kubernetes, Jenkins, monitoring (Prometheus/Grafana).',
 3000.00, 5000.00, '2 mois (mission ponctuelle)', 'ouvert'),

-- WebCrea Agency
(2, 'Designer UI/UX pour application mobile',
 'Création de maquettes Figma pour une nouvelle app de gestion de santé. 10 écrans principaux + charte graphique. Livraison en 3 semaines.',
 1200.00, 1800.00, '3 semaines', 'ouvert'),

(2, 'Intégrateur Web (HTML/CSS/JS)',
 'Intégration responsive de maquettes Figma vers un thème WordPress. Bonne maîtrise de Sass, JavaScript moderne et accessibilité (a11y).',
 800.00, 1500.00, '4 semaines', 'ouvert'),

(2, 'Rédacteur SEO - Secteur Tech',
 'Rédaction de 15 articles SEO (1500 mots chacun) sur les sujets cloud, cybersécurité et IA. Recherche de mots-clés incluse. Style clair et professionnel.',
 900.00, 1400.00, '4 semaines', 'ouvert'),

-- InnovData
(3, 'Data Scientist - Analyse prédictive',
 'Développement d’un modèle prédictif de churn client en Python (scikit-learn, pandas). Nettoyage de données, feature engineering, et reporting via Jupyter.',
 3500.00, 5500.00, '6 semaines', 'ouvert'),

(3, 'Développeur Python - ETL Pipeline',
 'Création d’un pipeline ETL pour ingérer des données depuis des APIs vers un entrepôt Snowflake. Utilisation de Apache Airflow et tests automatisés.',
 2800.00, 4200.00, '2 mois', 'ouvert');

 -- Ajouter le rôle 'admin'
ALTER TABLE Utilisateur MODIFY role ENUM('freelance','entreprise','admin') NOT NULL;


UPDATE Utilisateur SET role = 'admin' WHERE email = 'admin@freelancepro.fr';