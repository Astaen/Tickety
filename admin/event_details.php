<?php
$eventDetails = $this->getEventDetails($event_id);
$ticketsList = $this->getTicketsByEvent($event_id);
$buyersList = $this->getBuyers($event_id);
$this->showNotice();
?>

<!-------------- EVENT DESC -------------------->

<div class="event-details">
	<p class="event-name"><?= $eventDetails->name; ?></p>
	<p class="event-dates">Start : <?= $eventDetails->date_start; ?> / End : <?= $eventDetails->date_end; ?></p>
</div>

<!-------------- TICKETS AVAILABLE -------------------->
<hr/>
<h2>Tickets available : </h2>

<?php
if(!sizeof($ticketsList)) {
	echo "Aucun billet";
}

$total = 0;
$sold_qty = 0;
foreach ($ticketsList as $key => $value) {
$sold_qty += $value->sold_qty;
if($value->enabled) {
	$state = "Activé";
	$state_verb = "Désactiver";
	$state_class = "enabled";
} else {
	$state = "Désactivé";
	$state_verb = "Activer";
	$state_class = "disabled";
}
?>
<div class="ticket-details <?= $state_class; ?>">
	<p class="ticket-name"><?= $value->name; ?></p>
	<p class="ticket-price"><?= $value->price . "€"; ?></p>
	<p class="ticket-qty"><?= "Vendus: ".$value->sold_qty." | Restant: ".$value->available_qty; ?></p>
	<a class="button" href="?page=tickety&edit_ticket=<?= $value->id; ?>">Editer</a>
	<a class="button" href="?page=tickety&toggle_ticket=<?= $value->id; ?>&state=<?= !$value->enabled; ?>"><?= $state_verb; ?></a>
	<a class="button" href="?page=tickety&del_ticket=<?= $value->id; ?>">Supprimer</a>	
</div>
<?php
}
?>
<p><strong><?= $sold_qty; ?></strong> tickets vendus.</p>

<!-------------- TICKETS SOLD -------------------->
<hr/>
<h2>Tickets sold : </h2>
<?php

if(!sizeof($buyersList)) {
	echo "Aucun billet acheté";
}

?>
<table class="buyer-details">
	<thead>
	<tr>
		<th>Nom / Prénom</th>
		<th>Adresse</th>
		<th>Ville</th>
		<th>Ticket</th>
		<th>Coupon</th>
		<th>Payé</th>
	</tr>
	</thead>
	<tbody class="page_1">
	<?php
	$count = 0;
	$page = 1;
	foreach ($buyersList as $key => $buyer) {
	$total += $buyer->total;
	$ticket = $this->getTicketDetails($buyer->ticket_id);
	?>
		<tr id="buyer_<?= $buyer->id; ?>">
			<td><?= $buyer->fname . " " . strtoupper($buyer->lname); ?></td>
			<td><?= $buyer->adress; ?></td>
			<td><?= $buyer->p_code . " " . $buyer->city; ?></td>
			<td><?= $ticket->name; ?></td>
			<td><?= empty($buyer->ticket_coupon) ? "Non" : "Oui"; ?></td>
			<td><?= $buyer->total . " €"; ?></td>
		</tr>
	<?php
	$count++;
	if($count >= 20) {
		$page++;
		echo '</tbody><tbody class="page_'.$page.'">';
		$count = 0;
	}	
	}
	?>
	</tbody>
	<tfoot>
	<tr class="total">
		<td colspan="4"></td>
		<td>Total</td>
		<td><?= $total . "€"; ?></td>
	</tr>		
	</tfoot>
</table>

<div id="page_select">
	<?php
	for($i = 1; $i < $page+1; $i++) {
		echo '<span class="page_number">'.$i.'</span>';
	}
	?>
</div>

</div><!-- #tickety-wrapper closure -->