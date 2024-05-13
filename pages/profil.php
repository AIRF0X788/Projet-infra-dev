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

$success_username = $success_email = $success_password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

$sql = "SELECT id_utilisateur, nom_utilisateur, email FROM utilisateurs WHERE id_utilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_id = $row['id_utilisateur'];
    $nom_utilisateur = $row['nom_utilisateur'];
    $email = $row['email'];
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
    <title>Profil</title>
</head>

<body>
<a href="logout.php">Déconnexion</a>
<a href="main.php" class="btn btn-primary">Retourner à la page d'accueil</a>
    <div class="container mt-3">
        <h2>Profil de <?php echo $nom_utilisateur; ?></h2>
        <p><strong>ID Utilisateur:</strong> <?php echo $user_id; ?></p>
        <p><strong>Nom d'utilisateur:</strong> <?php echo $nom_utilisateur; ?></p>
        <p><strong>Email:</strong> <?php echo $email; ?></p>
        <?php if (!empty($categories)) : ?>
            <p><strong>Catégories :</strong> <?php echo implode(", ", $categories); ?></p>
        <?php endif; ?>
        <br><br>

        <div class="mb-3">
            <button class="btn btn-primary" onclick="showForm('usernameForm')">Changer le nom d'utilisateur</button>
            <button class="btn btn-primary" onclick="showForm('emailForm')">Changer l'adresse e-mail</button>
            <button class="btn btn-primary" onclick="showForm('passwordForm')">Changer le mot de passe</button>
        </div>

        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="usernameForm" style="display:none;">
            <label for="new_username">Changer le nom d'utilisateur:</label>
            <input type="text" name="new_username" id="new_username" required>
            <button type="submit" name="change_username" class="btn btn-primary">Changer le nom d'utilisateur</button>
        </form>
        <?php if (!empty($success_username)) {
            echo "<p class='text-success'>$success_username</p>";
        } ?>

        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="emailForm" style="display:none;">
            <label for="new_email">Changer l'adresse e-mail:</label>
            <input type="email" name="new_email" id="new_email" required>
            <button type="submit" name="change_email" class="btn btn-primary">Changer l'adresse e-mail</button>
        </form>
        <?php if (!empty($success_email)) {
            echo "<p class='text-success'>$success_email</p>";
        } ?>

        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="passwordForm" style="display:none;">
            <label for="new_password">Changer le mot de passe:</label>
            <input type="password" name="new_password" id="new_password" required>
            <button type="submit" name="change_password" class="btn btn-primary">Changer le mot de passe</button>
        </form>
        <?php if (!empty($success_password)) {
            echo "<p class='text-success'>$success_password</p>";
        } ?>
        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <h3>Changer les catégories</h3>
            <?php while ($row = $result_categories->fetch_assoc()): ?>
                <input type="checkbox" name="categories[]" value="<?php echo $row['id']; ?>" <?php echo (in_array($row['id'], $user_categories) ? 'checked' : ''); ?>>
                <?php echo $row['name']; ?><br>
            <?php endwhile; ?>
            <button type="submit" name="update_categories">Mettre à jour les catégories</button>
        </form>
    </div>

    <script>
        function showForm(formId) {
            document.getElementById('usernameForm').style.display = 'none';
            document.getElementById('emailForm').style.display = 'none';
            document.getElementById('passwordForm').style.display = 'none';

            document.getElementById(formId).style.display = 'block';
        }
    </script>
</body>

</html>