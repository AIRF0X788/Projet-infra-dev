CREATE TABLE IF NOT EXISTS utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom_utilisateur VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    statut VARCHAR(10) NOT NULL DEFAULT 'inactif',
    activation_token VARCHAR(255) DEFAULT NULL,
    est_admin BOOLEAN DEFAULT 0
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
    lien_audio VARCHAR(255),
    date_publication DATETIME
);

CREATE TABLE achats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    prix DECIMAL(10,2),
    date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur),
    id_publication INT NOT NULL,
    FOREIGN KEY (id_publication) REFERENCES publications(id)
);
