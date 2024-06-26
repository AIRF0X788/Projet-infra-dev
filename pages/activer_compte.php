<?php
$servername = "localhost";
$username = "root";
$password = "root";
$database = "infra/dev";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

if (isset($_GET['token'])) {
    $activation_token = $_GET['token'];
    $sql = "SELECT id_utilisateur FROM utilisateurs WHERE activation_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $activation_token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['id_utilisateur'];

        $update_sql = "UPDATE utilisateurs SET statut = 'actif', activation_token = NULL WHERE id_utilisateur = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $user_id);
        $update_stmt->execute();

        echo "Votre compte a été activé avec succès!";
    } else {
        echo "Le token d'activation est invalide.";
    }
} else {
    echo "Paramètre token manquant dans l'URL.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirection en cours...</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="text-center">
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    setTimeout(function () {
        window.location.href = 'main.php';
    }, 1000);
</script>

</body>
</html>
