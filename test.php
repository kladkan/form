<?php
if (!empty($_POST)) {
foreach ($_POST as $key => $value) {
    $result[$key] = explode('-', $key);
  }

$testnum = str_replace('_', '.', $result[$key][0]);

$forcheck = file_get_contents(__DIR__ . '/tests/' . $testnum);
$check = json_decode($forcheck, true);

if (count($_POST) !== count($check)) {
  echo 'Вы ответили не на все вопросы. <a href="list.php">Перейти к списку тестов.</a>';
  exit;
}

//'

$i = 0;
foreach ($_POST as $key => $value) {

  if ($value == $check[$i]['trueans']) {
    echo $check[$i]['q'].' Ваш ответ: '. $_POST[$key].' верный.<br>';
  } else {
    echo $check[$i]['q'].' Ваш ответ: '. $_POST[$key].' не верный.<br>';
  }
  $i++;
}
exit;

}

$list = scandir('./tests');
for ($i=2; $i < count($list); $i++) {
    if ($_GET['testnumber'] == $list[$i]) {
    $json = file_get_contents(__DIR__ . '/tests/' . $list[$i]);
      $test = json_decode($json, true);
  }
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
      <?php foreach ($val['answer'] as $row => $ans) : ?>
        <label><input type="radio" name="<?php echo $_GET['testnumber'].'-' ?>q<?php echo $key+1 ?>" value="<?php echo $ans ?>"><?php echo $ans ?></label>
      <?php endforeach; ?>
    </fieldset>
  <?php endforeach; ?>
  <input type="submit" value="Отправить">
</form>
</body>
</html>
