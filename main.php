<?php

//DBへの接続の準備
$dsn = 'mysql:dbname=den2_db;host=mysql408.db.sakura.ne.jp';
$dbuser = 'den2';
$passwd = 'den2den2den2';

try {
  $pdo = new PDO($dsn, $dbuser, $passwd);
  $sql = 'SELECT QuestionAnswer, QuestionAnswerCount, QuestionAnswerPoint FROM questionAnswer ';
//文字コードをutf8に指定
  $stmt = $pdo->query("SET NAMES utf8;");
  $stmt = $pdo->query($sql);

} catch( PDOException $e ) {
// DBアクセスができなかったとき
  echo 'Connection failed(2): ' . $e->getMessage();
  $pdo = null;
  die();
}

?>

<html>
<head>
<meta charset="UTF-8">
<title>PHP TEST</title>

</head>
<body>

<img src = "images/Vfor_03.gif" width = "350" height = "150">

<!--カウント用グラフ-->
<table border="1" cellpadding="5">
<caption>アンケート結果</caption>
<tr>
    <th>答え</th>
    <th>カウント</th>
    <th></th>
</tr>

<?php

//DBへの接続の準備
$dsn = 'mysql:dbname=den2_db;host=mysql408.db.sakura.ne.jp';
$dbuser = 'den2';
$passwd = 'den2den2den2';

try {
  $pdo = new PDO($dsn, $dbuser, $passwd);
  $sql = 'SELECT QuestionAnswer, QuestionAnswerCount, QuestionAnswerPoint FROM questionAnswer ';
//文字コードをutf8に指定
  $stmt = $pdo->query("SET NAMES utf8;");
  $stmt = $pdo->query($sql);

} catch( PDOException $e ) {
// DBアクセスができなかったとき
  echo 'Connection failed(1): ' . $e->getMessage();
  $pdo = null;
  die();
}

//SELECTした結果を取り出し、表示
  while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
    print('<td>'.$result['QuestionAnswer'].'</td>');
    print('<td><div style="background-color: blue; width: '.$result['QuestionAnswerCount'].' px; font-size: 10px;">&nbsp;</div></td>');
    print('<td>'.$result['QuestionAnswerCount'].' 票</td>');
    print('</tr>');
  }
?>
</table>
<br>
<!--地域別グラフ-->
<table border="1" cellpadding="5">
<caption>地域別</caption>
<tr>
    <th>答え</th>
    <th>Point</th>
    <th></th>
</tr>

<?php
try {
//  $pdo = new PDO($dsn, $user, $passwd);
  $sql = 'SELECT QuestionAnswer, QuestionAnswerCount, QuestionAnswerPoint FROM questionAnswer ';
//文字コードをutf8に指定
  $stmt = $pdo->query("SET NAMES utf8;");
  $stmt = $pdo->query($sql);
  $pdo = null;

} catch( PDOException $e ) {

// DBアクセスができなかったとき
  echo 'Connection failed(2): ' . $e->getMessage();
  $pdo = null;
  die();
}
//SELECTした結果を取り出し、表示
  while($result02 = $stmt->fetch(PDO::FETCH_ASSOC)){
    print('<tr><td>'.$result02['QuestionAnswer'].'</td>');
    print('<td><div style="background-color: red; width: '.$result02['QuestionAnswerPoint'].' px; font-size: 10px;">&nbsp;</div></td>');
    print('<td>'.$result02['QuestionAnswerPoint'].' P</td>');
    print('</tr>');
  }
?>
</table>

<br>

<Form><Input type=button value="アンケート画面に戻る" onClick="location.href='index.php'"></Form>

<form action = "index.php" >
<input type = "submit" name = "submit3" value = "アンケート画面に戻る" />
</form>

<!--javascript:history.go(-1)-->

  </body>
</html>