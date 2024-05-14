<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

$clientId = 'ASFdIL07qF8HUVG_0bb9SS3IVpfgZt9y923M89o4zAHexvst956mnXlsS0qTnTh-qft1UgY3WQ2fql9W';
$clientSecret = 'EL_VXIt9FilP_QeocwMXo-lzBQreD16VB97dwEHs0ycauaz9cXWlzNgbfNIP8zXlNVl0Q4I80PId5CD8';

$apiContext = new ApiContext(
    new OAuthTokenCredential($clientId, $clientSecret)
);

$apiContext->setConfig(['mode' => 'sandbox']);

if(isset($_GET['prix'])) {
    $totalPrice = $_GET['prix'];
}

$user_id = $_SESSION['user_id'];    
$post_id = $_GET['id'];

if(isset($_GET['prix'])) {
    $prix = $_GET['prix'];
}

$payer = new Payer();
$payer->setPaymentMethod('paypal');

$amount = new Amount();
$amount->setTotal($totalPrice);
$amount->setCurrency('EUR');

$transaction = new Transaction();
$transaction->setAmount($amount);

$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl('http://localhost/xampp/infradev/pages/success.php')
    ->setCancelUrl('http://localhost/xampp/infradev/pages/cancel.php');

$payment = new Payment();
$payment->setIntent('sale')
    ->setPayer($payer)
    ->setTransactions([$transaction])
    ->setRedirectUrls($redirectUrls);

try {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "infra/dev";
    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Erreur de connexion à la base de données: " . $conn->connect_error);
    }

    $sql = "INSERT INTO achats (user_id, id_publication, prix, date_achat) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $user_id, $post_id, $prix);
    $stmt->execute();

    $payment->create($apiContext);
    header('Location: ' . $payment->getApprovalLink());
    exit;
} catch (Exception $ex) {
    echo "Une erreur s'est produite lors de la création du paiement PayPal: " . $ex->getMessage();
    header("Location: cancel.php");
    exit;
}
?>
