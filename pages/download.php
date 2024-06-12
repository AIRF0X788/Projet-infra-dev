<?php
session_start();

$conn = new mysqli("localhost", "root", "root", "infra/dev");

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['type']) && isset($_GET['title'])) {
    $type = $_GET['type'];
    $title = urldecode($_GET['title']);

    if ($type === 'text' && isset($_GET['content'])) {
        $content = urldecode($_GET['content']);
        
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $title . '.txt"');
        
        echo $content;
        exit;
    } elseif ($type === 'prod') {
        $sql = "SELECT audio_data FROM publications WHERE titre = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $title);
        $stmt->execute();
        $stmt->bind_result($audio_data);
        $stmt->fetch();
        $stmt->close();

        if ($audio_data) {
            header('Content-Description: File Transfer');
            header('Content-Type: audio/mpeg');
            header('Content-Disposition: attachment; filename="' . $title . '.mp3"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . strlen($audio_data));
            
            echo $audio_data;
            exit;
        } else {
            echo "Erreur: Audio non trouvé.";
        }
    }
}

header("Location: main.php");
exit;