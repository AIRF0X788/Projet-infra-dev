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

$sql_likes = "SELECT * FROM publications ORDER BY likes_count DESC LIMIT 5";
$result_likes = $conn->query($sql_likes);

$sql_recent = "SELECT * FROM publications ORDER BY date_publication DESC LIMIT 5";
$result_recent = $conn->query($sql_recent);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Home</title>
</head>

<body>
    <nav class="navbars">
        <ul class="navbar__menu">
            <li class="navbar__item">
                <a href="profil.php" class="navbar__link"><i class="fa fa-address-card"></i><span>Voir mon profil</span></a>
            </li>
            <li class="navbar__item">
                <a href="achat.php" class="navbar__link"><i class="fa fa-cart-plus"></i><span>Voir mes achats</span></a>
            </li>
            <li class="navbar__item">
                <a href="post.php" class="navbar__link"><i class="fa fa-plus"></i><span>Poster</span></a>
            </li>
            <li class="navbar__item">
                <a href="prods.php" class="navbar__link"><i class="fa fa-headphones"></i><span>Prods</span></a>
            </li>
            <li class="navbar__item">
                <a href="texte.php" class="navbar__link"><i class="fa fa-file-text-o"></i><span>Textes</span></a>
            </li>
        </ul>
    </nav>
    <h1>Les plus likés</h1>
    <?php if ($result_likes->num_rows > 0): ?>
        <?php while ($row = $result_likes->fetch_assoc()): ?>
            <div>
                <p>Type: <?php echo $row['type_publication']; ?></p>
                <p>Titre: <?php echo $row['titre']; ?></p>
                <a href='post_info.php?id=<?php echo $row['id']; ?>'>Voir plus</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Aucune publication disponible.</p>
    <?php endif; ?>

    <h1>Les plus récents</h1>
    <?php if ($result_recent->num_rows > 0): ?>
        <?php while ($row = $result_recent->fetch_assoc()): ?>
            <div>
                <p>Type: <?php echo $row['type_publication']; ?></p>
                <p>Titre: <?php echo $row['titre']; ?></p>
                <a href='post_info.php?id=<?php echo $row['id']; ?>'>Voir plus</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Aucune publication disponible.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>

    <script>feather.replace()</script>
</body>

</html>