<?php
ob_start();
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if($_SESSION['free_ticket']) {
	$response = true;
	unset($_SESSION['free_ticket']);
} else {
	/* Récupération des infos de paiement */
	$paypal = new Paypal();
	$response = $paypal->request('GetExpressCheckoutDetails', Array(
		'TOKEN' => $_GET['token']
	));

	if($response) {
		if($response['CHECKOUTSTATUS'] == 'PaymentActionCompleted') {
			header("Location: /");
		}
	} else {
		session_destroy();
		header("Location: ".$_SERVER['REDIRECT_URL']);
	}

	$params = Array(
		'TOKEN' => $response['TOKEN'],
		'PAYERID' => $response['PAYERID'],
		'PAYMENTREQUEST_0_AMT' => $response['AMT'],
		'PAYMENTREQUEST_0_CURRENCYCODE' => $response['CURRENCYCODE'],
		'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
		'PAYMENTREQUEST_0_ITEMAMT' => $response['ITEMAMT'],
		'PAYMENTREQUEST_0_SHIPPINGAMT' => $response['SHIPPINGAMT'],
	);

	$params["L_PAYMENTREQUEST_0_NAME0"] = $_SESSION['ticket']->name;
	$params["L_PAYMENTREQUEST_0_DESC0"] = '';
	$params["L_PAYMENTREQUEST_0_AMT0"] = $_SESSION['ticket']->price;
	$params["L_PAYMENTREQUEST_0_QTY0"] = 1;

	$response = $paypal->request('DoExpressCheckoutPayment', $params);
}

if($response) {
	$fname = $_SESSION['details']['fname'];
	$lname = $_SESSION['details']['lname'];
	$adress = $_SESSION['details']['adress'] . ", " . $_SESSION['details']['country'];
	$city = $_SESSION['details']['city'];
	$p_code = $_SESSION['details']['p_code'];
	$mail = $_SESSION['details']['mail'];
	$ticket_id = $_SESSION['ticket']->id;
	$ticket_options = ""; //str_replace('"', '\"', json_encode($_SESSION['ticket_options']));
	$ticket_coupon = $_SESSION['details']['coupon'];
	$total = $_SESSION['ticket']->price;
	$_SESSION['success'] = true;

	//Creating ticket
	$token = generateRandomString(6);
	include(TY_PATH.'order/qr_gen.php');

	//Email
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";	
	$headers .= 'From: PonySouth <contact@ponysouth.fr>' . "\r\n";
	include(TY_PATH.'mail/ticket.php');
	$mail_content = ob_get_clean();
	wp_mail($mail, "Votre ticket PonySouth", $mail_content, $headers);
	$this->addBuyer($fname, $lname, $adress, $city, $p_code, $mail, $ticket_id, $ticket_coupon, $ticket_options, $token, $total);
	$this->useCoupon($ticket_coupon);
} else {
	var_dump($paypal->errors);
}
?>

	<p>Merci d'avoir choisi de participer à un évenement PonySouth !</p>
	<strong>Les détails du billet ont été envoyés à <?= $_SESSION['details']['mail']; ?>, pensez à l'imprimer et à l'avoir en votre possession le jour J !</strong>
	<div class="ticket_final">
		<h3>Votre billet :</h3>
		<img src="<?= TY_URL.'temp/'.md5($token); ?>.png" />
		<a target="_blank" href="<?= TY_URL; ?>pdf.php?token=<?= $token; ?>" title="Imprimer">Version PDF imprimable</a>
	</div>
</div>