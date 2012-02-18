<?php
require 'php-sdk/src/facebook.php';

// インスタンス生成
$facebook = new Facebook(array(
   'appId'  => '139869786134181',
   'secret' => '00032eaeb46e598f9c69aa07400e9458',
//  'appId'  => '185962178177200', // for YC
//  'secret' => '7d297d8f025ab9497ffb7a8267f7d16c' // YC
));

// ユーザＩＤ取得
$user = $facebook->getUser();

// $user_id = $facebook->require_login("publish_stream");

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}

if ($user) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl(array(
    'scope' => 'publish_stream,user_birthday',
    'redirect_uri' => 'https://apps.facebook.com/sakauratestai/'
  ));
  header("Location: {$loginUrl}");
  exit;
}

// メッセージが投稿されたときは Facebook に送信

    if(isset($_POST['submit1'] ) || isset( $_POST['submit2'])) {
        $facebook->api('/me/feed', 'POST', array(
            'message' => $_POST['wallmessage'],
        ));
       
       //DBへの接続の準備
$questionID = $_POST["questionID"];
$questionAnswerID = $_POST["questionAnswerID"];
$comment =$_POST["comment"];

$dsn = 'mysql:dbname=den2_db;host=mysql408.db.sakura.ne.jp';
$dbuser = 'den2';
$passwd = 'den2den2den2';

try {
   $pdo = new PDO($dsn, $dbuser, $passwd);

//投票数の更新
   $sql = 'UPDATE questionAnswer SET QuestionAnswerCount = QuestionAnswerCount + 1,QuestionAnswerPoint = QuestionAnswerPoint + 1 WHERE QuestionID = ? and QuestionAnswerID = ? ';   
   $stmt = $pdo->prepare($sql);
   $stmt->execute(array($questionID,$questionAnswerID));

   //コメントの保有
   //Commentは８０字以内のコメントを保有
   $stmt = $pdo->query("SET NAMES utf8;");
   $stmt = $pdo->query($sql);
   $sql = 'INSERT INTO QuestionAnswerComment (QuestionID,QuestionAnswerID,Comment,FBUserID) VALUES (?,?,?,?)';
   $stmt = $pdo->prepare($sql);
   $stmt->execute(array($questionID,$questionAnswerID,$comment,$user));

    //ユーザーID,ユーザ名保有
   $sql = 'INSERT INTO facebookUser(FacebookUserID,FacebookUserName) VALUES (?,?)';
   $stmt = $pdo->prepare($sql);
   $stmt->execute(array($user,$user_profile['name']));

} catch( PDOException $e ) {
// DBアクセスができなかったとき
  echo 'Connection failed(1): ' . $e->getMessage();
  $pdo = null;
  die();
}
        header("Location: main.php");
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<link rel = "stylesheet" href = "vote.css" type = "text/css">
<title>vote page</title>

</head>

<body>

<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js"></script>
<div id="fb-root"></div>

<?php

$name_p1 = "きゃりーぱみゅぱみゅ";
$name_p2 = "温水洋一";
$img_p1 = "/images/pmpm.jpg";
$img_p2 = "/images/nknk.jpg";

?>


<!-- ヘッダー -->
<div id = "header">
<img src = "images/Vfor_03.gif" alt = "ロゴ" width = "300" height = "100" />
<h3>嫌いな方に投票してください</h3>
</div>

<!-- コンテンツ -->

<div id = "content">
<form action = "" method = "POST">
<div id = "questionAnswerID01">
<?php print "<img src = {$img_p1}><br/>\n"; ?>
<?php print "$name_p1<br/>\n"; ?>
<input type = "hidden" name="questionID" value="01">
<input type = "hidden" name="questionAnswerID" value="01">
<input type = "text" name = "comment" maxlength = "80" />
<input type = "submit" name = "submit1" value = "投票" />
 <input type = "hidden" name="wallmessage" value="<?php print $user_profile['name']; ?>さんがきゃりーぱみゅぱみゅに投票しました">
</form>
</br></br>
</div>

<form action = "" method = "POST">
<div id = "questionAnswerID02">
<?php print "<img src = {$img_p2}><br/>\n"; ?>
<?php print "$name_p2<br/>\n"; ?>
<input type = "hidden" name="questionID" value="01">
<input type = "hidden" name="questionAnswerID" value="02">
<input type = "text" name = "comment" maxlength = "80" />
<input type = "submit" name = "submit2" value = "投票" />
<input type = "hidden" name="wallmessage" value="<?php print $user_profile['name']; ?>さんが温水に投票しました">
</form>
</br></br>
</div>
</div>

<div id = "comment">
<div id = "table-left">
<table border="1">
<tr>
    <th>コメント</th>
　<th>Num.</th>
</tr>

<?php

$dsn = 'mysql:dbname=den2_db;host=mysql408.db.sakura.ne.jp';
$dbuser = 'den2';
$passwd = 'den2den2den2';

try{
$pdo = new PDO($dsn, $dbuser, $passwd);
$sql = 'SELECT QuestionAnswerCommentID , Comment  FROM  QuestionAnswerComment WHERE  QuestionID = 01 and QuestionAnswerID = 01 and LENGTH(Comment)  > 0 ORDER BY  QuestionAnswerCommentID  DESC LIMIT 0,5 ';
//文字コードをutf8に指定
$stmt = $pdo->query("SET NAMES utf8;");
 $stmt = $pdo->query($sql);
} catch( PDOException $e ) {
// DBアクセスができなかったとき
 echo 'Connection failed(2): ' . $e->getMessage();
 $pdo = null;
 die();
}
 while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
 print('<td>'.$result['Comment'].'</td>');
 print('<td>'.$result['QuestionAnswerCommentID'].'</td>');
 print('</tr>');
 }
?>
</table>
</div>

<div id = "table-right">
<table border="1">
<tr>
    <th>コメント</th>
　<th>Num.</th>
</tr>

<?php

//$dsn = 'mysql:dbname=den2_db;host=mysql408.db.sakura.ne.jp';
//$dbuser = 'den2';
//$passwd = 'den2den2den2';

try{
//$pdo = new PDO($dsn, $dbuser, $passwd);
$sql = 'SELECT QuestionAnswerCommentID , Comment  FROM  QuestionAnswerComment WHERE  QuestionID = 01 and QuestionAnswerID = 02 and LENGTH(Comment)  > 0 ORDER BY  QuestionAnswerCommentID  DESC LIMIT 0,5 ';
//文字コードをutf8に指定
$stmt = $pdo->query("SET NAMES utf8;");
$stmt = $pdo->query($sql);
} catch( PDOException $e ) {
// DBアクセスができなかったとき
 echo 'Connection failed(3): ' . $e->getMessage();
 $pdo = null;
 die();
}
 while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
 print('<td>'.$result['Comment'].'</td>');
 print('<td>'.$result['QuestionAnswerCommentID'].'</td>');
 print('</tr>');
 }
?>
</table>
</div>
</div>


<div id = "check">
<form method="POST" action="main.php" >
  <input type="submit" id="result" name="result" value="投票せずに結果を見る">
</form>
</div>

    <h1>php-sdk</h1>

    <?php if ($user): ?>
      <a href="<?php echo $logoutUrl; ?>">Logout</a>
    <?php else: ?>
//      </div>
       Login using OAuth 2.0 handled by the PHP SDK:
  header("HTTP/1.1 301 Moved Permanently");
  header($loginUrl);
//        <a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
//      </div>
    <?php endif ?>

    <h3>PHP Session</h3>
    <pre><?php print_r($_SESSION); ?></pre>

    <?php if ($user): ?>
      <h3>You</h3>
      <img src="https://graph.facebook.com/<?php echo $user; ?>/picture">

      <h3>Your User Object (/me)</h3>
      <pre><?php print_r($user_profile); ?></pre>
    <?php else: ?>
      <strong><em>You are not Connected.</em></strong>
    <?php endif ?>


  </body>
</html>