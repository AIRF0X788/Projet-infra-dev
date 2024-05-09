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

$sql = "SELECT * FROM publications WHERE type_publication = 'prod'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<p>Type de publication: " . $row['type_publication'] . "</p>";
        echo "<p>Contenu: " . $row['contenu'] . "</p>";
        if (!empty($row['lien_audio'])) {
            echo "<audio controls>";
            echo "<source src='" . $row['lien_audio'] . "' type='audio/mpeg'>";
            echo "Votre navigateur ne prend pas en charge l'élément audio.";
            echo "</audio>";
        }
        echo "</div>";
    }
} else {
    echo "Aucune publication de type 'prod' n'a été trouvée.";
}

$conn->close();
?>
