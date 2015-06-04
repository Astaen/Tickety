<link rel="stylesheet" href="<?=TY_URL;?>css/style.css">
<script src="<?=TY_URL;?>js/jquery.js"></script>
<script src="<?=TY_URL;?>js/client.js"></script>

<div id="tickety-wrapper">
	<h1>Tickety Dashboard</h1>
	<?php
	//Clicked on an event's title
	if(isset($_GET['event'])) {
		$event_id = $_GET['event'];
		include(TY_PATH.'admin/event_details.php');
		exit;
	}

	//Clicked on an event's title
	if(isset($_GET['create_ticket'])) {
		include(TY_PATH.'forms/create_ticket_form.php');
		exit;
	}

	//On "create ticket" form submit
	if(isset($_POST['create_ticket'])) {
		// $attributes = Array();
		// for($i = 1; $i <= $_POST['attr-count']; $i++) {
		// 	$attributes[$_POST['attr-name-'.$i]] = $_POST['attr-value-'.$i];
		// }
		// $attributes_string = str_replace('"', '\"', json_encode($attributes));
		if($this->createTicket($_POST['ticket_event'], $_POST['ticket_name'], $_POST['ticket_desc'], $_POST['ticket_short_desc'], $_POST['ticket_price'], $_POST['ticket_discount'], "", "", $_POST['ticket_quantity']) == false) {
			echo "erreur BDD";
		}
		header("Location: /wp-admin/admin.php?page=tickety");
	}

	if(isset($_GET['edit_ticket'])) {
		$ticket_id = $_GET['edit_ticket'];
		include(TY_PATH.'forms/edit_ticket_form.php');
		exit;
	}

	if(isset($_POST['edit_ticket'])) {
		if($this->editTicket($_POST['ticket_id'], $_POST['ticket_event'], $_POST['ticket_name'], $_POST['ticket_desc'], $_POST['ticket_short_desc'], $_POST['ticket_price'], $_POST['ticket_discount'], "", "", $_POST['ticket_quantity']) == false) {
			echo "erreur BDD";
		}
		//header("Location: /wp-admin/admin?page=tickety");
	}	

	//Clicked on an event's title
	if(isset($_GET['create_event'])) {
		include(TY_PATH.'forms/create_event_form.php');
		exit;
	}		

	//On "create event" form submit
	if(isset($_POST['create_event'])) {
		if($this->createEvent($_POST['event_name'], $_POST['event_desc'], $_POST['event_date_s'], $_POST['event_date_e']) == false) {
			echo "erreur BDD";
		}
		header("Location: /wp-admin/admin.php?page=tickety");
	}	

	//Switched an event "off"
	if(isset($_GET['toggle_event']) && isset($_GET['state'])) {
		$event_id = $_GET['toggle_event'];
		$state = $_GET['state'];
		$this->toggleEvent($event_id, $state);
	}

	//Switched a ticket "off"
	if(isset($_GET['toggle_ticket']) && isset($_GET['state'])) {
		$ticket_id = $_GET['toggle_ticket'];
		$state = $_GET['state'];
		$this->toggleTicket($ticket_id, $state);
	}

	//CLicked the "delete" link of an event
	if(isset($_GET['del_event'])) {
		$event_id = $_GET['del_event'];
		include(TY_PATH.'admin/event_delete.php');
		exit;
	}	

	//User confirmed deletion
	if(isset($_POST['del_event'])) {
		$event_id = $_POST['del_event'];
		$this->deleteEvent($event_id);
		header("Location: /wp-admin/admin.php?page=tickety");
	}

	//CLicked the "delete" link of an event
	if(isset($_GET['del_ticket'])) {
		$ticket_id = $_GET['del_ticket'];
		include(TY_PATH.'admin/ticket_delete.php');
		exit;
	}	

	//User confirmed deletion
	if(isset($_POST['del_ticket'])) {
		$ticket_id = $_POST['del_ticket'];
		$this->deleteTicket($ticket_id);
	}	
	
	//If a notice message is set, display it
	if(!empty($this->notice)) {
		$this->showNotice();
	}
	?>
	
	<!-- EVENTS SECTION -->
	<section id="events">
		<hr/>
		<h2>Events</h2>
		<a class="button-primary create" role="create-ticket" href="?page=tickety&create_ticket">Create ticket</a>		
		<a class="button-primary create" role="create-event" href="?page=tickety&create_event">Create event</a>
		<?php

		//Get all events.
		$eventList = $this->getEvents();

		//If there are no events.
		if(!sizeof($eventList)) {
			echo '<p>No event yet. Start by <a href="?page=tickety&create_event">creating one</a> !</p>';
		}

		//Display all events.
		foreach ($eventList as $key => $value) {
			if($value->enabled) {
				$state = "Activé";
				$state_verb = "Désactiver";
				$state_class = "enabled";
			} else {
				$state = "Désactivé";
				$state_verb = "Activer";
				$state_class = "disabled";
			}
			echo '<div class="event '.$state_class.'" id="event_'.$value->id.'">
					<p class="event-name">'.($key+1).'. <a href="?page=tickety&event='.$value->id.'">'.$value->name.'</a> ('.$state.')</p>
					<p class="event-desc">'.$value->description.'</p><br/>
					<a class="button" href="?page=tickety&toggle_event='.$value->id.'&state='.!$value->enabled.'">'.$state_verb.'</a>
					<a class="button" href="?page=tickety&del_event='.$value->id.'">Supprimer</a>
				</div>';
		}
		?>		
	</section>

	<!-- TICKETS SECTION -->
	<section id="tickets">
		<hr/>
		<h2>Last tickets bought</h2>
		<a class="button-primary create" role="add-ticket">Add ticket</a>			
	</section>
</div>