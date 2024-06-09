<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type_publication = $_POST['type_publication'];
    $genre_musical = $_POST['genre_musical'];
    $titre = null;
    $description = null;

    if ($type_publication === 'texte') {
        $titre = isset($_POST['titre_texte']) ? $_POST['titre_texte'] : null;
        $description = isset($_POST['description_texte']) ? $_POST['description_texte'] : null;
    } elseif ($type_publication === 'prod') {
        $titre = isset($_POST['titre_prod']) ? $_POST['titre_prod'] : null;
        $description = isset($_POST['description_prod']) ? $_POST['description_prod'] : null;
    }

    $date_publication = date('Y-m-d H:i:s');

    if ($type_publication === 'texte') {
        if (empty($_POST['contenu_texte'])) {
            echo "Le champ contenu est requis pour une publication de type texte.";
            exit();
        }
        $contenu = $_POST['contenu_texte'];
        $audio_data = null;
        $audio_type = null;
    } elseif ($type_publication === 'prod') {
        if ($_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
            echo "Veuillez sélectionner un fichier audio.";
            exit();
        }

        $audio_data = file_get_contents($_FILES['audio']['tmp_name']);
        $audio_type = $_FILES['audio']['type'];

        $contenu = null;
    } else {
        echo "Type de publication non valide.";
        exit();
    }

    $servername = "localhost";
    $username = "root";
    $password = "root";
    $database = "infra/dev";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

    $user_id = $_SESSION['user_id'];
    $prix = null; 

    if (isset($_POST['payant']) && $_POST['payant'] == 'on') {
        $prix = isset($_POST['prix']) ? $_POST['prix'] : null;
    }

    $sql = "INSERT INTO publications (user_id, type_publication, genre_musical, titre, description, contenu_texte, audio_data, audio_type, prix, date_publication) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssss", $user_id, $type_publication, $genre_musical, $titre, $description, $contenu, $audio_data, $audio_type, $prix, $date_publication);
    $stmt->execute();

    if (!empty($prix)) {
        $publication_id = $stmt->insert_id;
        $sql_update_payant = "UPDATE publications SET payant = 1 WHERE id = ?";
        $stmt_update_payant = $conn->prepare($sql_update_payant);
        $stmt_update_payant->bind_param("i", $publication_id);
        $stmt_update_payant->execute();
    }    

    $stmt->close();
    $conn->close();

    header("Location: main.php");
    exit();
}
?>
