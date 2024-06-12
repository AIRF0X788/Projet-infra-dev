<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des demandes de contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/demande.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="container mt-5 mb-5">Liste des demandes de contact</h2>
        <?php

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

        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }

        $user_id = $_SESSION['user_id'];

        $sql = "SELECT est_admin FROM utilisateurs WHERE id_utilisateur = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['est_admin'] != 1) {
                header('Location: main.php');
                exit();
            }
        }

        $sql = "SELECT * FROM demandes_contact ORDER BY date_demande DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo '<table class="table">';
            echo '<thead><tr><th scope="col">Email</th><th scope="col">Sujet</th><th scope="col">Message</th><th scope="col">Date</th></tr></thead>';
            echo '<tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['email'] . '</td>';
                echo '<td>' . $row['sujet'] . '</td>';
                echo '<td>' . $row['message'] . '</td>';
                echo '<td>' . $row['date_demande'] . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo "Aucune demande de contact trouvée.";
        }

        $conn->close();
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>