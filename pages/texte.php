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

$search_term = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT publications.*, utilisateurs.nom_utilisateur 
        FROM publications 
        JOIN utilisateurs ON publications.user_id = id_utilisateur
        WHERE type_publication = 'texte'";

if (!empty($search_term)) {
    $sql .= " AND (titre LIKE '%" . $conn->real_escape_string($search_term) . "%' OR genre_musical LIKE '%" . $conn->real_escape_string($search_term) . "%')";
}

$result = $conn->query($sql);

if (!$result) {
    die("Erreur dans la requête SQL : " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Textes</title>
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/search.css">
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <nav class="navbars">
        <ul class="navbar__menu">
            <li class="navbar__item">
                <a href="main.php" class="navbar__link"><i class="fa fa-home"></i><span>Home</span></a>
            </li>
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
        </ul>
    </nav>
    <div class="search">
        <form method="GET" action="">
            <div class="wrapper">
                <div class="searchBar">
                    <button id="searchQuerySubmit" type="submit" name="search">
                        <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                            <path fill="#666666"
                                d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z" />
                        </svg>
                    </button>
                    <input id="searchQueryInput" type="text" name="search"
                        placeholder="Chercher le titre, genre musical ou type..."
                        value="<?php echo htmlspecialchars($search_term); ?>" />
                </div>
            </div>
        </form>
    </div>
    <?php if (!empty($search_term)): ?>
        <h1>Résultats de la recherche pour "<?php echo htmlspecialchars($search_term); ?>"</h1>
    <?php endif; ?>
    <div class="container-topics">
        <section class="existing-topics">
            <?php if ($result->num_rows > 0): ?>
                <ul>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <li class="topic-item">
                            <div class="topic-wrapper">
                                <a class="title"><?php echo htmlspecialchars($row['titre']); ?></a>
                                <br>
                                <i class="fa fa-headphones" aria-hidden="true">
                                    <?php echo htmlspecialchars($row['genre_musical']); ?></i>
                                <br>
                                <p>Type de publication:
                                    <?php if ($row['type_publication'] == 'prod'): ?>
                                        <i class="fa fa-music" aria-hidden="true"></i> Prod
                                    <?php else: ?>
                                        <i class="fa fa-file-text" aria-hidden="true"></i> Texte
                                    <?php endif; ?>
                                </p>
                                <p>Crée par : <?php echo $row['nom_utilisateur']; ?> le <?php echo $row['date_publication']; ?></p>
                                <a href="post_info.php?id=<?php echo $row['id']; ?>" style="text-decoration: none;">Découvrir
                                    ...</a>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Aucune publication disponible.</p>
            <?php endif; ?>
        </section>
    </div>
    <?php $conn->close(); ?>
</body>

</html>