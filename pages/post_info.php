<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info</title>
</head>

<body>
    <a href="main.php" class="btn btn-primary">Retour</a>
    <div>
        <?php
        session_start();

        if (!isset($_SESSION['user_id'])) {
            die("Vous devez être connecté pour voir cette page.");
        }

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
            $user_id = $_SESSION['user_id'];

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
                    if ($row['payant'] === 1 && !empty($row['prix'])) {
                        echo "<audio id='audioPlayer'>";
                        echo "<source src='" . $row['lien_audio'] . "' type='audio/mpeg'>";
                        echo "Votre navigateur ne prend pas en charge l'élément audio.";
                        echo "</audio>";
                        echo "<button id='previewButton'>Ecouter l'extrait de la prod</button>";
                    } else {
                        echo "<audio id='audioPlayer' controls>";
                        echo "<source src='" . $row['lien_audio'] . "' type='audio/mpeg'>";
                        echo "Votre navigateur ne prend pas en charge l'élément audio.";
                        echo "</audio>";
                    }
                }
                echo "</div>";

                echo "<p>Likes: " . $row['likes_count'] . "</p>";

                $liked = isset($_COOKIE['liked_' . $post_id]) && $_COOKIE['liked_' . $post_id] == 'true';

                echo "<form method='POST' action='like.php'>";
                echo "<input type='hidden' name='publication_id' value='" . $post_id . "'>";
                if ($liked) {
                    echo "<button type='submit' class='btn btn-secondary' disabled>Liked</button>";
                } else {
                    echo "<button type='submit' class='btn btn-primary'>Like</button>";
                }
                echo "</form>";

                if ($row['user_id'] == $user_id) {
                    echo "<form method='POST' action='delete_post.php'>";
                    echo "<input type='hidden' name='post_id' value='" . $post_id . "'>";
                    echo "<button type='submit' class='btn btn-danger'>Supprimer</button>";
                    echo "</form>";
                }
            } else {
                echo "Aucun poste trouvé avec cet ID.";
            }
        } else {
            echo "ID de poste non spécifié.";
        }

        $conn->close();
        ?>
    </div>
    <script>
        document.getElementById("previewButton").addEventListener("click", function() {
            var audio = document.getElementById("audioPlayer");
            audio.currentTime = 0;
            audio.play();
            setTimeout(function() {
                audio.pause();
            }, 15000);
        });
    </script>
    <div class="container">
        <?php if ($row['payant'] === 1 && !empty($row['prix'])) : ?>
            <a href="process_payment.php?id=<?php echo $post_id; ?>&prix=<?php echo $row['prix']; ?>" class="btn btn-primary">Payer</a>
        <?php endif; ?>
    </div>
</body>

</html>
