<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour effectuer cette action.");
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "infra/dev";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT * FROM publications WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $delete_sql = "DELETE FROM publications WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $post_id);
        if ($delete_stmt->execute()) {
            header("Location: main.php");
        } else {
            echo "Erreur lors de la suppression du post : " . $conn->error;
        }
    } else {
        echo "Vous n'êtes pas autorisé à supprimer ce post.";
    }
} else {
    echo "Requête invalide.";
}

$conn->close();
?>
