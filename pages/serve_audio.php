<?php
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $servername = "localhost";
    $username = "root";
    $password = "root";
    $database = "infra/dev";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

    $sql = "SELECT audio_data, audio_type FROM publications WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($audio_data, $audio_type);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        header("Content-Type: " . $audio_type);
        echo $audio_data;
    } else {
        echo "Fichier audio introuvable.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "ID de publication non spécifié.";
}
?>
