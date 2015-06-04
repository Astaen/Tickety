<?php 
$eventList = $this->getEvents();
if(!sizeof($eventList)) {
	echo '<p>You must create an event first.</p><a href="?page=tickety" class="button">Return</a>';
	exit;
}

?>

<form class="create-ticket" action="admin.php?page=tickety" method="post">
	<h2>Create ticket :</h2>
	<p>Event :</p>
	<input type="hidden" name="create_ticket" value="true">
	<input type="hidden" name="attr-count" id="attr-count" value="0">
	<select name="ticket_event">
	<?php
	foreach ($eventList as $key => $value) {
		echo '<option value="'.$value->id.'">'.$value->name.'</option>';
	}
	?>
	</select>
	<p>Name :</p>
	<input type="text" name="ticket_name">
	<p>Description :</p>
	<textarea name="ticket_desc" cols="30" rows="10"></textarea>
	<p>Short description (appears on ticket) :</p>
	<input type="text" name="ticket_short_desc">	
	<p>Price</p>
	<input type="text" name="ticket_price">
	<p>Discount :</p>
	<input type="number" min="0" max="100" value="0" name="ticket_discount">%
	<!--<p>Attributes (Title : Value)</p>
	<div id="ticket-attributes">
		
	</div>
	<a href="#" id="add-attribute">+Add</a>
	<p>Options :</p>
	<textarea name="ticket_options" cols="30" rows="10"></textarea>-->
	<p>Quantity</p>
	<input type="number" min="0" name="ticket_quantity">
	<?php submit_button("Add"); ?>
</form>
</div>