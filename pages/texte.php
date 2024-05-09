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

$sql = "SELECT * FROM publications WHERE type_publication = 'texte'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<p>Type de publication: " . $row['type_publication'] . "</p>";
        echo "<p>Contenu: " . $row['contenu'] . "</p>";
        echo "</div>";
    }
} else {
    echo "Aucune publication de type 'texte' n'a été trouvée.";
}

$conn->close();
?>
