<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "root";
$database = "infra/dev";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

$search_term = "";

if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
}

if (!empty($search_term)) {
    $sql = "SELECT p.*, u.nom_utilisateur FROM publications p JOIN utilisateurs u ON p.user_id = id_utilisateur WHERE p.titre LIKE '%$search_term%' OR p.genre_musical LIKE '%$search_term%' OR p.type_publication LIKE '%$search_term%' ORDER BY p.date_publication DESC";
    $result = $conn->query($sql);
} else {
    $sql_likes = "SELECT p.*, u.nom_utilisateur FROM publications p JOIN utilisateurs u ON p.user_id = id_utilisateur ORDER BY p.likes_count DESC LIMIT 5";
    $result = $conn->query($sql_likes);

    $sql_recent = "SELECT p.*, u.nom_utilisateur FROM publications p JOIN utilisateurs u ON p.user_id = id_utilisateur ORDER BY p.date_publication DESC LIMIT 5";
    $result_recent = $conn->query($sql_recent);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/footer.css">
    <link rel="stylesheet" type="text/css" href="../css/search.css">
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <link rel="stylesheet" type="text/css" href="../css/cookies.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Home</title>
</head>

<body>
    <div id="imageFond"></div>

    <nav class="navbars">
        <ul class="navbar__menu">
            <li class="navbar__item">
                <a href="profil.php" class="navbar__link"><i class="fa fa-address-card"></i><span>Voir mon
                        profil</span></a>
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
    <div class="search">
        <form method="GET" action="">
            <div id="titre">
                <h1>SoundSphere</h1>
            </div>
            <div class="wrapper">
                <div class="searchBar">
                    <button id="searchQuerySubmit" type="submit" name="search">
                        <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                            <path fill="#666666" d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z" />
                        </svg>
                    </button>
                    <input id="searchQueryInput" type="text" name="search" placeholder="Chercher le titre, genre muscial ou type..." value="<?php echo $search_term; ?>" />
                    <div id="imageFond"></div>
                </div>
            </div>
        </form>
    </div>
    <?php if (!empty($search_term)) : ?>
        <h1>Résultats de la recherche pour "<?php echo $search_term; ?>"</h1>
        <div class="container-topics">
            <section class="existing-topics">
                <?php if ($result->num_rows > 0) : ?>
                    <ul>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <li class="topic-item">
                                <div class="topic-wrapper">
                                    <a class="title"><?php echo htmlspecialchars($row['titre']); ?></a>
                                    <br>

                                    <i class="fa fa-headphones" aria-hidden="true">
                                        <?php echo htmlspecialchars($row['genre_musical']); ?></i>
                                    <br>
                                    <p>Type de publication:
                                        <?php if ($row['type_publication'] == 'prod') : ?>
                                            <i class="fa fa-music" aria-hidden="true"></i> Prod
                                        <?php else : ?>
                                            <i class="fa fa-file-text" aria-hidden="true"></i> Texte
                                        <?php endif; ?>
                                    </p>
                                    <p>Crée par <?php echo $row['nom_utilisateur']; ?> le <?php echo $row['date_publication']; ?>
                                    </p>
                                    <a href="post_info.php?id=<?php echo $row['id']; ?>" style="text-decoration: none;">Découvrir
                                        ...</a>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else : ?>
                    <p>Aucune publication disponible.</p>
                <?php endif; ?>
            </section>
        </div>
    <?php else : ?>
        <div class="container-topics">
            <section class="existing-topics">
                <h1>Les plus likés</h1>
                <?php if ($result->num_rows > 0) : ?>
                    <ul>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <li class="topic-item">
                                <div class="topic-wrapper">
                                    <a class="title"><?php echo htmlspecialchars($row['titre']); ?></a>
                                    <br>

                                    <i class="fa fa-headphones" aria-hidden="true">
                                        <?php echo htmlspecialchars($row['genre_musical']); ?></i>
                                    <br>
                                    <p>Type de publication:
                                        <?php if ($row['type_publication'] == 'prod') : ?>
                                            <i class="fa fa-music" aria-hidden="true"></i> Prod
                                        <?php else : ?>
                                            <i class="fa fa-file-text" aria-hidden="true"></i> Texte
                                        <?php endif; ?>
                                    </p>
                                    <p>Crée par : <?php echo $row['nom_utilisateur']; ?> le <?php echo $row['date_publication']; ?>
                                    </p>
                                    <a href="post_info.php?id=<?php echo $row['id']; ?>" style="text-decoration: none;">Découvrir
                                        ...</a>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else : ?>
                    <p>Aucune publication disponible.</p>
                <?php endif; ?>
            </section>
        </div>
        <div class="container-topics">
            <section class="existing-topics">
                <h1>Les plus récents</h1>
                <?php if ($result_recent->num_rows > 0) : ?>
                    <ul>
                        <?php while ($row_recent = $result_recent->fetch_assoc()) : ?>
                            <li class="topic-item">
                                <div class="topic-wrapper">
                                    <a class="title"><?php echo htmlspecialchars($row_recent['titre']); ?></a>
                                    <br>
                                    <i class="fa fa-headphones" aria-hidden="true">
                                        <?php echo htmlspecialchars($row_recent['genre_musical']); ?></i>
                                    <br>
                                    <p>Type de publication:
                                        <?php if ($row_recent['type_publication'] == 'prod') : ?>
                                            <i class="fa fa-music" aria-hidden="true"></i> Prod
                                        <?php else : ?>
                                            <i class="fa fa-file-text" aria-hidden="true"></i> Texte
                                        <?php endif; ?>
                                    </p>
                                    <p>Crée par : <?php echo $row_recent['nom_utilisateur']; ?> le <?php echo $row_recent['date_publication']; ?>
                                    </p>
                                    <a href="post_info.php?id=<?php echo $row_recent['id']; ?>" style="text-decoration: none;">Découvrir ...</a>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else : ?>
                    <p>Aucune publication récente disponible.</p>
                <?php endif; ?>
            </section>
        </div>
    <?php endif; ?>
    <?php $conn->close(); ?>

    <script>
        feather.replace()
    </script>
    <div id="cookie-consent" class="cookie-consent">
        <span>Ce site utilise des cookies. En continuant à naviguer sur ce site, vous acceptez leur utilisation.</span>
        <div class="buttons">
            <button id="cookie-accept" class="cookie-accept">Accepter</button>
            <a href="./plus.php">En savoir plus</a>
        </div>
    </div>
</body>
<footer class="footer">
    <div class="footer-content">
        <p>© 2024 Soundsphere. Tous droits réservés.</p>
        <a href="./contact.php" class="contact-button">Nous contacter</a>
    </div>
</footer>

<script src="/js/cookies.js"></script>

</html>