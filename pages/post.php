<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Publication</title>
</head>
<body>
    <h2>Nouvelle Publication</h2>
    <form method="post" action="traitement_publication.php" enctype="multipart/form-data">
        <label for="type_publication">Type de publication:</label>
        <select name="type_publication" id="type_publication" onchange="showFields()">
            <option value="texte">Texte</option>
            <option value="prod">Prod</option>
        </select>

        <div id="textFields">
            <label for="titre_texte">Titre:</label>
            <input type="text" name="titre_texte" id="titre_texte"><br>
            <label for="description_texte">Description:</label>
            <textarea name="description_texte" id="description_texte"></textarea><br>
            <label for="contenu_texte">Contenu Texte:</label>
            <textarea name="contenu_texte" id="contenu_texte"></textarea>
        </div>

        <div id="audioFields" style="display:none;">
            <label for="titre_prod">Titre:</label>
            <input type="text" name="titre_prod" id="titre_prod"><br>
            <label for="description_prod">Description:</label>
            <textarea name="description_prod" id="description_prod"></textarea><br>
            <label for="audio">Fichier audio:</label>
            <input type="file" name="audio" id="audio">
        </div>

        <label for="payant">Payant:</label>
        <input type="checkbox" name="payant" id="payant" onchange="showPrice()"><br>

        <div id="priceField" style="display:none;">
            <label for="prix">Prix:</label>
            <input type="number" name="prix" id="prix" step="0.01">
        </div>

        <button type="submit">Publier</button>
    </form>

    <script>
        function showFields() {
            var selectedValue = document.getElementById("type_publication").value;
            var textFields = document.getElementById("textFields");
            var audioFields = document.getElementById("audioFields");

            if (selectedValue === "texte") {
                textFields.style.display = "block";
                audioFields.style.display = "none";
            } else if (selectedValue === "prod") {
                textFields.style.display = "none";
                audioFields.style.display = "block";
            }
        }

        function showPrice() {
            var payantCheckbox = document.getElementById("payant");
            var priceField = document.getElementById("priceField");

            if (payantCheckbox.checked) {
                priceField.style.display = "block";
            } else {
                priceField.style.display = "none";
            }
        }

        showFields();
    </script>
</body>
</html>
