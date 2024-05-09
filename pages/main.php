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

$sql = "SELECT * FROM publications ORDER BY date_publication DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main</title>
</head>
<body>
<a href="profil.php" class="btn btn-primary">Voir mon profil</a>
<a href="post.php" class="btn btn-primary">Poster</a>
<a href="prods.php" class="btn btn-primary">Prods</a>
<a href="texte.php" class="btn btn-primary">Textes</a>

<h1>Publications</h1>
<?php if ($result->num_rows > 0) : ?>
    <?php while ($row = $result->fetch_assoc()) : ?>
        <div>
            <p>Type de publication: <?php echo $row['type_publication']; ?></p>
            <p>Contenu: <?php echo $row['contenu']; ?></p>
            <?php if ($row['type_publication'] === 'prod' && !empty($row['lien_audio'])) : ?>
                <audio controls>
                    <source src="<?php echo $row['lien_audio']; ?>" type="audio/mpeg">
                    Votre navigateur ne prend pas en charge l'élément audio.
                </audio>
            <?php endif; ?>
            <a href='post_info.php?id=<?php echo $row['id']; ?>'>Voir plus</a>
        </div>
    <?php endwhile; ?>
<?php else : ?>
    <p>Aucune publication disponible.</p>
<?php endif; ?>

</body>

</html>