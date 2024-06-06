<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$servername = "localhost";
$username = "root";
$password = "";
$database = "infra/dev";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

$success_username = $success_email = $success_password = $success_picture = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_picture'])) {
        if (isset($_FILES['new_profile_picture']) && $_FILES['new_profile_picture']['error'] === UPLOAD_ERR_OK) {
            $profile_picture = file_get_contents($_FILES['new_profile_picture']['tmp_name']);
        }
        $sql_update_picture = "UPDATE utilisateurs SET profile_picture = ? WHERE id_utilisateur = ?";
        $stmt_update_picture = $conn->prepare($sql_update_picture);
        $stmt_update_picture->bind_param("si", $profile_picture, $user_id);
        $stmt_update_picture->send_long_data(4, $profile_picture);
        if ($stmt_update_picture->execute()) {
            $success_picture = "Photo de profil mise à jour avec succès.";
        } else {
            echo "Erreur lors de la mise à jour de la photo de profil : " . $stmt_update_picture->error;
        }
    }



    if (isset($_POST['change_username'])) {
        $new_username = $_POST['new_username'];
        $sql = "UPDATE utilisateurs SET nom_utilisateur = ? WHERE id_utilisateur = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_username, $user_id);
        if ($stmt->execute()) {
            $success_username = "Nom d'utilisateur mis à jour avec succès.";
        } else {
            echo "Erreur lors de la mise à jour du nom d'utilisateur : " . $stmt->error;
        }
    }

    if (isset($_POST['change_email'])) {
        $new_email = $_POST['new_email'];
        $sql = "UPDATE utilisateurs SET email = ? WHERE id_utilisateur = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_email, $user_id);
        if ($stmt->execute()) {
            $success_email = "Adresse e-mail mise à jour avec succès.";
        } else {
            echo "Erreur lors de la mise à jour de l'adresse e-mail : " . $stmt->error;
        }
    }

    if (isset($_POST['change_password'])) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $sql = "UPDATE utilisateurs SET mot_de_passe = ? WHERE id_utilisateur = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_password, $user_id);
        if ($stmt->execute()) {
            $success_password = "Mot de passe mis à jour avec succès.";
        } else {
            echo "Erreur lors de la mise à jour du mot de passe : " . $stmt->error;
        }
    }
    $sql_delete = "DELETE FROM user_categories WHERE user_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $user_id);
    $stmt_delete->execute();

    if (!empty($_POST['categories'])) {
        foreach ($_POST['categories'] as $category_id) {
            $sql_insert = "INSERT INTO user_categories (user_id, category_id) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ii", $user_id, $category_id);
            $stmt_insert->execute();
        }
    }

    header("Location: profil.php");
    exit();
}

$sql_categories = "SELECT id, name FROM categories";
$result_categories = $conn->query($sql_categories);

$sql_user_categories = "SELECT category_id FROM user_categories WHERE user_id = ?";
$stmt_user_categories = $conn->prepare($sql_user_categories);
$stmt_user_categories->bind_param("i", $user_id);
$stmt_user_categories->execute();
$result_user_categories = $stmt_user_categories->get_result();

$user_categories = [];
while ($row = $result_user_categories->fetch_assoc()) {
    $user_categories[] = $row['category_id'];
}

$sql = "SELECT id_utilisateur, nom_utilisateur, email, profile_picture FROM utilisateurs WHERE id_utilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_id = $row['id_utilisateur'];
    $nom_utilisateur = $row['nom_utilisateur'];
    $email = $row['email'];
    $profile_picture = $row['profile_picture'];
} else {
    echo "Aucun résultat trouvé pour cet utilisateur.";
}


$sql = "SELECT c.name FROM categories c
        INNER JOIN user_categories uc ON c.id = uc.category_id
        WHERE uc.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row['name'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/profil.css">
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Profil</title>
</head>

<body>
    <nav class="navbars">
        <ul class="navbar__menu">
            <li class="navbar__item">
                <a href="main.php" class="navbar__link"><i class="fa fa-home"></i><span>Home</span></a>
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
    <div class="container-fluid main" style="height:100vh;padding-left:25%;">
        <div class="row align-items-center" style="height:100%">
            <div class="col-md-9">
                <div class="container content clear-fix">
                    <h2 class="mt-5 mb-5">Profil de <?php echo $nom_utilisateur; ?></h2>
                    <div class="row" style="height:100%">
                        <div class="col-md-3">
                            <div class="d-inline position-relative profile-container">
                                <?php if (!empty($profile_picture)): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($profile_picture); ?>"
                                        width="130px" height="130px" id="profilePicture" class="rounded-circle">
                                <?php endif; ?>
                                <form method="post" enctype="multipart/form-data" id="profileForm">
                                    <div class="overlay">
                                        Changer la photo de profil
                                        <input type="file" name="new_profile_picture" accept="image/png, image/jpeg"
                                            id="fileInput">
                                    </div>
                                    <br>
                                    <button type="submit" name="update_picture" class="btn btn-primary btn-block"
                                        id="submitButton">Enregistrer</button>
                                    <br>
                                    <p id="error-message" class="text-danger" style="display: none;">Vous devez choisir
                                        une photo de profil.</p>
                                </form>
                            </div>
                            <p><strong>ID Utilisateur:</strong> <?php echo $user_id; ?></p>
                            <p><strong>Nom d'utilisateur:</strong> <?php echo $nom_utilisateur; ?></p>
                            <p><strong>Email:</strong> <?php echo $email; ?></p>
                            <?php if (!empty($categories)): ?>
                                <p><strong>Catégories :</strong> <?php echo implode(", ", $categories); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <div class="container">
                                <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="usernameForm">
                                    <div class="form-group">
                                        <label for="new_username">Changer le nom d'utilisateur:</label>
                                        <input type="text" class="form-control" name="new_username" id="new_username"
                                            required>
                                    </div>
                                    <button type="submit" name="change_username"
                                        class="btn btn-primary btn-block">Changer le nom d'utilisateur</button>
                                </form>
                                <?php if (!empty($success_username)) {
                                    echo "<p class='text-success'>$success_username</p>";
                                } ?>
                                <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="emailForm">
                                    <div class="mt-4 form-group">

                                        <label for="new_email">Changer l'adresse e-mail:</label>
                                        <input type="email" class="form-control" name="new_email" id="new_email"
                                            required>
                                    </div>
                                    <button type="submit" name="change_email" class="btn btn-primary btn-block">Changer
                                        l'adresse e-mail</button>
                                </form>
                                <?php if (!empty($success_email)) {
                                    echo "<p class='text-success'>$success_email</p>";
                                } ?>
                                <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="passwordForm">
                                    <div class="mt-4 form-group">
                                        <label for="new_password">Changer le mot de passe:</label>
                                        <input type="password" class="form-control" name="new_password"
                                            id="new_password" required>
                                    </div>
                                    <button type="submit" name="change_password"
                                        class="btn btn-primary btn-block">Changer
                                        le mot de passe</button>
                                </form>
                                <?php if (!empty($success_password)) {
                                    echo "<p class='text-success'>$success_password</p>";
                                } ?>
                                <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                                    <div class="mt-4 form-group">

                                        <label>Changer les catégories:</label>
                                        <br>
                                        <?php while ($row = $result_categories->fetch_assoc()): ?>
                                            <input type="checkbox" name="categories[]" value="<?php echo $row['id']; ?>"
                                                <?php echo (in_array($row['id'], $user_categories) ? 'checked' : ''); ?>>
                                            <?php echo $row['name']; ?><br>
                                        <?php endwhile; ?>
                                    </div>
                                    <button type="submit" name="update_categories"
                                        class="btn btn-primary btn-block">Mettre
                                        à jour les catégories</button>
                                    <button type="button" class="mt-5 btn btn-default btn-block"><a href="logout.php"
                                            style="text-decoration:none;">Déconnexion</a></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/profil.js"></script>
</body>

</html>