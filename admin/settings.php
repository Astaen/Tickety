<link rel="stylesheet" href="<?=TY_URL;?>css/style.css">
<script src="<?=TY_URL;?>js/jquery.js"></script>
<script src="<?=TY_URL;?>js/client.js"></script>
<?php
if(isset($_POST['submit'])) {
	var_dump($_POST);
	if($_POST['is_dev'] == "true") {
		var_dump($this->setPaypalCreds($_POST['paypal_devusr'], $_POST['paypal_devpwd'], $_POST['paypal_devsign'], true));	
	} else {
		var_dump($this->setPaypalCreds($_POST['paypal_usr'], $_POST['paypal_pwd'], $_POST['paypal_sign'], false));
	}
}
$pp = $this->paypal_settings;
var_dump($pp);

?>
<div id="tickety-wrapper">
	<h1>Tickety Settings</h1>
	<hr/>
	<h2>Paypal Credentials</h2>
	<form id="paypal_cred" action="?page=tickety_settings" method="post">
		<p>User :</p>
		<input type="text" name="paypal_usr" value="<?= $pp['paypal_usr']; ?>">
		<p>Password :</p>
		<input type="text" name="paypal_pwd" value="<?= $pp['paypal_pwd']; ?>">
		<p>Signature :</p>
		<input type="text" name="paypal_sign" value="<?= $pp['paypal_sign']; ?>">
		<input type="submit" name="submit" class="button-primary" value="Save">
	</form>

	<hr/>
	<input type="checkbox" id="use_dev" <?php if($pp['sandbox']) { echo "checked"; } ?>>
	<label for="use_dev">Use sandbox credentials.</label>

	<form id="paypal_cred" class="greyed" action="?page=tickety_settings" method="post">
		<p>User (sandbox) :</p>
		<input type="text" name="paypal_devusr" value="<?= $pp['paypal_devusr']; ?>" disabled>
		<p>Password (sandbox) :</p>
		<input type="text" name="paypal_devpwd" value="<?= $pp['paypal_devpwd']; ?>" disabled>
		<p>Signature (sandbox) :</p>
		<input type="text" name="paypal_devsign" value="<?= $pp['paypal_devsign']; ?>" disabled>
		<input type="hidden" name="is_dev" value="true" disabled />	
		<input type="submit" name="submit" class="button-primary" value="Save" disabled>
	</form>

</div>
