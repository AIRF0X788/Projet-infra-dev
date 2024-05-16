<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "infra/dev";

$conn = new mysqli($servername, $username, $password, $database);

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour ajouter un commentaire.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $publication_id = $_POST['publication_id'];
    $user_id = $_SESSION['user_id'];
    $commentaire = $_POST['commentaire'];

    $sql = "INSERT INTO commentaires (publication_id, user_id, commentaire, date_commentaire) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $publication_id, $user_id, $commentaire);
    if ($stmt->execute()) {
        header("Location: post_info.php?id=" . $publication_id);
        exit();
    } else {
        echo "Erreur lors de l'ajout du commentaire: " . $stmt->error;
    }
}

$conn->close();
?>
