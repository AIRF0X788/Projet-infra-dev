<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type_publication = $_POST['type_publication'];
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
        $lien_audio = null;
    } elseif ($type_publication === 'prod') {
        if ($_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
            echo "Veuillez sélectionner un fichier audio.";
            exit();
        }
        $upload_dir = "../audio/";
        $file_name = uniqid() . '_' . basename($_FILES['audio']['name']);
        $target_path = $upload_dir . $file_name;

        if (!move_uploaded_file($_FILES['audio']['tmp_name'], $target_path)) {
            echo "Une erreur s'est produite lors du téléchargement du fichier audio.";
            exit();
        }
        $contenu = null;
        $lien_audio = $target_path;
    } else {
        echo "Type de publication non valide.";
        exit();
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "infra/dev";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

    $user_id = $_SESSION['user_id'];

    $prix = isset($_POST['prix']) ? $_POST['prix'] : null;

    $sql = "INSERT INTO publications (user_id, type_publication, titre, description, contenu_texte, lien_audio, prix, date_publication) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssis", $user_id, $type_publication, $titre, $description, $contenu, $lien_audio, $prix, $date_publication);
    $stmt->execute();

    if (!empty($prix)) {
        $publication_id = $stmt->insert_id;
        $sql_update_payant = "UPDATE publications SET payant = 1 WHERE id = ?";
        $stmt_update_payant = $conn->prepare($sql_update_payant);
        $stmt_update_payant->bind_param("i", $publication_id);
        $stmt_update_payant->execute();
    }    

    $conn->close();

    header("Location: main.php");
    exit();
}
?>
<!--  -->