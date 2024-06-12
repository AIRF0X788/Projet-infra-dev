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

if (empty($_SESSION['user_id'])) {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];

        $sql_delete_user_categories = "DELETE FROM user_categories WHERE user_id = ?";
        $stmt_delete_user_categories = $conn->prepare($sql_delete_user_categories);
        $stmt_delete_user_categories->bind_param("i", $user_id);
        $stmt_delete_user_categories->execute();

        $sql_delete_user = "DELETE FROM utilisateurs WHERE id_utilisateur = ?";
        $stmt_delete_user = $conn->prepare($sql_delete_user);
        $stmt_delete_user->bind_param("i", $user_id);
        $stmt_delete_user->execute();

        echo "L'utilisateur avec l'ID $user_id a été supprimé avec succès.";
    }
}

if (isset($_POST['delete_post'])) {
    $post_id_to_delete = $_POST['post_id'];

    $sql_delete_post = "DELETE FROM publications WHERE id = ?";
    $stmt_delete_post = $conn->prepare($sql_delete_post);
    $stmt_delete_post->bind_param("i", $post_id_to_delete);
    $stmt_delete_post->execute();

    echo "La publication avec l'ID $post_id_to_delete a été supprimée avec succès.";
}

if (isset($_POST['ban_user']) || isset($_POST['unban_user'])) {
    $user_id_to_change = $_POST['user_id'];

    $sql_check_admin = "SELECT est_admin FROM utilisateurs WHERE id_utilisateur = ?";
    $stmt_check_admin = $conn->prepare($sql_check_admin);
    $stmt_check_admin->bind_param("i", $user_id_to_change);
    $stmt_check_admin->execute();
    $result_check_admin = $stmt_check_admin->get_result();
    $row_check_admin = $result_check_admin->fetch_assoc();

    if ($row_check_admin['est_admin'] == 1) {
        echo "Impossible de bannir ou débannir un administrateur.";
    } else {
        if (isset($_POST['ban_user'])) {
            $sql_ban_user = "UPDATE utilisateurs SET ban = 1 WHERE id_utilisateur = ?";
            $stmt_ban_user = $conn->prepare($sql_ban_user);
            $stmt_ban_user->bind_param("i", $user_id_to_change);
            $stmt_ban_user->execute();
            echo "L'utilisateur avec l'ID $user_id_to_change a été banni avec succès.";
        }

        if (isset($_POST['unban_user'])) {
            $sql_unban_user = "UPDATE utilisateurs SET ban = 0 WHERE id_utilisateur = ?";
            $stmt_unban_user = $conn->prepare($sql_unban_user);
            $stmt_unban_user->bind_param("i", $user_id_to_change);
            $stmt_unban_user->execute();
            echo "L'utilisateur avec l'ID $user_id_to_change a été débanni avec succès.";
        }
    }
}

$sql_all_users = "SELECT id_utilisateur, nom_utilisateur, email, statut, est_admin, ban FROM utilisateurs";
$result_all_users = $conn->query($sql_all_users);

$sql_all_posts = "SELECT p.id, p.titre, p.description, p.date_publication, u.nom_utilisateur FROM publications p INNER JOIN utilisateurs u ON p.user_id = u.id_utilisateur";
$result_all_posts = $conn->query($sql_all_posts);

$conn->close();
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Admin Page</title>
</head>

<body>
    <div class="container mt-5">
        <h2>Bienvenue sur la page d'administration</h2>
        <br>
        <form method="post" action="admin.php">
            <h4>Supprimer un compte utilisateur</h4>
            <div class="form-group">
                <label for="user_id">ID de l'utilisateur :</label>
                <input type="number" id="user_id" name="user_id" class="form-control" required>
            </div>
            <button type="submit" name="delete_user" class="btn btn-danger">Supprimer le compte utilisateur</button>
        </form>
        <hr>
        <form method="post" action="admin.php">
            <h4>Supprimer une publication</h4>
            <div class="form-group">
                <label for="post_id">ID de la publication :</label>
                <input type="number" id="post_id" name="post_id" class="form-control" required>
            </div>
            <button type="submit" name="delete_post" class="btn btn-danger">Supprimer la publication</button>
        </form>
        <hr>
        <h4>Liste des utilisateurs</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom d'utilisateur</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Admin</th>
                    <th>Banni</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_all_users->num_rows > 0) {
                    while ($row = $result_all_users->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id_utilisateur'] . "</td>";
                        echo "<td>" . $row['nom_utilisateur'] . "</td>";
                        echo "<td>" . $row['email'] . "</td>";
                        echo "<td>" . $row['statut'] . "</td>";
                        echo "<td>" . ($row['est_admin'] ? 'Oui' : 'Non') . "</td>";
                        echo "<td>" . ($row['ban'] ? 'Oui' : 'Non') . "</td>";
                        echo "<td>";
                        if ($row['ban']) {
                            echo "<form method='post' action='admin.php' style='display:inline-block;'>
                                    <input type='hidden' name='user_id' value='" . $row['id_utilisateur'] . "'>
                                    <button type='submit' name='unban_user' class='btn btn-success'>Débannir</button>
                                  </form>";
                        } else {
                            echo "<form method='post' action='admin.php' style='display:inline-block;'>
                                    <input type='hidden' name='user_id' value='" . $row['id_utilisateur'] . "'>
                                    <button type='submit' name='ban_user' class='btn btn-warning'>Bannir</button>
                                  </form>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Aucun utilisateur trouvé</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <hr>
        <h4>Liste des publications</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Date de publication</th>
                    <th>Auteur</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_all_posts->num_rows > 0) {
                    while ($row = $result_all_posts->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['titre'] . "</td>";
                        echo "<td>" . $row['description'] . "</td>";
                        echo "<td>" . $row['date_publication'] . "</td>";
                        echo "<td>" . $row['nom_utilisateur'] . "</td>";
                        echo "<td>";
                        echo "<form method='post' action='admin.php' style='display:inline-block;'>
                                <input type='hidden' name='post_id' value='" . $row['id'] . "'>
                                <button type='submit' name='delete_post' class='btn btn-danger'>Supprimer</button>
                              </form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Aucune publication trouvée</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <hr>
        <h4>Demande de contact</h4>
        <a href="./demande_contact.php">Page de contact</a>
    </div>
    <button type="button" class="mt-5 btn btn-default btn-block"><a href="logout.php"
    style="text-decoration:none;">Déconnexion</a></button>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
</body>

</html>