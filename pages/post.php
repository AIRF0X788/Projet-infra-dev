<?php session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Publication</title>
    <link rel="stylesheet" type="text/css" href="../css/post.css">
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.0/css/bulma.min.css">
</head>

<body>
    <nav class="navbars">
        <ul class="navbar__menu">
            <li class="navbar__item">
                <a href="main.php" class="navbar__link"><i class="fa fa-home"></i><span>Home</span></a>
            </li>
            <li class="navbar__item">
                <a href="profil.php" class="navbar__link"><i class="fa fa-address-card"></i><span>Voir mon
                        profil</span></a>
            </li>
            <li class="navbar__item">
                <a href="achat.php" class="navbar__link"><i class="fa fa-cart-plus"></i><span>Voir mes achats</span></a>
            </li>
            <li class="navbar__item">
                <a href="prods.php" class="navbar__link"><i class="fa fa-headphones"></i><span>Prods</span></a>
            </li>
            <li class="navbar__item">
                <a href="texte.php" class="navbar__link"><i class="fa fa-file-text-o"></i><span>Textes</span></a>
            </li>
        </ul>
    </nav>
    <form method="post" action="traitement_publication.php" enctype="multipart/form-data">
        <section class="section">
            <div class="container">
                <p class="has-text-black">Nouvelle Publication</p>
                <hr>
                <div class="columns is-centered">
                    <div class="column is-8">
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label for="type_publication" class="label has-text-black">Type de publication:</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <div class="control">
                                        <div class="select">
                                            <select name="type_publication" id="type_publication" onchange="showFields()">
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
                                    <label class="label has-text-black">Titre:</label>
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
                                    <label class="label has-text-black">Description:</label>
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
                                    <label class="label has-text-black">Contenu du Texte:</label>
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
                                    <label class="label has-text-black">Titre:</label>
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
                                    <label class="label has-text-black">Description:</label>
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
                                                    <span class="file-label">Fichier audio:</span>
                                                </span>
                                                <span class="file-name"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label class="label has-text-black">Genre Musical:</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <div class="control">
                                        <div class="select">
                                            <select name="genre_musical" id="genre_musical">
                                                <option value="Rock">Rock</option>
                                                <option value="Pop">Pop</option>
                                                <option value="Hip-Hop">Hip-Hop</option>
                                                <option value="Jazz">Jazz</option>
                                                <option value="Classical">Classical</option>
                                                <option value="Electronic">Electronic</option>
                                                <option value="Reggae">Reggae</option>
                                                <option value="Country">Country</option>
                                                <option value="Blues">Blues</option>
                                                <option value="Soul">Soul</option>
                                                <option value="Metal">Metal</option>
                                                <option value="Punk">Punk</option>
                                                <option value="Folk">Folk</option>
                                                <option value="Disco">Disco</option>
                                                <option value="Funk">Funk</option>
                                                <option value="R&B">R&B</option>
                                                <option value="Gospel">Gospel</option>
                                                <option value="Latin">Latin</option>
                                                <option value="Reggaeton">Reggaeton</option>
                                                <option value="Ska">Ska</option>
                                            </select>
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
            <label class="columns is-centered has-text-black" for="payant">Payant:&nbsp
                <input type="checkbox" name="payant" id="payant" onchange="showPrice()">
            </label>

            <div class="columns is-centered">
                <div class="field is-horizontal">
                    <div id="priceField" style="display:none;">
                        <div class="field-label is-normal">
                            <label for="prix" class="label has-text-black">Prix:</label>
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
                                                    <input class="input" type="number" name="prix" id="prix" step="0.01" placeholder="10.00">
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
