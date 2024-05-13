<?php
session_start();

$conn = new mysqli("localhost", "root", "", "infra/dev");

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM achats WHERE user_id = ?";
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
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['purchase_date'] . "</td>";
                echo "<td>" . $row['amount'] . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="main.php" class="btn btn-primary">Retour</a>
</div>

</body>
</html>
