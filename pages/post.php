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
?>

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