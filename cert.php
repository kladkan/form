<?php
  $i = imageCreate(600, 300);
  $color = imageColorAllocate($i, 117, 193, 255);
  $textColor = imagecolorallocate($i, 120, 100, 50);
  imageFilledRectangle($i, 0, 0, imageSX($i), imageSY($i), $color);
  $fontFile = __DIR__ . '/font.ttf';
    if (!file_exists($fontFile)) {
        echo 'Файл со шрифтом не найден!';
        exit;
    }
  imagettftext($i, 14, 0, 50, 100, $textColor, $fontFile, $_GET['param1']);
  imagettftext($i, 14, 0, 50, 140, $textColor, $fontFile, $_GET['param2']);
  Header("Content-type: image/png");
  imagepng($i);
  imageDestroy($i);
?>