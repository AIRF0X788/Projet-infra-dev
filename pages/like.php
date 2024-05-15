<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour liker une publication.");
}

if (isset($_POST['publication_id'])) {
    $user_id = $_SESSION['user_id'];
    $publication_id = $_POST['publication_id'];

    if (!isset($_COOKIE['liked_' . $publication_id]) || $_COOKIE['liked_' . $publication_id] != 'true') {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "infra/dev";

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
            die("La connexion à la base de données a échoué : " . $conn->connect_error);
        }

        $update_likes_sql = "UPDATE publications SET likes_count = likes_count + 1 WHERE id = ?";
        $update_likes_stmt = $conn->prepare($update_likes_sql);
        $update_likes_stmt->bind_param("i", $publication_id);
        $update_likes_stmt->execute();
        $update_likes_stmt->close();

        $conn->close();

        setcookie('liked_' . $publication_id, 'true', time() + (10 * 365 * 24 * 60 * 60));
    }

    header("Location: post_info.php?id=" . $publication_id);
    exit;
} else {
    die("ID de publication non spécifié.");
}
?>
