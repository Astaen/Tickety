<?php
header('Content-Type: text/html; charset=utf8');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
</head>
<body>
<strong>Bonjour, <?= $fname; ?> !</strong>
<p>Merci d'avoir acheté ton billet pour un évenement de la PonySouth.</p>
<p>N'oublie pas d'imprimer cet email avant le jour J !</p>
<p>Retrouve tous les détails du billet ci-dessous.</p>

<table style="width: 100%; background: #f8f8f8; border: 1px solid #eaeaea; text-align: center;">
	<tr>
		<td>
			<h2><?= $fname . " " . strtoupper($lname); ?></h2>
			<img src="<?= TY_URL.'temp/'.md5($token); ?>.png" />
		</td>
	</tr>
	<tr>
		<td>
			<p><?= $_SESSION['ticket']->name; ?></p>
		</td>
	</tr>
</table>
</body>
</html>