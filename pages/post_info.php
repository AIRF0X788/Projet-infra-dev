<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "infra/dev";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

// Récupérez l'ID du poste à partir des paramètres de l'URL
if (isset($_GET['id'])) {
    $post_id = $_GET['id'];

    // Requête pour récupérer les détails du poste en fonction de son ID
    $sql = "SELECT * FROM publications WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<div>";
        echo "<p>Type de publication: " . $row['type_publication'] . "</p>";
        echo "<p>Contenu: " . $row['contenu'] . "</p>";
        if ($row['type_publication'] === 'prod' && !empty($row['lien_audio'])) {
            echo "<audio controls>";
            echo "<source src='" . $row['lien_audio'] . "' type='audio/mpeg'>";
            echo "Votre navigateur ne prend pas en charge l'élément audio.";
            echo "</audio>";
        }
        echo "</div>";
    } else {
        echo "Aucun poste trouvé avec cet ID.";
    }
} else {
    echo "ID de poste non spécifié.";
}

$conn->close();
?>
