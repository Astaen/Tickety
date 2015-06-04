<?php
    include(TY_PATH.'phpqrcode/qrlib.php'); 
    include(TY_PATH.'phpqrcode/qrconfig.php'); 

    // how to save PNG codes to server 
     
    $tempDir = TY_PATH.'temp/';
     
    $codeContents = $token;
     
    // we need to generate filename somehow,  
    // with md5 or with database ID used to obtains $codeContents... 
    $fileName = md5($token).'.png'; 
     
    $pngAbsoluteFilePath = $tempDir.$fileName; 
    $urlRelativeFilePath = TY_URL.'temp/'.$fileName; 

    // generating 
    if (!file_exists($pngAbsoluteFilePath)) { 
        QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 10);

    if(!empty($ticket_coupon)) {
        $im = imagecreatefromjpeg(TY_PATH."img/ticket_backer.jpg");
    } else {
        $im = imagecreatefromjpeg(TY_PATH."img/ticket_normal.jpg");
    }
    
    $qr = imagecreatefrompng(TY_PATH.'temp/'.md5($token).'.png');
    // $font = imageloadfont();
    $color = imagecolorallocate($im, 0, 0, 0);
    imagettftext($im, 24, 0, 380, 160, $color, TY_PATH."img/opensans.ttf", "Détails du billet :");
    imagettftext($im, 18, 0, 380, 205, $color, TY_PATH."img/opensans.ttf", $fname . " " . strtoupper($lname));
    imagettftext($im, 18, 0, 380, 235, $color, TY_PATH."img/opensans.ttf", $_SESSION['ticket']->short_desc);
    imagettftext($im, 18, 0, 380, 265, $color, TY_PATH."img/opensans.ttf", $_SESSION['ticket']->name);
    imagecopymerge($im, $qr, 80, 84, 0, 0, 290, 290, 100);
    // imagestring($im, 1, 390, 110, $string, $color);
    imagepng($im, TY_PATH.'temp/'.md5($token).'.png');
    imagedestroy($im);
    }