<?php
    $pdo = new PDO("mysql:host=localhost; dbname=ayakovlev; charset=utf8","ayakovlev","neto1880");
    $stmt = $pdo->prepare("CREATE TABLE `admins` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `login` varchar(50) NOT NULL,
        `password` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $stmt->execute();
   

    $stmt = $pdo->prepare("CREATE TABLE `questions` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `theme_id` int(11) NOT NULL,
        `question` varchar(5000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        `answer` varchar(5000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        `answered` int(11) NOT NULL DEFAULT '0',
        `published` int(11) NOT NULL DEFAULT '0',
        `hidden` int(11) NOT NULL DEFAULT '0',
        `author_name` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        `e-mail` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $stmt->execute();

    $stmt = $pdo->prepare("CREATE TABLE `themes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `theme` varchar(100) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $stmt->execute();
    
    if ($stmt->execute()) {
        echo 'все нормально';
    } else {
        echo 'не получилоcь создать таблицу';
    }