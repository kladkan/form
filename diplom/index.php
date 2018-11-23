<?php
session_start();
if (isset($_GET['exit'])) {
    session_destroy();
    header('Location: ./index.php');
}
//Начальная версия Дипломной работы. Просто поместить этот файл из резервной папки в папку diplom
$pdo = new PDO("mysql:host=localhost; dbname=ayakovlev; charset=utf8","ayakovlev","neto1880");

//Проверка существования таблицы
$get_table = "describe `admins`";
if ($pdo->query($get_table) == FALSE) { //если таблицы с админами нет, то создаем её
    $stmt = $pdo->prepare("CREATE TABLE `admins` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `login` varchar(50) NOT NULL,
        `password` varchar(150) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $stmt->execute();

    //Создаем администратора по умолчанию
    $stmt = $pdo->prepare("INSERT INTO `admins`(`login`, `password`) VALUES (?, ?)");
    $x = 'admin';
    $stmt->bindParam(1, $x);
    $stmt->bindParam(2, $x);
    $stmt->execute();

    //создаем таблицу где будут храниться вопросы и ответы
    $stmt = $pdo->prepare("CREATE TABLE `questions` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `theme_id` int(11) NOT NULL,
        `question` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        `answer` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
        `published` int(11) NOT NULL DEFAULT '0',
        `author_name` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        `e-mail` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $stmt->execute();

    //создаем таблицу с темами
    $stmt = $pdo->prepare("CREATE TABLE `themes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `theme` varchar(100) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $stmt->execute();
}

//Авторизация для администраторов
if (!empty($_POST['authname']) && !empty($_POST['authpass'])) {
    $sql = "SELECT `id` FROM `admins` WHERE `login`='{$_POST['authname']}' AND `password`='{$_POST['authpass']}'";
    foreach ($pdo->query($sql) as $admin) {
    }

    if (!empty($admin['id'])) {
        $_SESSION['adminId'] = $admin['id'];
        $_SESSION['adminLogin'] = $_POST['authname'];

    } else {
        //include 'includes/empty_admin.php';
        echo 'Администратор не найден или неправильно введён логин/пароль!<br>';
        echo '<a href="index.php?admin=admin">Войдите заново</a> или обратитесь к главному Администратору</a>';
        exit;    
    }
}


if (isset($_SESSION['adminLogin'])) {
    //Получение списка администраторов
    if (isset($_GET['listAdmin'])) {
        $sql = "SELECT `login`, `password` FROM `admins`";
        $admins = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        //echo '<pre>'; print_r($admins); echo '</pre>';
    }

    //Добавление администратора
    if (isset($_POST['newLogin']) && isset($_POST['newPassword'])) {
        if (empty($_POST['newLogin']) or empty($_POST['newPassword'])) {
            echo 'Вы пропустили пароль или логин! Вернитесь назад.';
            exit;
        } else {
            //echo '<pre>'; print_r($_POST); echo '</pre>';
            $sql = "SELECT `id` FROM `admins` WHERE `login`='{$_POST['newLogin']}'";
            foreach ($pdo->query($sql) as $admin) {
            }

            if (!empty($admin['id'])) {
                echo 'Логин: '.$_POST['newLogin'].' - занят! Придумайте уникальное имя.<br>';
            } else {
                $stmt = $pdo->prepare("INSERT INTO `admins`(`login`, `password`) VALUES (?, ?)");
                $stmt->bindParam(1, $_POST['newLogin']);
                $stmt->bindParam(2, $_POST['newPassword']);
                $stmt->execute();
                header('Location: ./index.php?listAdmin=listAdmin');
            }
        }
    }

    //Изменение пароля администратора
    if (isset($_POST['changePassword'])) {
        $stmt = $pdo->prepare("UPDATE `admins` SET `password`='{$_POST['changePassword']}' WHERE `login`='{$_GET['login']}' LIMIT 1");
        $stmt->execute();
        header('Location: ./index.php?listAdmin=listAdmin');
    }

    //Удаление администратора
    if (isset($_GET['delAdmin'])) {
        $stmt = $pdo->prepare("DELETE FROM `admins` WHERE `login`='{$_GET['delAdmin']}' LIMIT 1");
        $stmt->execute();
        header('Location: ./index.php?listAdmin=listAdmin');
    }

    //Добавление новой темы
    if (isset($_POST['newTheme'])) {
        $stmt = $pdo->prepare("INSERT INTO `themes` (`theme`) VALUES (?)");
        $stmt->bindParam(1, $_POST['newTheme']);
        $stmt->execute();
        header('Location: ./index.php');
    }

    //Удаление темы со всеми вопросами
    if (isset($_GET['delTheme'])) {
        $stmt = $pdo->prepare("DELETE FROM `questions` WHERE `theme_id`='{$_GET['delTheme']}';
        DELETE FROM `themes` WHERE `id`='{$_GET['delTheme']}'");
        $stmt->execute();
        header('Location: ./index.php');
    }
    
    //Переключатель опубликован/неопубликован
    if (isset($_GET['publishedOnOff']) OR isset($_POST['changeAnswer'])) {
        if (isset($_POST['changeAnswer']) && !isset($_POST['publish'])) {
            $_GET['publishedOnOff'] = 0;
            //$_GET['questionId'] = $_GET['showQuestionId'];
        }
        if (isset($_POST['publish'])) {
            $_GET['publishedOnOff'] = 1;
            //$_GET['questionId'] = $_GET['showQuestionId'];
        }
        $stmt = $pdo->prepare("UPDATE `questions` SET `published`='{$_GET['publishedOnOff']}' WHERE `id`='{$_GET['showQuestionId']}' LIMIT 1");
        $stmt->execute();
        //header('Location: ./index.php?showQuestionsTheme='.$_GET['questionsThemeId']);
    }

    //Удаление вопроса из темы
    if (isset($_GET['delQuestionId'])) {
        $stmt = $pdo->prepare("DELETE FROM `questions` WHERE `id`='{$_GET['delQuestionId']}'");
        $stmt->execute();
        header('Location: ./index.php?showQuestionsTheme='.$_GET['questionsThemeId']);
    }

    //Изменение автора
    if (isset($_POST['changeAuthorName'])) {
        $stmt = $pdo->prepare("UPDATE `questions` SET `author_name`='{$_POST['changeAuthorName']}' WHERE `id`='{$_GET['showQuestionId']}' LIMIT 1");
        $stmt->execute();
    }

    //Изменение вопроса
    if (isset($_POST['changeQuestion'])) {
        $stmt = $pdo->prepare("UPDATE `questions` SET `question`='{$_POST['changeQuestion']}' WHERE `id`='{$_GET['showQuestionId']}' LIMIT 1");
        $stmt->execute();
    }

    //Изменение ответа
    if (isset($_POST['changeAnswer'])) {
        $stmt = $pdo->prepare("UPDATE `questions` SET `answer`='{$_POST['changeAnswer']}' WHERE `id`='{$_GET['showQuestionId']}' LIMIT 1");
        $stmt->execute();
    }

    //Изменение темы
    if (isset($_POST['changeThemeId'])) {
        if (!isset($_GET['unansQuestions'])) {
            $_GET['showQuestionsTheme'] = $_POST['changeThemeId'];
        }
        $stmt = $pdo->prepare("UPDATE `questions` SET `theme_id`='{$_POST['changeThemeId']}' WHERE `id`='{$_GET['showQuestionId']}' LIMIT 1");
        $stmt->execute();
    }

    //Получение всех вопросов без ответа во всех темах в порядке их добавления
    if (isset($_GET['unansQuestions'])) {
        $sql = "SELECT `questions`.`id`, `theme_id`, `theme`, `question`, `answer`, `published`, `author_name`, `e-mail`, `date_added` FROM `questions` JOIN `themes` ON `themes`.`id`=`questions`.`theme_id` WHERE `answer` IS NULL OR `answer`='' ORDER BY `date_added` ASC";
        $allUnansQuestions = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        //echo '<pre>'; print_r($allUnansQuestions); echo '</pre>';
    }
}

//Получение списка тем (для всех)
$sql = "SELECT * FROM `themes`";
$themes = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
//echo '<pre>'; print_r($themes); echo '</pre>';

if (!empty($themes)) {
    foreach ($themes as $theme) {
        //подсчет вопросов в теме
        $sql = "SELECT COUNT(*) as 'Вопросов в теме' FROM `questions` WHERE `theme_id`='{$theme['id']}' GROUP BY `theme_id`";
        $countAllQuesInThemeArray = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if ($countAllQuesInThemeArray) {
            foreach ($countAllQuesInThemeArray as $countAllQues) {
            }
        } else {
            $countAllQues['Вопросов в теме'] = 0;
        }

        //подсчет опубликованных вопросов в теме
        $sql = "SELECT COUNT(*) AS 'Опубликовано вопросов' FROM `questions` WHERE `theme_id`='{$theme['id']}' AND `published`=1 GROUP BY `theme_id`";
        $countPublishedQuesInThemeArray = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if ($countPublishedQuesInThemeArray) {
            foreach ($countPublishedQuesInThemeArray as $countPublishedQues) {
            }
        } else {
            $countPublishedQues['Опубликовано вопросов'] = 0;
        }
        
        //подсчет вопросов без ответа в теме
        $sql = "SELECT COUNT(*) AS 'Вопросов без ответа' FROM `questions` WHERE `theme_id`='{$theme['id']}' AND (`answer` IS NULL OR `answer`='') GROUP BY `theme_id`";
        $countUnansweredQuesInThemeArray = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if ($countUnansweredQuesInThemeArray) {
            foreach ($countUnansweredQuesInThemeArray as $countUnansweredQues) {
            }
        } else {
            $countUnansweredQues['Вопросов без ответа'] = 0;
        }

        //Создаем новый массив и добавляем данные из запросов
        $themes2[] = array('id' => $theme['id'], 'theme' => $theme['theme'], 'Вопросов в теме' => $countAllQues['Вопросов в теме'], 'Опубликовано вопросов' => $countPublishedQues['Опубликовано вопросов'], 'Вопросов без ответа' => $countUnansweredQues['Вопросов без ответа']);
    }
} else {
    $themes2 = 0;
}
//echo '<pre>'; print_r($countPublishedQuesInThemeArray); echo '</pre>';
//echo '<pre>'; print_r($themes2); echo '</pre>';

//Получение списка вопросов в выбранной теме
if (isset($_GET['showQuestionsTheme'])) {
    $sql = "SELECT `questions`.`id`,  `questions`.`theme_id`, `theme`, `question`, `answer`, `date_added`, `published` FROM `questions` JOIN `themes` ON `themes`.`id`=`questions`.`theme_id` WHERE `theme_id`='{$_GET['showQuestionsTheme']}'";
    $questions = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    //echo '<pre>'; print_r($questions); echo '</pre>';
}

//Получение данных вопроса
if (isset($_GET['showQuestionId'])) {
    $sql = "SELECT `questions`.`id`,  `questions`.`theme_id`, `date_added`, `author_name`, `theme`, `question`, `answer`, `published` FROM `questions` JOIN `themes` ON `themes`.`id`=`questions`.`theme_id` WHERE `questions`.`id`='{$_GET['showQuestionId']}'";
    $showQuestion = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    //echo '<pre>'; print_r($showQuestion); echo '</pre>';
}



//Добавление вопроса
if (isset($_POST['question'])) {
    //echo '<pre>'; print_r($_POST); echo '</pre>';
    $stmt = $pdo->prepare("INSERT INTO `questions`(`author_name`, `e-mail`, `theme_id`, `question`) VALUES (?, ?, ?, ?)");
    $stmt->bindParam(1, $_POST['author_name']);
    $stmt->bindParam(2, $_POST['e-mail']);
    $stmt->bindParam(3, $_POST['theme_id']);
    $stmt->bindParam(4, $_POST['question']);
    $stmt->execute();
    //header('Location: ./index.php');
}
//echo '<pre>'; print_r($_GET); echo '</pre>';
//echo '<pre>'; print_r($_POST); echo '</pre>';
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Сервис вопросов и ответов</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    
<!--Панель администратора-->
<?php if (isset($_SESSION['adminLogin'])) : ?>
    <p>Вы вошли как: <?=$_SESSION['adminLogin']?></p>
    <p><a href="index.php?exit=exit">Выход (прекратить администрирование)</a></p>
    <fieldset>
        <legend>Меню администратора:</legend>
        <ul>
            <li><a href="index.php?listAdmin=listAdmin">Список админов</a></li>
            <li><a href="index.php?unansQuestions=unansQuestions">Список вопросов без ответов</a></li>
            <li><a href="index.php?addTheme=addTheme">Добавить тему...</a></li>
        </ul>
    </fieldset>
<?php endif ?>


<!--Для авторизации-->
<?php if (!isset($_SESSION['adminId']) && !isset($_GET['admin'])) : ?>
    <p><a href="index.php?admin=admin">Вход для администраторов</a></p>
<?php endif ?>

<?php if (isset($_GET['admin'])) : ?>
    <form action="index.php" method="POST">
        <fieldset>
            <legend>Вход для администратора</legend>
            <p>Логин: <input type="text" name="authname"></p>
            <p>Пароль: <input type="password" name="authpass"></p>
            <p><input type="submit" value="Войти">
        </fieldset>
    </form>
<?php endif ?>

<!---->

<!--Для авторизованного администратора-->
<?php if (isset($_SESSION['adminLogin'])) : ?>
    <!--Вывод списка администраторов-->
    <?php if (isset($_GET['listAdmin'])) : ?>
    <p>Список администраторов:</p>
    <table width="" border="1" cellpadding="4" cellspacing="0">
        <tr>
            <th>Логин</th>
            <th>Пароль</th>
            <th>Удаление</th>
        </tr>
        <?php foreach ($admins as $row) : ?>    
        <tr>
            <?php foreach ($row as $key => $value) : ?>
                <td>
                    <?= $value?>
                    <?php if ($key == 'password') : ?>
                    <form action="index.php?login=<?=$row['login']?>" method="POST">
                        <input type="text" size="20" name="changePassword">
                        <input type="submit" value="Изменить">
                    </form>
                    <?php endif ?>
                </td>                
            <?php endforeach ?>
                <td><a href="index.php?delAdmin=<?=$row['login']?>">Удалить</a></td>
        </tr>
        <?php endforeach ?>
    </table>
    <p><a href="index.php?listAdmin=listAdmin&addAdmin=addAdmin">Добавить администратора</a></p>
    <?php endif ?>

    <!--Форма для добавления администратора-->
    <?php if (isset($_GET['addAdmin'])) : ?>
        <form action="index.php" method="POST">
            <fieldset>
                <legend>Новый администратор</legend>
                <p>Логин: <input type="text" size="50" name="newLogin"></p>
                <p>Пароль: <input type="text" size="50" name="newPassword"></p>
                <p><input type="submit" value="Добавить"></p>
            </fieldset>
        </form>
    <?php endif ?>

    <!--Форма добавление темы-->
    <?php if (isset($_GET['addTheme'])) : ?>
        <form action="index.php" method="POST">
            <fieldset>
                <legend>Новая тема</legend>
                <p><input type="text" size="70" name="newTheme"></p>
                <p><input type="submit" value="Добавить"></p>
            </fieldset>
        </form>
    <?php endif ?>

<?php endif ?><!--Для авторизованного администратора (конец)-->


<!--Выводим темы для пользователей и для админов-->
<?php if ($themes2 != 0) : ?>
    <?php if (isset($_SESSION['adminLogin'])) : ?><!--Для админов-->
        <fieldset>
            <legend>Список тем:</legend>
            <table width="" border="1" cellpadding="4" cellspacing="0">
                <tr>
                    <th>Тема</th>
                    <?php if (isset($_SESSION['adminLogin'])) : ?>
                        <th>Удаление темы со всеми вопросами</th>
                        <th>Вопросов в теме</th>
                        <th>Опубликовано вопросов</th>
                        <th>Вопросов без ответа</th>
                    <?php endif ?>
                </tr>
                
                <?php foreach ($themes2 as $theme) : ?>    
                <tr>
                    <td><a href="index.php?showQuestionsTheme=<?=$theme['id']?>"><?= $theme['theme']?></a></td>
                    <?php if (isset($_SESSION['adminLogin'])) : ?>
                        <td><a href="index.php?delTheme=<?=$theme['id']?>">Удалить</a></td>
                        <td><?= $theme['Вопросов в теме']?></td>
                        <td><?= $theme['Опубликовано вопросов']?></td>
                        <td><?= $theme['Вопросов без ответа']?></td>
                    <?php endif ?>
                </tr>
                <?php endforeach ?>
            </table>
        </fieldset>
    <?php else : ?> <!--Для пользователей-->
        <fieldset>
            <legend>Список тем:</legend>
                <table width="" border="1" cellpadding="4" cellspacing="0">
                    <tr>
                        <th>Тема</th>
                    </tr>
                    <?php foreach ($themes2 as $theme) : ?>    
                    <tr>
                        <?php if ($theme['Вопросов в теме'] <> $theme['Вопросов без ответа'] OR $theme['Опубликовано вопросов'] <> 0) : ?>
                            <td><a href="index.php?showQuestionsTheme=<?=$theme['id']?>"><?= $theme['theme']?></a></td>
                        <?php endif ?>
                    </tr>
                    <?php endforeach ?>
                </table>
            </fieldset>
    <?php endif ?>
<?php else :?>
    <p>Список тем пуст! Только администраторы могу добавлять темы.</p>
<?php endif ?>

<!--Выводим вопросы для пользователей и для админов-->
<?php if (isset($_GET['showQuestionsTheme'])) : ?>

    <?php if (isset($_SESSION['adminLogin'])) : ?><!--Для админов-->

        <fieldset>
            <legend>Список всех вопросов в теме:</legend>
            <?php if (empty($questions)) : ?>
                <p>В этой теме вопросов нет.</p>
            <?php else : ?>
                <table width="" border="1" cellpadding="4" cellspacing="0">
                    <tr>
                        <th>Тема</th>
                        <th>Вопрос</th>
                        <th>Дата создания</th>
                        <th>Ожидает ответа</th>
                        <th>Опубликован</th>
                        <th>Удалить вопрос</th>
                    </tr>
                    <?php foreach ($questions as $question) : ?>
                        <tr>
                            <td><?=$question['theme']?></td>
                            <td><a href="index.php?showQuestionId=<?=$question['id']?>&showQuestionsTheme=<?=$question['theme_id']?>"><?=$question['question']?></a></td>
                            <td><?=$question['date_added']?></td>
                            <td>
                                <?php if ($question['answer'] === NULL OR $question['answer'] ==''): ?>
                                    <?='Да'?>
                                <?php else : ?>
                                    <?='Нет'?>
                                <?php endif ?>
                            </td>
                            <td>
                                <?php if ($question['published'] == 1) : ?>
                                    <p>ДА - <a href="index.php?publishedOnOff=0&showQuestionId=<?=$question['id']?>&showQuestionsTheme=<?=$question['theme_id']?>">скрыть</a></p>
                                <?php else : ?>
                                    <p>НЕТ - <a href="index.php?publishedOnOff=1&showQuestionId=<?=$question['id']?>&showQuestionsTheme=<?=$question['theme_id']?>">опубликовать</a></p>
                                <?php endif ?>
                            </td>
                            <td><a href="index.php?delQuestionId=<?=$question['id']?>&questionsThemeId=<?=$question['theme_id']?>">Удалить</a></td>
                        </tr>
                    <?php endforeach ?>
                </table>
            <?php endif ?>
        </fieldset>

    <?php else : ?><!--Для пользователей-->

        <fieldset>
            <legend>Список всех вопросов в теме:</legend>
            <table width="" border="1" cellpadding="4" cellspacing="0">
                <tr>
                    <th>Тема</th>
                    <th>Вопрос</th>
                </tr>
                <?php foreach ($questions as $question) : ?>
                    <tr>
                        <?php if ($question['answer'] !== NULL && $question['answer'] !='' && $question['published'] != 0) : ?>
                            <td><?=$question['theme']?></td>
                            <td><a href="index.php?showQuestionId=<?=$question['id']?>&showQuestionsTheme=<?=$question['theme_id']?>"><?=$question['question']?></a></td>
                        <?php endif ?>
                    </tr>
                <?php endforeach ?>
            </table>
        </fieldset>

    <?php endif ?>

    <!--Выводим данные вопроса если был запрос-->
    <?php if (isset($_GET['showQuestionId'])) :?>
        <?php if (isset($_SESSION['adminLogin'])) : ?>
            <fieldset>
                <legend>Редактирование вопроса</legend>
                <table width="" border="1" cellpadding="4" cellspacing="0">
                    <tr>
                        <th>Дата создания</th>
                        <th>Тема</th>
                        <th>Автор</th>
                        <th>Вопрос</th>
                        <th>Ответ</th>
                        <th>Ожидает ответа</th>
                        <th>Опубликован</th>
                    </tr>
                    <?php foreach ($showQuestion as $questionInfo) : ?>
                        <tr>
                            <td><?= $questionInfo['date_added'] ?></td>
                            <td>
                                <form action="index.php?showQuestionId=<?=$questionInfo['id']?>&showQuestionsTheme=<?=$questionInfo['theme_id']?>" method="POST">
                                    <select name="changeThemeId">
                                        <?php foreach ($themes as $theme) : ?>
                                            <option <?php if ($questionInfo['theme_id'] == $theme['id']):?>
                                                selected<?php endif ?> value="<?= $theme['id'] ?>">
                                                <?= $theme['theme'] ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                    <button type="submit">Изменить</button>
                                </form>
                            </td>
                            <td>
                                <form action="index.php?showQuestionId=<?=$questionInfo['id']?>&showQuestionsTheme=<?=$questionInfo['theme_id']?>" method="POST">
                                    <input type="text" size="" name="changeAuthorName" value="<?= $questionInfo['author_name'] ?>">
                                    <input type="submit" value="Изменить">
                                </form>
                            </td>
                            <td>
                                <form action="index.php?showQuestionId=<?=$questionInfo['id']?>&showQuestionsTheme=<?=$questionInfo['theme_id']?>" method="POST">
                                    <input type="text" size="" name="changeQuestion" value="<?= $questionInfo['question'] ?>">
                                    <input type="submit" value="Изменить">
                                </form>
                            </td>
                            <td>
                                <form action="index.php?showQuestionId=<?=$questionInfo['id']?>&showQuestionsTheme=<?=$questionInfo['theme_id']?>" method="POST">
                                    <textarea type="text" size="" name="changeAnswer" value=""><?= $questionInfo['answer'] ?></textarea>
                                    <p><input type="checkbox" name="publish" value="<?=$questionInfo['id']?>">опубликовать</p>
                                    <input type="submit" value="Ответить">
                                </form>
                            </td>
                            <td>
                                <?php if ($questionInfo['answer'] === NULL OR $questionInfo['answer'] ==''): ?>
                                    <?='Да'?>
                                <?php else : ?>
                                    <?='Нет'?>
                                <?php endif ?>
                            </td>
                            <td>
                            <?php if ($questionInfo['published'] == 1) : ?>
                                    <p>ДА<!-- - <a href="index.php?publishedOnOff=0&showQuestionId=<?=$questionInfo['id']?>&showQuestionsTheme=<?=$questionInfo['theme_id']?>">скрыть</a>--></p>
                                <?php else : ?>
                                    <p>НЕТ<!-- - <a href="index.php?publishedOnOff=1&showQuestionId=<?=$questionInfo['id']?>&showQuestionsTheme=<?=$questionInfo['theme_id']?>">опубликовать</a>--></p>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </table>
            </fieldset>
        <?php else : ?>
            <fieldset>
                <legend>Ответ</legend>
                <table width="" border="1" cellpadding="4" cellspacing="0">
                    <tr>
                        <th>Вопрос</th>
                        <th>Ответ</th>
                    </tr>
                    <?php foreach ($showQuestion as $questionInfo) : ?>
                        <tr>
                            <td><?= $questionInfo['question'] ?></td>
                            <td><?= $questionInfo['answer'] ?></td>
                        </tr>
                    <?php endforeach ?>
                </table>
            </fieldset>
        <?php endif ?>   
    <?php endif ?>
<?php endif ?>

<!--Вывод всех вопросов без ответа во всех темах в порядке их добавления-->
<?php if (isset($_GET['unansQuestions'])) : ?>
        <fieldset>
            <legend>Вопросы без ответа во всех темах</legend>
            <table width="" border="1" cellpadding="4" cellspacing="0">
                <tr>
                    <th>Дата создания</th>
                    <th>Тема</th>
                    <th>Вопрос</th>
                    <th>Ответ</th>
                    <th>Опубликован</th>
                    <th>Автор</th>
                    <th>E-mail автора</th>
                </tr>
                <?php foreach ($allUnansQuestions as $unansQuestion) : ?>
                    <tr>
                        <td><?= $unansQuestion['date_added'] ?></td>
                        <td>
                            <form action="index.php?showQuestionId=<?=$unansQuestion['id']?>&unansQuestions=unansQuestions" method="POST">
                                <select name="changeThemeId">
                                    <?php foreach ($themes as $theme) : ?>
                                        <option <?php if ($unansQuestion['theme_id'] == $theme['id']):?>
                                            selected<?php endif ?> value="<?= $theme['id'] ?>">
                                            <?= $theme['theme'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <button type="submit">Изменить</button>
                            </form>
                        </td>
                        <td>
                            <form action="index.php?showQuestionId=<?=$unansQuestion['id']?>&unansQuestions=unansQuestions" method="POST">
                                <input type="text" size="" name="changeQuestion" value="<?= $unansQuestion['question'] ?>">
                                <input type="submit" value="Изменить">
                            </form>
                        </td>
                        <td>
                            <form action="index.php?showQuestionId=<?=$unansQuestion['id']?>&unansQuestions=unansQuestions" method="POST">
                                <textarea type="text" size="" name="changeAnswer" value=""><?= $unansQuestion['answer'] ?></textarea>
                                <p><input type="checkbox" name="publish" value="<?=$unansQuestion['id']?>">опубликовать</p>
                                <input type="submit" value="Ответить">
                            </form>
                        </td>
                        <td>
                            <?php if ($unansQuestion['published'] == 1) : ?>
                                <p>ДА - <a href="index.php?publishedOnOff=0&showQuestionId=<?=$unansQuestion['id']?>&unansQuestions=unansQuestions">скрыть</a></p>
                            <?php else : ?>
                                <p>НЕТ - <a href="index.php?publishedOnOff=1&showQuestionId=<?=$unansQuestion['id']?>&unansQuestions=unansQuestions">опубликовать</a></p>
                            <?php endif ?>
                        </td>
                        <td>
                            <form action="index.php?showQuestionId=<?=$unansQuestion['id']?>&unansQuestions=unansQuestions" method="POST">
                                <input type="text" size="" name="changeAuthorName" value="<?= $unansQuestion['author_name'] ?>">
                                <input type="submit" value="Изменить">
                            </form>
                        </td>
                        <td><?= $unansQuestion['e-mail'] ?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        </fieldset>
    <?php endif ?>



<!--Для пользователей-->
<?php if (!isset($_SESSION['adminLogin'])) : ?>

    <!--Кнопка для вопроса-->
    <?php if (isset($theme)) : ?><!--если темы существуют то показываем кнопку "задать вопрос"-->
        <?php if (!isset($_GET['ask_question'])) : ?>
            <p><a href="index.php?ask_question=ask_question">Задать вопрос</a></p>
        <?php endif ?>
    <?php else : ?>
        <p>Задать вопрос невозможно! Попросите администратора создать тему(темы).</p>
    <?php endif ?>


    <!--Форма для нового вопроса-->
    <?php if (isset($_GET['ask_question'])) : ?>
        <form action="index.php" method="POST">
            <fieldset>
                <legend>Новый вопрос</legend>
                <p>Все поля обязательны для заполнения</p>
                <p>Ваше имя: <input type="text" size="50" name="author_name"></p>
                <p>E-mail: <input type="text" size="50" name="e-mail"></p>
                <p>Выберите тему:
                    <select name="theme_id">
                        <?php foreach ($themes as $theme) : ?>
                            <option value="<?= $theme['id'] ?>"><?= $theme['theme'] ?></option>
                        <?php endforeach ?>
                    </select>
                </p>
                <p>Ваш вопрос: <textarea type="text" cols="50" rows="5" name="question"></textarea></p>
                <button type="submit">Задать</button>
            </fieldset>
        </form>
    <?php endif ?>

    <?php if (isset($_POST['question'])) : ?>
        <p>Спасибо за вопрос! Ответ будет размещен на нашем сайте в течение суток.</p>
    <?php endif ?>

<?php endif ?>

</body>
</html>