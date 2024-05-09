<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "infra/dev";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

$sql = "SELECT * FROM publications ORDER BY date_publication DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main</title>
</head>
<body>
<a href="profil.php" class="btn btn-primary">Voir mon profil</a>
<a href="prods.php" class="btn btn-primary">Prods</a>
<a href="texte.php" class="btn btn-primary">Textes</a>

<h2>Nouvelle Publication</h2>
<form method="post" action="traitement_publication.php" enctype="multipart/form-data">
    <label for="type_publication">Type de publication:</label>
    <select name="type_publication" id="type_publication" onchange="showFields()">
        <option value="texte">Texte</option>
        <option value="prod">Prod</option>
    </select>

    <div id="textFields">
        <label for="contenu_texte">Contenu Texte:</label>
        <textarea name="contenu_texte" id="contenu_texte"></textarea>
    </div>

    <div id="audioFields" style="display:none;">
        <label for="audio">Fichier audio:</label>
        <input type="file" name="audio" id="audio">
    </div>

    <button type="submit">Publier</button>
</form>

<script>
    function showFields() {
        var typePublication = document.getElementById("type_publication").value;
        if (typePublication === "prod") {
            document.getElementById("audioFields").style.display = "block";
            document.getElementById("contenu_texte").setAttribute("disabled", "disabled");
            document.getElementById("textFields").style.display = "none";
        } else {
            document.getElementById("audioFields").style.display = "none";
            document.getElementById("contenu_texte").removeAttribute("disabled");
            document.getElementById("textFields").style.display = "block";
        }
    }
</script>

    <h1>Publications</h1>
    <?php if ($result->num_rows > 0) : ?>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <div>
                <p>Type de publication: <?php echo $row['type_publication']; ?></p>
                <p>Contenu: <?php echo $row['contenu']; ?></p>
                <?php if ($row['type_publication'] === 'prod' && !empty($row['lien_audio'])) : ?>
                    <audio controls>
                        <source src="<?php echo $row['lien_audio']; ?>" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else : ?>
        <p>Aucune publication disponible.</p>
    <?php endif; ?>
</body>

</html>