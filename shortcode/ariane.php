<?php
switch($_GET['step']) {
	case 'customize':
		$class = Array("", " selected", "", "");
		break;
	case 'checkout':
		$class = Array("", "", " selected", "");
		break;
	case 'done':
		$class = Array("", "", "", " selected");
		break;		
	default:
		$class = Array(" selected", "", "", "");
		break;
}
?>

<div class="ariane">
	<span class="step<?=$class[0];?>">Billetterie</span>
	<span class="step<?=$class[1];?>">Informations</span>
	<span class="step<?=$class[2];?>">Paiement</span>
	<span class="step<?=$class[3];?>">Récapitualif</span>
</div>