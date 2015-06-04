<form class="create-event" action="admin.php?page=tickety" method="post">
	<h2>Create event :</h2>
	<p>Name :</p>
	<input type="hidden" name="create_event" value="true">
	<input type="text"  name="event_name">
	<p>Description :</p>
	<input type="text"  name="event_desc">
	<p>Start date :</p>
	<input type="date" name="event_date_s">
	<p>End date :</p>
	<input type="date" name="event_date_e">
	<?php submit_button("Add"); ?>
</form>
</div>