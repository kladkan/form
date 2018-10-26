<?php
if (!empty($_POST)) {
  foreach ($_POST as $key => $value) {
    $result[$key] = explode('-', $value);
  }
  echo 'Релультаты тестирования:<br><br>';
  foreach ($result as $key => $value) {
    if ($value[0] == $value[1]) {
      echo $value[2].' Ваш ответ: '.$value[0].' верный.<br>';
    } else {
      echo $value[2].' Ваш ответ: '.$value[0].' не верный.<br>';
    }  
  }
  echo '<br><a href="admin.php">Вернуться на главную страницу</a><br>';
  echo '<br><a href="list.php">Перейти к списку загруженных тестов</a><br>';
  exit;
}

switch ($_GET['testnumber']) {
  case 't1':
    $json = file_get_contents(__DIR__ . '/tests/onservertest1.json');
    $test = json_decode($json, true);
    break;
  case 't2':
    $json = file_get_contents(__DIR__ . '/tests/onservertest2.json');
    $test = json_decode($json, true);
    break;
}

?>

<html>
<head>
  <title>Тестирование</title>
</head>
<body>
  <form action="test.php" method="POST">
  <?php foreach ($test as $key => $val) : ?>
    <fieldset>
      <legend><?php echo $val['q'] ?></legend>
        <label><input type="radio" name="q<?php echo $key+1 ?>" value="<?php echo $val['answer']['res1'].'-'.$val['trueans'].'-'.$val['q'] ?>"><?php echo $val['answer']['res1'] ?></label>
        <label><input type="radio" name="q<?php echo $key+1 ?>" value="<?php echo $val['answer']['res2'].'-'.$val['trueans'].'-'.$val['q'] ?>"><?php echo $val['answer']['res2'] ?></label>
        <label><input type="radio" name="q<?php echo $key+1 ?>" value="<?php echo $val['answer']['res3'].'-'.$val['trueans'].'-'.$val['q'] ?>"><?php echo $val['answer']['res3'] ?></label>
        <label><input type="radio" name="q<?php echo $key+1 ?>" value="<?php echo $val['answer']['res4'].'-'.$val['trueans'].'-'.$val['q'] ?>"><?php echo $val['answer']['res4'] ?></label>
    </fieldset>
  <?php endforeach; ?>
  <input type="submit" value="Отправить">
</form>
</body>
</html>
