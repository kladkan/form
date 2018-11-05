<?php
session_start();

include 'resources/include/forunauthor.php';

if (!empty($_POST)) {
    
    if (count($_POST) == 0) {
        echo 'Вы не ответили ни на один вопрос. Вернитесь на предыдущую страницу';
        exit;
    }

    foreach ($_POST as $row => $value) {
      $result[$row] = explode('-', $row);
    }

    $testnum = str_replace('_', '.', $result[$row][0]);

    $forcheck = file_get_contents(__DIR__ . '/resources/tests/' . $testnum);
    $check = json_decode($forcheck, true);

    $x = 0;
    foreach ($check as $key => $value) {

        if (empty($_POST[$result[$row][0].'-'.$key])) {        
            echo $check[$key]['q'].' Вы не ответили на этот вопрос.<br>';
        } elseif ($_POST[$result[$row][0].'-'.$key] == $check[$key]['trueans']) {
                echo $check[$key]['q'].' Ваш ответ: '. $_POST[$result[$row][0].'-'.$key].' верный.<br>';
                $x = $x + 1;
            } else {
                echo $check[$key]['q'].' Ваш ответ: '. $_POST[$result[$row][0].'-'.$key].' не верный.<br>';
            }
    }

    $usname = 'СЕРТИФИКАТ выдан студенту: '.$_SESSION['name'];
    $res = 'с результатом: Правильных ответов '.$x.' из '.count($check);

    echo '<a href="resources/img/certV3.php?param1='.$usname.'&param2='.$res.'">Получить сертификат</a>';
        
    exit;
}

$list = scandir('./resources/tests');

if (empty($_GET['testnumber'])) {
  echo 'Вы не ответили ни на один вопрос.Вернитесь на предыдущую страницу';
  exit;
}

for ($i=2; $i < count($list); $i++) {
    if ($_GET['testnumber'] == $list[$i]) {
    $json = file_get_contents(__DIR__ . '/resources/tests/' . $list[$i]);
      $test = json_decode($json, true);
  }
}
if (empty($test)) {
  http_response_code(404);
  echo 'Теста не существует';
  exit;
}

?>

<html>
<head>
  <title>Тестирование</title>
</head>
<body>
  <form action="testV3.php" method="POST">
  <?php foreach ($test as $key => $val) : ?>
    <fieldset>
      <legend><?php echo $val['q'] ?></legend>
      <?php foreach ($val['answer'] as $row => $ans) : ?>
        <label><input type="radio" name="<?php echo $_GET['testnumber'].'-' ?><?php echo $key ?>" value="<?php echo $ans ?>"><?php echo $ans ?></label>
      <?php endforeach; ?>
    </fieldset>
  <?php endforeach; ?>
  <input type="submit" value="Отправить">

</form>

</body>
</html>
