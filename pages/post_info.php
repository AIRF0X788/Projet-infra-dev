<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info</title>
</head>

<body>
    <a href="main.php" class="btn btn-primary">Retour</a>
</body>

</html>

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

if (isset($_GET['id'])) {
    $post_id = $_GET['id'];

    $sql = "SELECT * FROM publications WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<div>";
        echo "<p>Type de publication: " . $row['type_publication'] . "</p>";
        echo "<p>Titre: " . $row['titre'] . "</p>";
        echo "<p>Description: " . $row['description'] . "</p>";
        if ($row['payant'] === 1 && !empty($row['prix'])) {
            echo "<p>Prix: " . $row['prix'] . "</p>";
        }
        if ($row['type_publication'] === 'texte' && !empty($row['contenu_texte'])) {
            echo "<p>Texte: " . $row['contenu_texte'] . "</p>";
        }
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="container">
        <a href="process_payment.php?id=<?php echo $post_id; ?>&prix=<?php echo $row['prix']; ?>" class="btn btn-primary">Payer</a>
    </div>
</body>

</html>