<?php
error_reporting(E_ALL & ~E_DEPRECATED);
require '../vendor/autoload.php';

use PayPal\Api\Payment;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

$clientId = 'ASFdIL07qF8HUVG_0bb9SS3IVpfgZt9y923M89o4zAHexvst956mnXlsS0qTnTh-qft1UgY3WQ2fql9W';
$clientSecret = 'EL_VXIt9FilP_QeocwMXo-lzBQreD16VB97dwEHs0ycauaz9cXWlzNgbfNIP8zXlNVl0Q4I80PId5CD8';

$apiContext = new ApiContext(
    new OAuthTokenCredential($clientId, $clientSecret)
);

$apiContext->setConfig(['mode' => 'sandbox']);

$paymentId = $_GET['paymentId'];
$token = $_GET['token'];
$payerId = $_GET['PayerID'];

$message = '';

try {
    $payment = Payment::get($paymentId, $apiContext);

    $conn = new mysqli("localhost", "root", "", "infra/dev");

    if ($conn->connect_error) {
        die("Erreur de connexion à la base de données : " . $conn->connect_error);
    }

    session_start();
    $user_id = $_SESSION['user_id'];

    $transactions = $payment->getTransactions();
    $amount = $transactions[0]->getAmount()->getTotal();

    $sql = "INSERT INTO achats (user_id, payment_id, amount, purchase_date) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $paymentId, $amount);
    $stmt->execute();

    $message = 'Paiement réussi. Merci pour votre achat!';
} catch (Exception $ex) {
    $message = "Une erreur s'est produite lors de la récupération des détails du paiement PayPal: " . $ex->getMessage();
    header("Location: cancel.php");
    exit;
}
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
        <h1 class="font-weight-bold display-4"><?php echo $message; ?></h1>
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-3">Redirection en cours...</p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    setTimeout(function () {
        window.location.href = 'main.php';
    }, 5000);
</script>

</body>
</html>
