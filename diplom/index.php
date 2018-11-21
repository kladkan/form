<?php
session_start();
if (isset($_GET['exit'])) {
    session_destroy();
    header('Location: ./index.php');
}

$pdo = new PDO("mysql:host=localhost; dbname=ayakovlev; charset=utf8","ayakovlev","neto1880");

//Авторизация для администраторов
if (!empty($_POST['authname']) && !empty($_POST['authpass'])) {
    $sql = "SELECT `id` FROM admins WHERE `login`='{$_POST['authname']}' AND `password`='{$_POST['authpass']}'";
    foreach ($pdo->query($sql) as $admin) {
    }

    if (!empty($admin['id'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_login'] = $_POST['authname'];

    } else {
        //include 'includes/empty_admin.php';
        echo 'Администратор не найден или неправильно введён логин/пароль!<br>';
        echo '<a href="index.php?admin=admin">Войдите заново</a> или обратитесь к главному Администратору</a>';
        exit;    
    }
}


if (isset($_SESSION['admin_login'])) {
    //Получение списка администраторов
    if (isset($_GET['list_admin'])) {
        $sql = "SELECT `login`, `password` FROM `admins`";
        $admins = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        //echo '<pre>'; print_r($admins); echo '</pre>';
    }

    //Добавление администратора
    if (isset($_POST['new_login']) && isset($_POST['new_password'])) {
        if (empty($_POST['new_login']) or empty($_POST['new_password'])) {
            echo 'Вы пропустили пароль или логин! Вернитесь назад.';
            exit;
        } else {
            //echo '<pre>'; print_r($_POST); echo '</pre>';
            $sql = "SELECT `id` FROM `admins` WHERE `login`='{$_POST['new_login']}'";
            foreach ($pdo->query($sql) as $admin) {
            }

            if (!empty($admin['id'])) {
                echo 'Логин: '.$_POST['new_login'].' - занят! Придумайте уникальное имя.<br>';
            } else {
                $stmt = $pdo->prepare("INSERT INTO `admins`(`login`, `password`) VALUES (?, ?)");
                $stmt->bindParam(1, $_POST['new_login']);
                $stmt->bindParam(2, $_POST['new_password']);
                $stmt->execute();
                header('Location: ./index.php?list_admin=list_admin');
            }
        }
    }

    //Изменение пароля администратора
    if (isset($_POST['change_password'])) {
        $stmt = $pdo->prepare("UPDATE `admins` SET `password`='{$_POST['change_password']}' WHERE `login`='{$_GET['login']}' LIMIT 1");
        $stmt->execute();
        header('Location: ./index.php?list_admin=list_admin');
    }

    //Удаление администратора
    if (isset($_GET['del_admin'])) {
        $stmt = $pdo->prepare("DELETE FROM `admins` WHERE `login`='{$_GET['del_admin']}' LIMIT 1");
        $stmt->execute();
        header('Location: ./index.php?list_admin=list_admin');
    }

    //Добавление новой темы
    if (isset($_POST['new_theme'])) {
        $stmt = $pdo->prepare("INSERT INTO `themes` (`theme`) VALUES (?)");
        $stmt->bindParam(1, $_POST['new_theme']);
        $stmt->execute();
        header('Location: ./index.php');
    }

    //Удаление темы со всеми вопросами
    if (isset($_GET['del_theme'])) {
        $stmt = $pdo->prepare("DELETE FROM `questions` WHERE `theme_id`='{$_GET['del_theme']}';
        DELETE FROM `themes` WHERE `id`='{$_GET['del_theme']}'");
        $stmt->execute();
        header('Location: ./index.php');
    }
    
    //Переключатель опубликован/неопубликован
    if (isset($_GET['published_on_off']) OR isset($_POST['change_answer'])) {
        if (isset($_POST['change_answer']) && !isset($_POST['publish'])) {
            $_GET['published_on_off'] = 0;
            //$_GET['question_id'] = $_GET['show_question_id'];
        }
        if (isset($_POST['publish'])) {
            $_GET['published_on_off'] = 1;
            //$_GET['question_id'] = $_GET['show_question_id'];
        }
        $stmt = $pdo->prepare("UPDATE `questions` SET `published`='{$_GET['published_on_off']}' WHERE `id`='{$_GET['show_question_id']}' LIMIT 1");
        $stmt->execute();
        //header('Location: ./index.php?show_questions_theme='.$_GET['questions_theme_id']);
    }

    //Удаление вопроса из темы
    if (isset($_GET['del_question_id'])) {
        $stmt = $pdo->prepare("DELETE FROM `questions` WHERE `id`='{$_GET['del_question_id']}'");
        $stmt->execute();
        header('Location: ./index.php?show_questions_theme='.$_GET['questions_theme_id']);
    }

    //Изменение автора
    if (isset($_POST['change_author_name'])) {
        $stmt = $pdo->prepare("UPDATE `questions` SET `author_name`='{$_POST['change_author_name']}' WHERE `id`='{$_GET['show_question_id']}' LIMIT 1");
        $stmt->execute();
    }

    //Изменение вопроса
    if (isset($_POST['change_question'])) {
        $stmt = $pdo->prepare("UPDATE `questions` SET `question`='{$_POST['change_question']}' WHERE `id`='{$_GET['show_question_id']}' LIMIT 1");
        $stmt->execute();
    }

    //Изменение ответа
    if (isset($_POST['change_answer'])) {
        $stmt = $pdo->prepare("UPDATE `questions` SET `answer`='{$_POST['change_answer']}' WHERE `id`='{$_GET['show_question_id']}' LIMIT 1");
        $stmt->execute();
    }

    //Изменение темы
    if (isset($_POST['change_theme_id'])) {
        if (!isset($_GET['unans_questions'])) {
            $_GET['show_questions_theme'] = $_POST['change_theme_id'];
        }
        $stmt = $pdo->prepare("UPDATE `questions` SET `theme_id`='{$_POST['change_theme_id']}' WHERE `id`='{$_GET['show_question_id']}' LIMIT 1");
        $stmt->execute();
    }

    //Получение всех вопросов без ответа во всех темах в порядке их добавления
    if (isset($_GET['unans_questions'])) {
        $sql = "SELECT `questions`.`id`, `theme_id`, `theme`, `question`, `answer`, `published`, `author_name`, `e-mail`, `date_added` FROM `questions` JOIN `themes` ON `themes`.`id`=`questions`.`theme_id` WHERE `answer` IS NULL OR `answer`='' ORDER BY `date_added` ASC";
        $all_unans_questions = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        //echo '<pre>'; print_r($all_unans_questions); echo '</pre>';
    }
}

//Получение списка тем (для всех)
$sql = "SELECT * FROM `themes`";
$themes = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
//echo '<pre>'; print_r($themes); echo '</pre>';

foreach ($themes as $theme) {
    //подсчет вопросов в теме
    $sql = "SELECT COUNT(*) as 'Вопросов в теме' FROM `questions` WHERE `theme_id`='{$theme['id']}' GROUP BY `theme_id`";
    $count_all_ques_in_theme_array = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    if ($count_all_ques_in_theme_array) {
        foreach ($count_all_ques_in_theme_array as $count_all_ques) {
        }
    } else {
        $count_all_ques['Вопросов в теме'] = 0;
    }

    //подсчет опубликованных вопросов в теме
     $sql = "SELECT COUNT(*) AS 'Опубликовано вопросов' FROM `questions` WHERE `theme_id`='{$theme['id']}' AND `published`=1 GROUP BY `theme_id`";
     $count_published_ques_in_theme_array = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    if ($count_published_ques_in_theme_array) {
        foreach ($count_published_ques_in_theme_array as $count_published_ques) {
        }
    } else {
        $count_published_ques['Опубликовано вопросов'] = 0;
    }
    
    //подсчет вопросов без ответа в теме
    $sql = "SELECT COUNT(*) AS 'Вопросов без ответа' FROM `questions` WHERE `theme_id`='{$theme['id']}' AND (`answer` IS NULL OR `answer`='') GROUP BY `theme_id`";
    $count_unanswered_ques_in_theme_array = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    if ($count_unanswered_ques_in_theme_array) {
        foreach ($count_unanswered_ques_in_theme_array as $count_unanswered_ques) {
        }
    } else {
        $count_unanswered_ques['Вопросов без ответа'] = 0;
    }

    //Создаем новый массив и добавляем данные из запросов
    $themes2[] = array('id' => $theme['id'], 'theme' => $theme['theme'], 'Вопросов в теме' => $count_all_ques['Вопросов в теме'], 'Опубликовано вопросов' => $count_published_ques['Опубликовано вопросов'], 'Вопросов без ответа' => $count_unanswered_ques['Вопросов без ответа']);
}
//echo '<pre>'; print_r($count_published_ques_in_theme_array); echo '</pre>';
//echo '<pre>'; print_r($themes2); echo '</pre>';

//Получение списка вопросов в выбранной теме
if (isset($_GET['show_questions_theme'])) {
    $sql = "SELECT `questions`.`id`,  `questions`.`theme_id`, `theme`, `question`, `answer`, `date_added`, `published` FROM `questions` JOIN `themes` ON `themes`.`id`=`questions`.`theme_id` WHERE `theme_id`='{$_GET['show_questions_theme']}'";
    $questions = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    //echo '<pre>'; print_r($questions); echo '</pre>';
}

//Получение данных вопроса
if (isset($_GET['show_question_id'])) {
    $sql = "SELECT `questions`.`id`,  `questions`.`theme_id`, `date_added`, `author_name`, `theme`, `question`, `answer`, `published` FROM `questions` JOIN `themes` ON `themes`.`id`=`questions`.`theme_id` WHERE `questions`.`id`='{$_GET['show_question_id']}'";
    $show_question = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    //echo '<pre>'; print_r($show_question); echo '</pre>';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    
<!--Панель администратора-->
<?php if (isset($_SESSION['admin_login'])) : ?>
    <p>Вы вошли как: <?=$_SESSION['admin_login']?></p>
    <p><a href="index.php?exit=exit">Выход (прекратить администрирование)</a></p>
    <fieldset>
        <legend>Меню администратора:</legend>
        <ul>
            <li><a href="index.php?list_admin=list_admin">Список админов</a></li>
            <li><a href="index.php?unans_questions=unans_questions">Список вопросов без ответов</a></li>
        </ul>
    </fieldset>
<?php endif ?>


<!--Для авторизации-->
<?php if (!isset($_SESSION['admin_id']) && !isset($_GET['admin'])) : ?>
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
<?php if (isset($_SESSION['admin_login'])) : ?>
    <!--Вывод списка администраторов-->
    <?php if (isset($_GET['list_admin'])) : ?>
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
                        <input type="text" size="20" name="change_password">
                        <input type="submit" value="Изменить">
                    </form>
                    <?php endif ?>
                </td>                
            <?php endforeach ?>
                <td><a href="index.php?del_admin=<?=$row['login']?>">Удалить</a></td>
        </tr>
        <?php endforeach ?>
    </table>
    <p><a href="index.php?list_admin=list_admin&add_admin=add_admin">Добавить администратора</a></p>
    <?php endif ?>

    <!--Форма для добавления администратора-->
    <?php if (isset($_GET['add_admin'])) : ?>
        <form action="index.php" method="POST">
            <fieldset>
                <legend>Новый администратор</legend>
                <p>Логин: <input type="text" size="50" name="new_login"></p>
                <p>Пароль: <input type="text" size="50" name="new_password"></p>
                <p><input type="submit" value="Добавить"></p>
            </fieldset>
        </form>
    <?php endif ?>

    <!--Форма добавление темы-->
    <?php if (isset($_GET['add_theme'])) : ?>
        <form action="index.php" method="POST">
            <fieldset>
                <legend>Новая тема</legend>
                <p><input type="text" size="70" name="new_theme"></p>
                <p><input type="submit" value="Добавить"></p>
            </fieldset>
        </form>
    <?php endif ?>

<?php endif ?><!--Для авторизованного администратора (конец)-->


<!--Выводим темы для пользователей и для админов-->
<?php if (isset($_SESSION['admin_login'])) : ?>
    <fieldset>
        <legend>Список тем:</legend>
        <table width="" border="1" cellpadding="4" cellspacing="0">
            <tr>
                <th>Тема</th>
                <?php if (isset($_SESSION['admin_login'])) : ?>
                    <th>Удаление темы со всеми вопросами</th>
                    <th>Вопросов в теме</th>
                    <th>Опубликовано вопросов</th>
                    <th>Вопросов без ответа</th>
                <?php endif ?>
            </tr>
            <?php foreach ($themes2 as $theme) : ?>    
            <tr>
                <td><a href="index.php?show_questions_theme=<?=$theme['id']?>"><?= $theme['theme']?></a></td>
                <?php if (isset($_SESSION['admin_login'])) : ?>
                    <td><a href="index.php?del_theme=<?=$theme['id']?>">Удалить</a></td>
                    <td><?= $theme['Вопросов в теме']?></td>
                    <td><?= $theme['Опубликовано вопросов']?></td>
                    <td><?= $theme['Вопросов без ответа']?></td>
                <?php endif ?>
            </tr>
            <?php endforeach ?>
        </table>
        <?php if (isset($_SESSION['admin_login'])) : ?>
            <a href="index.php?add_theme=add_theme">Добавить тему...</a>
        <?php endif ?>
    </fieldset>
<?php else : ?>
    <fieldset>
            <legend>Список тем:</legend>
            <table width="" border="1" cellpadding="4" cellspacing="0">
                <tr>
                    <th>Тема</th>
                </tr>
                <?php foreach ($themes2 as $theme) : ?>    
                <tr>
                    <?php if ($theme['Вопросов в теме'] <> $theme['Вопросов без ответа']) : ?>
                        <td><a href="index.php?show_questions_theme=<?=$theme['id']?>"><?= $theme['theme']?></a></td>
                    <?php endif ?>
                </tr>
                <?php endforeach ?>
            </table>
        </fieldset>
<?php endif ?>

<!--Выводим вопросы для пользователей и для админов-->
<?php if (isset($_GET['show_questions_theme'])) : ?>

    <?php if (isset($_SESSION['admin_login'])) : ?><!--Для админов-->

        <fieldset>
            <legend>Список всех вопросов в теме:</legend>
            <?php if (empty($questions)) : ?>
                <p>В этой теме вопросов нет. Выберите другую тему.</p>
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
                            <td><a href="index.php?show_question_id=<?=$question['id']?>&show_questions_theme=<?=$question['theme_id']?>"><?=$question['question']?></a></td>
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
                                    <p>ДА - <a href="index.php?published_on_off=0&show_question_id=<?=$question['id']?>&show_questions_theme=<?=$question['theme_id']?>">скрыть</a></p>
                                <?php else : ?>
                                    <p>НЕТ - <a href="index.php?published_on_off=1&show_question_id=<?=$question['id']?>&show_questions_theme=<?=$question['theme_id']?>">опубликовать</a></p>
                                <?php endif ?>
                            </td>
                            <td><a href="index.php?del_question_id=<?=$question['id']?>&questions_theme_id=<?=$question['theme_id']?>">Удалить</a></td>
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
                        <?php if ($question['answer'] !== NULL OR $question['answer'] !=''): ?>
                            <td><?=$question['theme']?></td>
                            <td><a href="index.php?show_question_id=<?=$question['id']?>&show_questions_theme=<?=$question['theme_id']?>"><?=$question['question']?></a></td>
                        <?php endif ?>
                    </tr>
                <?php endforeach ?>
            </table>
        </fieldset>

    <?php endif ?>

    <!--Выводим данные вопроса если был запрос-->
    <?php if (isset($_GET['show_question_id'])) :?>
        <?php if (isset($_SESSION['admin_login'])) : ?>
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
                    <?php foreach ($show_question as $question_info) : ?>
                        <tr>
                            <td><?= $question_info['date_added'] ?></td>
                            <td>
                                <form action="index.php?show_question_id=<?=$question_info['id']?>&show_questions_theme=<?=$question_info['theme_id']?>" method="POST">
                                    <select name="change_theme_id">
                                        <?php foreach ($themes as $theme) : ?>
                                            <option <?php if ($question_info['theme_id'] == $theme['id']):?>
                                                selected<?php endif ?> value="<?= $theme['id'] ?>">
                                                <?= $theme['theme'] ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                    <button type="submit">Изменить</button>
                                </form>
                            </td>
                            <td>
                                <form action="index.php?show_question_id=<?=$question_info['id']?>&show_questions_theme=<?=$question_info['theme_id']?>" method="POST">
                                    <input type="text" size="" name="change_author_name" value="<?= $question_info['author_name'] ?>">
                                    <input type="submit" value="Изменить">
                                </form>
                            </td>
                            <td>
                                <form action="index.php?show_question_id=<?=$question_info['id']?>&show_questions_theme=<?=$question_info['theme_id']?>" method="POST">
                                    <input type="text" size="" name="change_question" value="<?= $question_info['question'] ?>">
                                    <input type="submit" value="Изменить">
                                </form>
                            </td>
                            <td>
                                <form action="index.php?show_question_id=<?=$question_info['id']?>&show_questions_theme=<?=$question_info['theme_id']?>" method="POST">
                                    <textarea type="text" size="" name="change_answer" value=""><?= $question_info['answer'] ?></textarea>
                                    <p><input type="checkbox" name="publish" value="<?=$question_info['id']?>">опубликовать</p>
                                    <input type="submit" value="Ответить">
                                </form>
                            </td>
                            <td>
                                <?php if ($question_info['answer'] === NULL OR $question_info['answer'] ==''): ?>
                                    <?='Да'?>
                                <?php else : ?>
                                    <?='Нет'?>
                                <?php endif ?>
                            </td>
                            <td>
                            <?php if ($question_info['published'] == 1) : ?>
                                    <p>ДА<!-- - <a href="index.php?published_on_off=0&show_question_id=<?=$question_info['id']?>&show_questions_theme=<?=$question_info['theme_id']?>">скрыть</a>--></p>
                                <?php else : ?>
                                    <p>НЕТ<!-- - <a href="index.php?published_on_off=1&show_question_id=<?=$question_info['id']?>&show_questions_theme=<?=$question_info['theme_id']?>">опубликовать</a>--></p>
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
                    <?php foreach ($show_question as $question_info) : ?>
                        <tr>
                            <td><?= $question_info['question'] ?></td>
                            <td><?= $question_info['answer'] ?></td>
                        </tr>
                    <?php endforeach ?>
                </table>
            </fieldset>
        <?php endif ?>   
    <?php endif ?>
<?php endif ?>

<!--Вывод всех вопросов без ответа во всех темах в порядке их добавления-->
<?php if (isset($_GET['unans_questions'])) : ?>
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
                <?php foreach ($all_unans_questions as $unans_question) : ?>
                    <tr>
                        <td><?= $unans_question['date_added'] ?></td>
                        <td>
                            <form action="index.php?show_question_id=<?=$unans_question['id']?>&unans_questions=unans_questions" method="POST">
                                <select name="change_theme_id">
                                    <?php foreach ($themes as $theme) : ?>
                                        <option <?php if ($unans_question['theme_id'] == $theme['id']):?>
                                            selected<?php endif ?> value="<?= $theme['id'] ?>">
                                            <?= $theme['theme'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <button type="submit">Изменить</button>
                            </form>
                        </td>
                        <td>
                            <form action="index.php?show_question_id=<?=$unans_question['id']?>&unans_questions=unans_questions" method="POST">
                                <input type="text" size="" name="change_question" value="<?= $unans_question['question'] ?>">
                                <input type="submit" value="Изменить">
                            </form>
                        </td>
                        <td>
                            <form action="index.php?show_question_id=<?=$unans_question['id']?>&unans_questions=unans_questions" method="POST">
                                <textarea type="text" size="" name="change_answer" value=""><?= $unans_question['answer'] ?></textarea>
                                <p><input type="checkbox" name="publish" value="<?=$unans_question['id']?>">опубликовать</p>
                                <input type="submit" value="Ответить">
                            </form>
                        </td>
                        <td>
                            <?php if ($unans_question['published'] == 1) : ?>
                                <p>ДА - <a href="index.php?published_on_off=0&show_question_id=<?=$unans_question['id']?>&unans_questions=unans_questions">скрыть</a></p>
                            <?php else : ?>
                                <p>НЕТ - <a href="index.php?published_on_off=1&show_question_id=<?=$unans_question['id']?>&unans_questions=unans_questions">опубликовать</a></p>
                            <?php endif ?>
                        </td>
                        <td>
                            <form action="index.php?show_question_id=<?=$unans_question['id']?>&unans_questions=unans_questions" method="POST">
                                <input type="text" size="" name="change_author_name" value="<?= $unans_question['author_name'] ?>">
                                <input type="submit" value="Изменить">
                            </form>
                        </td>
                        <td><?= $unans_question['e-mail'] ?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        </fieldset>
    <?php endif ?>



<!--Для пользователей-->
<?php if (!isset($_SESSION['admin_login'])) : ?>

    <!--Кнопка для вопроса-->
    <?php if (!isset($_GET['ask_question'])) : ?>
        <p><a href="index.php?ask_question=ask_question">Задать вопрос</a></p>
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