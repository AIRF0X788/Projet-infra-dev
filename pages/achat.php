<?php
session_start();

$conn = new mysqli("localhost", "root", "", "infra/dev");

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT achats.date_achat, achats.prix, publications.titre, publications.type_publication, publications.contenu_texte, publications.lien_audio
        FROM achats
        INNER JOIN publications ON achats.id_publication = publications.id
        WHERE achats.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulter les achats</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Vos achats précédents</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Date d'achat</th>
                <th>Titre de la publication</th>
                <th>Montant</th>
                <th>Télécharger</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['date_achat'] . "</td>";
                echo "<td>" . $row['titre'] . "</td>";
                echo "<td>" . $row['prix'] . "</td>";
                echo "<td>";
                if ($row['type_publication'] === 'texte') {
                    echo "<a href='download.php?type=text&title=" . urlencode($row['titre']) . "&content=" . urlencode($row['contenu_texte']) . "' class='btn btn-primary' download>Télécharger</a>";
                } elseif ($row['type_publication'] === 'prod') {
                    echo "<a href='" . $row['lien_audio'] . "' class='btn btn-primary' download>Télécharger</a>";
                }
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="main.php" class="btn btn-primary">Retour</a>
</div>

</body>
</html>
