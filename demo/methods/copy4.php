<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$tux = new PHPImage('tux.gif');
$image = new PHPImage('php.gif');
// point source : top center ('50%', 0)
// position of point source : top center
// width source : 50% of tux width
// height source : 40% of tux height
$image->copy($tux, 0, 0, '50%', 0, '50%', '40%', 0, '', 'top center');
$image->format = 'png';
$image->display();
?>