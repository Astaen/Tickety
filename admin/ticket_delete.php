<?php
$ticketDetails = $this->getTicketDetails($ticket_id)[0];
?>
<p>Do you really want to delete <strong><?= $ticketDetails->name; ?></strong> ?</p>
<form action="?page=tickety" method="post">
	<input type="hidden" name="del_ticket" value="<?=$ticket_id;?>">
	<?php submit_button("Delete"); ?>
</form>
</div><!-- #tickety-wrapper closure -->