<?php
$eventDetails = $this->getEventDetails($event_id)[0];
?>
<p>Do you really want to delete <strong><?= $eventDetails->name; ?></strong> ?</p>
<form action="?page=tickety" method="post">
	<input type="hidden" name="del_event" value="<?=$event_id;?>">
	<?php submit_button("Delete"); ?>
</form>
</div><!-- #tickety-wrapper closure -->