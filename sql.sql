CREATE TABLE IF NOT EXISTS utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom_utilisateur VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    statut VARCHAR(10) NOT NULL DEFAULT 'inactif',
    activation_token VARCHAR(255) DEFAULT NULL,
    est_admin BOOLEAN DEFAULT 0,
    profile_picture LONGBLOB NOT NULL,
    ban BOOL NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

INSERT INTO categories (name) VALUES ('Beatmaker'), ('Ghostwriter'), ('Chanteur'), ('Producteur');

CREATE TABLE IF NOT EXISTS user_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    category_id INT,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS publications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type_publication ENUM('texte', 'prod') NOT NULL,
    titre VARCHAR(255),
    description TEXT,
    payant BOOLEAN DEFAULT 0,
    prix DECIMAL(10,2) DEFAULT 0.00,
    contenu_texte TEXT,
    audio_data LONGBLOB,
    audio_type VARCHAR(255),
    date_publication DATETIME,
    likes_count INT DEFAULT 0,
    genre_musical VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS achats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    prix DECIMAL(10,2),
    date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_publication INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur),
    FOREIGN KEY (id_publication) REFERENCES publications(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS commentaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    publication_id INT NOT NULL,
    user_id INT NOT NULL,
    commentaire TEXT,
    date_commentaire DATETIME,
    FOREIGN KEY (publication_id) REFERENCES publications(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS demandes_contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    sujet VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);