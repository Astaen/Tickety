<?php
session_start();

if(isset($_SESSION['success'])) {
	header('Content-Type: application/pdf; Content-Disposition: attachment; filename="Ticket_PonySouth.pdf"');
	session_destroy();
	if(!isset($_GET['token'])) {
		header("Location: /");
	}
	require('fpdf.php');
	$pdf = new FPDF();
	$pdf->AddPage();
	$width = $pdf->w-10;
	$pdf->Image("temp/".md5($_GET['token']).".png",5,5,$width);
	$pdf->Output();
} else {
	die("Session expirée.");
}

?>