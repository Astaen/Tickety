<?php
if(!sizeof($_POST) && !sizeof($_SESSION)) {
	header("Location: /");
	die();
}

$edit = false;
if(isset($_SESSION['details'])) {
	$edit = true;
	$details = $_SESSION['details'];
	unset($_SESSION['details']);
}

if(!isset($_SESSION['ticket'])) {
	$_SESSION['ticket'] = $this->getTicketDetails($_POST['ticket_id']);
}

// $options = json_decode($ticket->options);

// echo $beforeHTML;

// if(sizeof($options)) {
// 	foreach ($options as $opt) {
// 		switch ($opt->type) {
// 			case 'select':
// 				echo '<p>'.$opt->name.'</p><select name="'.strtolower($opt->name).'">';
// 				foreach (explode(",", $opt->values) as $value) {
// 					echo '<option value="'.$value.'">'.$value.'</option>';
// 				}
// 				echo '</select>';
// 				break;
// 			default:
// 				# code...
// 				break;
// 		}

// 	}
// 	echo "<hr/>";
// }

if(!empty($_SESSION['err'])) {
	echo '<span class="error">'.$_SESSION['err'].'</span>'; 
	$_SESSION['err'] = "";
}
?>
<form method="post" action="?step=checkout">
	<p>Prénom :</p>
	<input type="text" name="fname" <?= $edit ? 'value="'.$details['fname'].'"' : ""; ?> required>
	<p>Nom :</p>
	<input type="text" name="lname" <?= $edit ? 'value="'.$details['lname'].'"' : ""; ?> required>
	<p>E-Mail :</p>
	<input type="email" name="mail" <?= $edit ? 'value="'.$details['mail'].'"' : ""; ?> required>	
	<p>Adresse :</p>
	<textarea name="adress" id="adress" cols="30" rows="10" required><?= $edit ? $details['adress'] : ""; ?></textarea>
	<p>Ville :</p>
	<input type="text" name="city" <?= $edit ? 'value="'.$details['city'].'"' : ""; ?> required>
	<p>Code postal :</p>
	<input type="text" name="p_code" <?= $edit ? 'value="'.$details['p_code'].'"' : ""; ?> required>
	<p>Pays :</p>
	<input type="text" name="country" <?= $edit ? 'value="'.$details['country'].'"' : ""; ?> required>	
	<div class="coupon-box">
		<label for="coupon">Coupon : </label>
		<input type="text" name="coupon" id="coupon" placeholder="(facultatif)" <?= $edit ? 'value="'.$details['coupon'].'"' : ""; ?>>
	</div>	
	<?php submit_button("Envoyer"); ?>
</form>