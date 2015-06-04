<?php
session_start();
include(TY_PATH."paypal.php");
$lng = $atts['lang'];
$str['fr'] = array(
	'debug' => "La langue est : français",
	'no_ticket' => "Aucun billet en vente pour l'instant.",
	'select_ticket' => "Selectionner"
);

?>

<link rel="stylesheet" href="<?= TY_URL; ?>css/order.css">
<div id="tickety-wrapper">
	<?php
	include("ariane.php");

	if(isset($_GET['step'])) {
		switch ($_GET['step']) {
			case 'customize':
				include(TY_PATH."order/customize.php");
				break;
			case 'checkout':
				include(TY_PATH."order/checkout.php");
				break;
			case 'done':
				include(TY_PATH.'order/summary.php');
				break;
			case 'get_pdf':
				include(TY_PATH.'order/pdf.php');
				break;				
			case 'cancel':
			case 'clear':
				session_destroy();
				header("Location: ".$_SERVER['REDIRECT_URL']);
				break;
		}		
	} else {
		//Retrieve all tickets that are enabled for sale
		$eventsList = $this->getEnabledEvents();
		$ticketsList = $this->getEnabledTickets();

		//If there are tickets ...
		if(sizeof($ticketsList)) {
			include("ticketing.php");
		} else {
			echo '<p class="noticket">'.$str[$lng]['no_ticket'].'</p>';
		}		
	}

	?>
</div>