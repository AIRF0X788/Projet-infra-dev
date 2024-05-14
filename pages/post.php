<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Publication</title>
    <link rel="stylesheet" type="text/css" href="../css/post.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.0/css/bulma.min.css">
</head>

<body>
    <a href="main.php" class="btn btn-primary">Retour</a>
    <form method="post" action="traitement_publication.php" enctype="multipart/form-data">
        <section class="section">
            <div class="container">
                <p class="has-text-white">Nouvelle Publication</p>
                <hr>
                <div class="columns is-centered">
                    <div class="column is-8">
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label for="type_publication" class="label">Type de publication:</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <div class="control">
                                        <div class="select">
                                            <select name="type_publication" id="type_publication"
                                                onchange="showFields()">
                                                <option value="texte">Texte</option>
                                                <option value="prod">Prod</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="textFields">
                            <div class="field is-horizontal">
                                <div class="field-label is-normal">
                                    <label class="label">Titre:</label>
                                </div>
                                <div class="field-body">
                                    <div class="field">
                                        <div class="control">
                                            <input class="input" type="text" name="titre_texte" id="titre_texte" placeholder="Titre">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="field is-horizontal">
                                <div class="field-label is-normal">
                                    <label class="label">Description:</label>
                                </div>
                                <div class="field-body">
                                    <div class="field">
                                        <div class="control">
                                            <textarea class="textarea" name="description_texte" id="description_texte" placeholder="Ceci est une description..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="field is-horizontal">
                                <div class="field-label is-normal">
                                    <label class="label">Contenu du Texte:</label>
                                </div>
                                <div class="field-body">
                                    <div class="field">
                                        <div class="control">
                                            <textarea class="textarea" name="contenu_texte" id="contenu_texte" placeholder="Ceci est ...."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="audioFields" style="display:none;">
                            <div class="field is-horizontal">
                                <div class="field-label is-normal">
                                    <label class="label">Titre:</label>
                                </div>
                                <div class="field-body">
                                    <div class="field">
                                        <div class="control">
                                            <input class="input" type="text" name="titre_prod" id="titre_prod" placeholder="Titre">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="field is-horizontal">
                                <div class="field-label is-normal">
                                    <label class="label">Description:</label>
                                </div>
                                <div class="field-body">
                                    <div class="field">
                                        <div class="control">
                                            <textarea class="textarea" name="description_prod" id="description_prod" placeholder="Ceci est une description..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="columns is-centered">

                                <div class="column is-2">
                                    <div class="field">
                                        <div class="file is-link is-centered has-name is-boxed">
                                            <label for="audio" class="file-label">
                                                <input class="file-input" type="file" name="audio" id="audio">
                                                <span class="file-cta">
                                                    <span class="file-icon">
                                                        <i class="fas fa-cloud-upload-alt"></i>
                                                    </span>
                                                    <span class="file-label">
                                                        Fichier audio:
                                                    </span>
                                                </span>
                                                <span class="file-name">
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <label class="columns is-centered" for="payant">Payant:&nbsp
                <input type="checkbox" name="payant" id="payant" onchange="showPrice()">
            </label>

            <div class="columns is-centered">
                <div class="field is-horizontal">
                    <div id="priceField" style="display:none;">
                        <div class="field-label is-normal">
                            <label for="prix" class="label">Prix:</label>
                        </div>

                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <div>
                                        <label class="radio">
                                            <span>
                                                <div class="field has-addons has-addons-centered">
                                                    <p class="control">
                                                        <span class="select">
                                                            <select>
                                                                <option>$</option>
                                                            </select>
                                                        </span>
                                                    </p>
                                                    <input class="input" type="number" name="prix" id="prix" step="0.01"
                                                        placeholder="10.00">
                                                </div>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="has-text-centered">
            <button class="button is-success is-dark" type="submit">Publier</button>
        </div>
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