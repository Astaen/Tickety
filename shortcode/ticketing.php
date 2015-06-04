<?php
/*
*	Previously, in "index.php" ...
*	$ticketsList = $this->getEnabledTickets();
*	$eventsList = $this->getEnabledEvents();
*/
if(isset($_SESSION['ticket'])) {
	unset($_SESSION['ticket']);
}

$_SESSION['err'] = "";
$beforeHTML = '<hr/><h2>Presse</h2><p>Vous souhaitez obtenir une accréditation presse ?</p>
<p>Le dossier à remplir se trouve à <a href="http://ponysouth.fr/wp-content/uploads/2015/04/DossierPresse_PonySouth.pdf">cette adresse</a> et doit être renvoyé avant le 30 Juin 2015.</p>';

foreach ($eventsList as $event_key => $event) {
	echo '<div class="event_'.$event->id.'"><h2>'.$event->name.'</h2><hr/>';
	foreach ($ticketsList as $ticket_key => $ticket) {
		if($ticket->event_id == $event->id) {
?>
			<div class="ticket">
				<p class="ticket-name"><?=$ticket->name; ?></p>
				<p class="ticket-desc"><?=$ticket->description; ?></p>
				<p class="ticket-price"><?=$ticket->price."€"; ?></p>
				<form method="post" action="?step=customize">
					<input type="hidden" name="ticket_id" value="<?= $ticket->id; ?>">
					<?php submit_button("Selectionner"); ?>
				</form> 
			</div>
<?php
		}
	}
	echo "</div>";
}

echo $beforeHTML;
?>