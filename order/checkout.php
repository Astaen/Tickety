<?php

if(!isset($_SESSION['details'])) {
	$_SESSION['details'] = $_POST;
}

//Get details on the choosen ticket
$ticket = $_SESSION['ticket'];
$details = $_SESSION['details'];
$final_price = $ticket->price;

foreach ($details as $key => $value) {
	if(empty($value)) {
		if($key != "coupon") {
			$_SESSION['err'] .= "<p>Merci de remplir tous les champs.</p>";
			header("Location: ".$_SERVER['REDIRECT_URL']."?step=customize");			
		}	
	}
}

if(!empty($details['coupon'])) {
	$_SESSION['coupon'] = $coupon = $this->getCouponDetails($details['coupon']);
	if($coupon[0]->discount >= $ticket->price) {
		$free_ticket = true;
		$discount = $ticket->price;
		$final_price = 0;
	} elseif($coupon[0]->discount > 0) {
		$final_price = $ticket->price - $coupon[0]->discount;
		$discount = $coupon[0]->discount;
	} else {
		$discount = 0;
	}
	if(!sizeof($coupon)) {
		$_SESSION['err'] .= "<p>Coupon invalide.</p>";
		header("Location: ".$_SERVER['REDIRECT_URL']."?step=customize");
	}
}

// $options = $_SESSION['ticket_options'];
// unset($options['step']);
// unset($options['submit']);
// $_SESSION['ticket_options'] = $options;


/*
*
*
* Paypal Magic
*
*
*/
if(!$free_ticket) {
	$paypal = new Paypal();

	$params = array(
		'RETURNURL' => 'http://'.$_SERVER['HTTP_HOST'].'/billetterie?step=done',
		'CANCELURL' => 'http://'.$_SERVER['HTTP_HOST'].'/billetterie?step=cancel',
		'PAYMENTREQUEST_0_ITEMAMT' => $final_price,
		'PAYMENTREQUEST_0_SHIPPINGAMT' => 0,
		'PAYMENTREQUEST_0_AMT' => $final_price,
		'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR',
		'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
		'SOLUTIONTYPE' => 'Sole',
		'PAGESTYLE' => 'PonySouth'
	);

	$params["L_PAYMENTREQUEST_0_NAME0"] = $ticket->name;
	$params["L_PAYMENTREQUEST_0_DESC0"] = '';
	$params["L_PAYMENTREQUEST_0_AMT0"] = $final_price;
	$params["L_PAYMENTREQUEST_0_QTY0"] = 1;


		$response = $paypal->request('SetExpressCheckout', $params);


	if($response) {
		if($paypal->prod) {
			$paypal_url = 'https://www.paypal.com/webscr?cmd=_express-checkout&token=' . $response['TOKEN'];
		} else {
			$paypal_url = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=' . $response['TOKEN'];
		}
	} else {
		var_dump($paypal->errors);
		die();
	}
}
/*
*
*
* End of Paypal Magic
*
*
*/

?>
<div class="summarized">
	<h2>Ticket choisi :</h2>
	<div class="ticket-name"><?= $ticket->name; ?></div>
	<div class="ticket-desc"><?= $ticket->description; ?></div>
	<hr/>
	<?php
	// TODO
	// if(sizeof($options)) {
	// 	echo "<hr/>
	// 		<h2>Options :</h2>
	// 	<ul>";
	// 	foreach ($options as $key => $value) {
	// 		echo '<li class="option">'.$key.' : '.$value.'</li>';
	// 	}
	// 	echo "</ul>";
	// }
	if($coupon) {
		echo "<h2>Coupon :</h2><p>Coupon appliqué : <ul>";
		foreach ($coupon as $key => $perk) {
			echo "<li>".$perk->name."</li>";
		}
		echo "</ul></p><hr/>";
	}
	?>
	<h2>Détails :</h2>
	<p><?= $details['fname'] . " " . strtoupper($details['lname']); ?></p>
	<p><?= $details['adress']; ?></p>
	<p><?= $details['p_code'] . " " . strtoupper($details['city']) . ", " . strtoupper($details['country']); ?></p>
	<hr/>

	<form method="post" action="?step=customize">
		<input type="hidden" name="ticket_id" value="<?= $ticket->id; ?>">
		<input type="submit" value="Modifier">
	</form>	
</div>

<?php
if($discount) {
	echo '<p style="text-align: right;"><strong>Remise de '.$discount.' euros.</strong></p>';
}

if($free_ticket) {
	$_SESSION['free_ticket'] = true;
	echo '<a class="buy" href="?step=done">Obtenir votre ticket ('.$final_price.'€)</a>';
} else {
	echo '<a class="buy" href="'.$paypal_url.'">Valider la commande ('.$final_price.'€)</a>';
}

$_SESSION['ticket']->price = $final_price;
?>
