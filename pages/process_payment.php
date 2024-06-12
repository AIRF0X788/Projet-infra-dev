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

if (isset($_GET['prix']) && isset($_GET['id'])) {
    $totalPrice = $_GET['prix'];
    $post_id = $_GET['id'];
} else {
    die("Prix ou ID de publication manquant.");
}

$user_id = $_SESSION['user_id'];    

$payer = new Payer();
$payer->setPaymentMethod('paypal');

$amount = new Amount();
$amount->setTotal($totalPrice);
$amount->setCurrency('EUR');

$transaction = new Transaction();
$transaction->setAmount($amount);
$transaction->setDescription("Achat de publication $post_id");

$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl('https://soundsphere/pages/success.php')
    ->setCancelUrl('https://soundsphere/pages/cancel.php');

$payment = new Payment();
$payment->setIntent('sale')
    ->setPayer($payer)
    ->setTransactions([$transaction])
    ->setRedirectUrls($redirectUrls);

try {
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $database = "infra/dev";
    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Erreur de connexion à la base de données: " . $conn->connect_error);
    }

    $payment->create($apiContext);
    $approvalUrl = $payment->getApprovalLink();

    $paymentId = $payment->getId();
    $stmt = $conn->prepare("INSERT INTO achats (user_id, id_publication, prix, date_achat) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $user_id, $post_id, $totalPrice);
    $stmt->execute();

    $stmt->close();
    $conn->close();
    header('Location: ' . $approvalUrl);
    exit;
} catch (Exception $ex) {
    echo "Une erreur s'est produite lors de la création du paiement PayPal: " . $ex->getMessage();
    header("Location: cancel.php");
    exit;
}
?>
