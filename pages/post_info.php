<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/commentaire.css">
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Info</title>
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
                <a href="prods.php" class="navbar__link"><i class="fa fa-headphones"></i><span>Prods</span></a>
            </li>
            <li class="navbar__item">
                <a href="texte.php" class="navbar__link"><i class="fa fa-file-text-o"></i><span>Textes</span></a>
            </li>
        </ul>
    </nav>
    <div class="publication-container">
        <?php
        session_start();

        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
        

        $servername = "localhost";
        $username = "root";
        $password = "root";
        $database = "infra/dev";

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
            die("La connexion à la base de données a échoué : " . $conn->connect_error);
        }

        if (isset($_GET['id'])) {
            $post_id = $_GET['id'];
            $user_id = $_SESSION['user_id'];

            $sql = "SELECT * FROM publications WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "<div class='publication-details'>";
                echo "<h2>" . htmlspecialchars($row['titre']) . "</h2>";
                echo "<p><strong>Type de publication:</strong> " . htmlspecialchars($row['type_publication']) . "</p>";
                echo "<p><strong>Genre musical:</strong> " . htmlspecialchars($row['genre_musical']) . "</p>";
                echo "<p><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
                if ($row['payant'] === 1 && !empty($row['prix'])) {
                    echo "<p><strong>Prix:</strong> " . htmlspecialchars($row['prix']) . "</p>";
                    $prix_produit = $row['prix'];
                }
                if ($row['type_publication'] === 'texte' && !empty($row['contenu_texte'])) {
                    echo "<p><strong>Texte:</strong> " . htmlspecialchars($row['contenu_texte']) . "</p>";
                }
                if ($row['type_publication'] === 'prod' && !empty($row['audio_data'])) {
                    $audio_url = "serve_audio.php?id=" . htmlspecialchars($row['id']);
                    if ($row['payant'] === 1 && !empty($row['prix'])) {
                        echo "<audio id='audioPlayer'>";
                        echo "<source src='" . $audio_url . "' type='" . htmlspecialchars($row['audio_type']) . "'>";
                        echo "Votre navigateur ne prend pas en charge l'élément audio.";
                        echo "</audio>";
                        echo "<button id='previewButton'>Ecouter l'extrait de la prod</button>";
                    } else {
                        echo "<audio id='audioPlayer' controls>";
                        echo "<source src='" . $audio_url . "' type='" . htmlspecialchars($row['audio_type']) . "'>";
                        echo "Votre navigateur ne prend pas en charge l'élément audio.";
                        echo "</audio>";
                    }
                }
                
                // if (isset($prix_produit)) {
                //     echo '<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top">';
                //     echo '<input type="hidden" name="cmd" value="_xclick">';
                //     echo '<input type="hidden" name="business" value="sb-i0zuj26771389@business.example.com">';
                //     echo '<input type="hidden" name="currency_code" value="EUR">';
                //     echo '<input type="hidden" name="amount" value="' . htmlspecialchars($prix_produit, ENT_QUOTES, 'UTF-8') . '">';
                //     echo '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - Le moyen le plus sûr et le plus simple de payer en ligne !">';
                //     echo '<img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">';
                //     echo '</form>';
                // }

                echo "</div>";

                echo "<p><strong>Likes:</strong> " . htmlspecialchars($row['likes_count']) . "</p>";

                $liked = isset($_COOKIE['liked_' . $post_id]) && $_COOKIE['liked_' . $post_id] == 'true';

                echo "<form method='POST' action='like.php'>";
                echo "<input type='hidden' name='publication_id' value='" . htmlspecialchars($post_id) . "'>";
                if ($liked) {
                    echo "<button type='submit' class='btn btn-secondary' disabled>Liked</button>";
                } else {
                    echo "<button type='submit' class='btn btn-primary'>Like</button>";
                }
                echo "</form>";

                if ($row['user_id'] == $user_id) {
                    echo "<form method='POST' action='delete_post.php'>";
                    echo "<input type='hidden' name='post_id' value='" . htmlspecialchars($post_id) . "'>";
                    echo "<button type='submit' class='btn btn-danger'>Supprimer</button>";
                    echo "</form>";
                }
                if ($row['payant'] === 1 && !empty($row['prix'])) {
                    echo '<a href="process_payment.php?id=' . htmlspecialchars($post_id) . '&prix=' . htmlspecialchars($row['prix']) . '" class="btn btn-primary">Acheter</a>';
                }
            }
            else {
                echo "Aucun poste trouvé avec cet ID.";
            }
        } else {
            echo "ID de poste non spécifié.";
        }
        ?>
    </div>
    <div class="comments-container">
        <h3>Commentaires</h3>
        <?php
        $sql_comments = "SELECT c.*, u.nom_utilisateur FROM commentaires c INNER JOIN utilisateurs u ON c.user_id = u.id_utilisateur WHERE c.publication_id = ?";
        $stmt_comments = $conn->prepare($sql_comments);
        $stmt_comments->bind_param("i", $post_id);
        $stmt_comments->execute();
        $result_comments = $stmt_comments->get_result();
        ?>
        <div class="write-new">
            <form method="POST" action="traitement_commentaire.php">
                <input type="hidden" name="publication_id" value="<?php echo htmlspecialchars($post_id); ?>">
                <textarea placeholder="Écrire un commentaire..." id="messageInput" name="commentaire" rows="4" cols="50"
                    required></textarea><br>
                <button type="submit">Ajouter un commentaire</button>
            </form>
        </div>
        <div class="comments-list">
            <?php if ($result_comments->num_rows > 0): ?>
                <ul>
                    <?php while ($row_comment = $result_comments->fetch_assoc()): ?>
                        <li>
                            <div class="comment-main-level">
                                <div class="comment-box">
                                    <div class="comment-head">
                                        <h6 class="comment-name by-author">
                                            <a href="#"><?php echo htmlspecialchars($row_comment['nom_utilisateur']); ?></a>
                                        </h6>
                                        <?php echo date("d/m/Y à H:i", strtotime($row_comment['date_creation'])); ?></span>
                                    </div>
                                    <div class="comment-content">
                                        <?php echo htmlspecialchars($row_comment['commentaire']); ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Aucun commentaire trouvé.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById("previewButton").addEventListener("click", function () {
            var audio = document.getElementById("audioPlayer");
            audio.currentTime = 0;
            audio.play();
            setTimeout(function () {
                audio.pause();
            }, 15000);
        });
    </script>
</body>

</html>
