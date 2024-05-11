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


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Textes</title>
</head>
<body>
<a href="main.php" class="btn btn-primary">Retour</a>
<?php if ($result->num_rows > 0) : ?>
    <?php while ($row = $result->fetch_assoc()) : ?>
        <div>
            <p>Type de publication: <?php echo $row['type_publication']; ?></p>
            <p>Titre: <?php echo $row['titre']; ?></p>
            <a href='post_info.php?id=<?php echo $row['id']; ?>'>Voir plus</a>
        </div>
    <?php endwhile; ?>
<?php else : ?>
    <p>Aucune publication disponible.</p>
<?php endif; ?>
</body>
</html>