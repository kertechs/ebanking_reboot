<?php
session_start();
header ("Content-type: image/png");
$font = dirname(__FILE__) . "/arial.ttf";
$tab = array (
    1,2,3,4,5,6,7,8,9,0,
    'p','a','s','w','o','r','d',
    'x','y','z','w','v','u','q','b',
    '','','','','','','','','',''
);
$tab2 = array();

shuffle($tab);
$_SESSION["pad_tab"] = $tab; //On met en session le claviers généré (du moins les "coordonnées")

$cellwidth = 30;
$width = $height = ceil(sqrt(count($tab)))*$cellwidth;
/*error_log('count($tab) : ' . count($tab));
error_log('sqrt(count($tab)) : ' . sqrt(count($tab)));
error_log('ceil(sqrt(count($tab))) : ' . ceil(sqrt(count($tab))));*/

$image = imagecreatetruecolor($width,$height);

$white = imagecolorallocate($image, 255, 255, 255);
$grey = imagecolorallocate($image, 128, 128, 128);
$black = imagecolorallocate($image, 0, 0, 0);
$red = imagecolorallocate($image, 255, 0, 0);
$blue = imagecolorallocate($image, 0, 0, 255);
imagefilledrectangle($image, 0, 0, $width-1, $height-1, $white);


foreach ($tab as $i => $val)
{
    if ($val)
    {
        $tab2[$i] = hash('sha256', $val);
    }
    if ($i%($width/$cellwidth) == 0)
    {
        $x = 9 + $cellwidth*(floor($i/($width/$cellwidth)));
    }
    $y = 22 + $cellwidth*($i%($width/$cellwidth));
    imagettftext($image, 16, 0, $x+4, $y+4, $grey, $font, $tab[$i]);
    imagettftext($image, 16, 0, $x-2, $y-2, $blue, $font, $tab[$i]);
}
//error_log('$tab2 = '.print_r($tab2, true));

for ($i=0;$i<($width/$cellwidth - 1);$i++)
{
    imageline ($image, 0, $cellwidth + $cellwidth*$i, $width, $cellwidth + $cellwidth*$i, $black);
    imageline ($image, $cellwidth * ( 1 + $i), 0, $cellwidth * (1 + $i), $height, $black);
}

imagepng($image);
imagedestroy($image);
?>
