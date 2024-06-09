<?php
require '../vendor/autoload.php';

error_reporting(E_ERROR | E_PARSE);

ob_start();
session_start();

$servername = "localhost";
$username = "root";
$password = "root";
$database = "infra/dev";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

// $sql_check_admin = "SELECT id_utilisateur FROM utilisateurs WHERE nom_utilisateur = 'admin' AND est_admin = 1";
// $result_check_admin = $conn->query($sql_check_admin);

if ($result_check_admin->num_rows === 0) {
    $admin_username = 'admin';
    $admin_email = 'admin@admin.com';
    $admin_password = password_hash('1234', PASSWORD_DEFAULT);

    $sql = "INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe, est_admin) VALUES (?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $admin_username, $admin_email, $admin_password);
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $login_username = $_POST['login_username'];
        $login_password = $_POST['login_password'];

        if (!empty($login_username) && !empty($login_password)) {
            $sql = "SELECT id_utilisateur, nom_utilisateur, mot_de_passe, est_admin FROM utilisateurs WHERE nom_utilisateur = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $login_username);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row && password_verify($login_password, $row['mot_de_passe'])) {
                $_SESSION['user_id'] = $row['id_utilisateur'];
                $user_name = $row['nom_utilisateur'];
                setcookie("user_name_cookie", $user_name, time() + 86400, "/");
                if ($row['est_admin'] == 1) {
                    header('Location: admin.php');
                } else {
                    $_SESSION['popup_shown'] = true;
                    header('Location: main.php');
                }
            } else {
                echo "Nom d'utilisateur ou mot de passe incorrect.";
            }
        } else {
            echo "Veuillez saisir le nom d'utilisateur et le mot de passe.";
        }
    } else {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $activation_token = bin2hex(random_bytes(32));

        $profile_picture = null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
        } else {
            $default_profile_picture_path = '../uploads/default_profile_picture.jpg';
            if (file_exists($default_profile_picture_path)) {
                $profile_picture = file_get_contents($default_profile_picture_path);
            } else {
                $profile_picture = null;
            }
        }

        $sql = "INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe, activation_token, profile_picture) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $username, $email, $password, $activation_token, $profile_picture);
        $stmt->send_long_data(4, $profile_picture);
        $stmt->execute();

        $user_id = $conn->insert_id;
        $_SESSION['user_id'] = $user_id;

        if (!empty($_POST['categories'])) {
            foreach ($_POST['categories'] as $category_id) {
                $sql = "INSERT INTO user_categories (user_id, category_id) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $user_id, $category_id);
                $stmt->execute();
            }
        }

        $activation_link = "https://soundsphere/pages/activer_compte.php?token=" . $activation_token;

        $transport = (new Swift_SmtpTransport('smtp.office365.com', 587, 'tls'))
            ->setUsername('tomandcocontact@gmail.com')
            ->setPassword('Panam2004*')
            ->setStreamOptions([
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                ],
            ]);

        $mailer = new Swift_Mailer($transport);

        $message = (new Swift_Message('Bienvenue sur Soundsphere'))
            ->setFrom(['tomandcocontact@gmail.com' => 'Soundsphere'])
            ->setTo([$email]) 
            ->setBody("Bonjour $username,\n\nMerci d'avoir créé un compte sur notre site. Cliquez sur le lien suivant pour activer votre compte :\n$activation_link\n\nCordialement");

        $result = $mailer->send($message);

        if ($result) {
            echo "E-mail envoyé avec succès.";
        } else {
            echo "Erreur lors de l'envoi de l'e-mail.";
        }
    }
}

$conn->close();
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
</head>

<body>
    <div class="main">
        <div class="container a-container" id="a-container">
            <form class="form" id="a-form" method="post" action="./login.php" enctype="multipart/form-data">
                <h2 class="form_title title">Créer un compte</h2>
                <input class="form__input" type="text" name="username" for="username" id="username" autocomplete="off" placeholder="Nom d'utilisateur" required>
                <input class="form__input" type="email" name="email" for="email" id="email" autocomplete="off" placeholder="Email" required>
                <input class="form__input" type="password" name="password" for="password" id="password" autocomplete="off" placeholder="Mot de passe" required>
                <label for="profile_picture">Photo de profil :</label>
                <input type="file" name="profile_picture" id="profile_picture">
                <label for="categories">Catégories :</label><br>
                <input type="checkbox" name="categories[]" value="1"> Beatmaker<br>
                <input type="checkbox" name="categories[]" value="2"> Ghostwriter<br>
                <input type="checkbox" name="categories[]" value="3"> Chanteur<br>
                <input type="checkbox" name="categories[]" value="4"> Producteur<br>
                <button type="submit" class="form__button button">S'INSCRIRE</button>
            </form>
        </div>
        <div class="container b-container" id="b-container">
            <form class="form" id="b-form" method="post" action="./login.php">
                <h2 class="form_title title">Se connecter</h2>
                <input class="form__input" type="text" name="login_username" for="login_username" placeholder="Nom d'utilisateur" id="login_username" autocomplete="off" required>
                <input class="form__input" type="password" name="login_password" for="login_password" placeholder="Mot de passe" id="login_password" autocomplete="off" required>
                <button class="form__button button" type="submit" name="login">SE CONNECTER</button>
            </form>
        </div>
        <div class="switch" id="switch-cnt">
            <div class="switch__circle"></div>
            <div class="switch__circle switch__circle--t"></div>
            <div class="switch__container" id="switch-c1">
                <h2 class="switch__title title">Ravi de vous revoir !</h2>
                <p class="switch__description description">Pour rester connecté avec nous, veuillez vous connecter avec vos informations personnelles</p>
                <button class="switch__button button switch-btn">SE CONNECTER</button>
            </div>
            <div class="switch__container is-hidden" id="switch-c2">
                <h2 class="switch__title title">Bonjour !</h2>
                <p class="switch__description description">Entrez vos données personnelles et commencez votre voyage avec nous</p>
                <button class="switch__button button switch-btn">S'INSCRIRE</button>
            </div>
        </div>
    </div>
    <script src="../js/login.js"></script>
</body>

</html>