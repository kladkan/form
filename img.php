<?php //к уроку PHP и HTML в этом же уроке есть реализация капчи(см. видео)
    ini_set('display_errors', 1);
    ini_set('errors_reporting', E_ALL);
    $code = random_int(1000, 9999);

    //Код генерации картинки
    $image = imagecreatetruecolor(600, 600);

    //RGB
    $backColor = imagecolorallocate($image, 123, random_int(1, 255), random_int(1, 255));
    $textColor = imagecolorallocate($image, 0, random_int(1, 255), random_int(1, 255));

    $boxFile = __DIR__ . '/present.png';

    if (!file_exists($boxFile)) {
        echo 'Файл с картинкой не найден';
        exit;
    }

    $imBox = imagecreatefrompng($boxFile);

    imagefill($image, 0, 0, $backColor);
    imagecopy($image, $imBox, 10, 10, 0, 0, 256, 256);

    $fontFile = __DIR__ . '/font.ttf';
    if (!file_exists($fontFile)) {
        echo 'Файл со шрифтом не найден!';
        exit;
    }

    imagettftext($image, 50, 0, 50, 200, $textColor, $fontFile, $code);
    header('Content-Type: image/png');

    imagepng($image);//после этой строки в браузер уходит картинка
    // не имеет никакого значения что происходит тут
    //imagedestroy($image)
?>