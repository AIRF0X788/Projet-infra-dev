<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type_publication = $_POST['type_publication'];

    $contenu = '';
    $lien_audio = null;

    if ($type_publication === 'texte') {
        if (isset($_POST['contenu_texte']) && !empty($_POST['contenu_texte'])) {
            $contenu = $_POST['contenu_texte'];
        } else {
            echo "Le champ contenu est requis pour une publication de type texte.";
            exit();
        }
    } elseif ($type_publication === 'prod') {
        if (isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = "../audio/";
            $file_name = uniqid() . '_' . basename($_FILES['audio']['name']);
            $target_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['audio']['tmp_name'], $target_path)) {
                $lien_audio = $target_path;
            } else {
                echo "Une erreur s'est produite lors du téléchargement du fichier audio.";
                exit();
            }
        } else {
            echo "Veuillez sélectionner un fichier audio.";
            exit();
        }
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

    $sql = "INSERT INTO publications (user_id, type_publication, contenu, lien_audio) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $type_publication, $contenu, $lien_audio);
    $stmt->execute();

    $conn->close();

    header("Location: main.php");
    exit();
}
