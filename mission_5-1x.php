<html>
<head>
<meta charset="UTF-8">
<style>
	body{
		margin: 0px;
	}

	.title{
		background:#00a7db;
		color: #ffffff;
		text-align: center;
		padding: 3px;
		box-shadow: 0 3px 6px rgba(0,0,0,0.2);
		position: -webkit-sticky;
		position: sticky;
		top: 0;
	}

	.form{
		background: #ffffff;
		text-align: center;	
	}
	
	.form .post{
		text-align: left;
		display: inline-block;
	}
	input{
		background: #ffffff;
	}

	textarea{
		vertical-align:top;
	}
</style>

</head>
	
<?php
	
	//データベースに接続 
	$dbn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dbn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
	//テーブル作成
	$sql = "CREATE TABLE IF NOT EXISTS comtable"
	."("
	."id INT  NOT NULL AUTO_INCREMENT PRIMARY KEY,"
	."name char(32),"
	."comment TEXT,"
	."time TEXT,"
	."pass TEXT"
	.");";
	$stmt = $pdo->query($sql);
			
	//投稿
	if(isset($_POST['btn'])){
		$toukoupass = $_POST['toukoupass'];
		
		if(!empty($_POST['namae']) && !empty($_POST['comment']) && $toukoupass == "pass"){
			
			if(empty($_POST['hidedit'])){
				
				//投稿内容をinsert
				$stmt = $pdo -> prepare("INSERT INTO comtable(name,comment,time,pass)VALUES(:name,:comment,:time,:pass)");
				$stmt->bindParam(':name', $name, PDO::PARAM_STR);
				$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
				$stmt->bindParam(':time',$date,PDO::PARAM_STR);
				$stmt->bindParam(':pass',$pass,PDO::PARAM_STR);
				$name = $_POST['namae'];
				$comment = $_POST['comment'];
				$date = date("Y/m/d H:i:s");
				$pass = $_POST['toukoupass'];
				$stmt->execute();
			}else{
				
				//編集
				$sql = 'UPDATE comtable SET name=:name,comment=:comment,time=:time,pass=:pass WHERE id=:id';
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':name', $name, PDO::PARAM_STR);
				$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
				$stmt->bindParam(':time',$date,PDO::PARAM_STR);
				$stmt->bindParam(':id', $edit, PDO::PARAM_INT);
				$stmt->bindParam(':pass',$pass,PDO::PARAM_INT);
				$edit = $_POST['hidedit'];
				$name = $_POST['namae'];
				$comment = $_POST['comment']."[編集済み]";
				$date = date("Y/m/d H:i:s");
				$pass = $_POST['editpass'];
				$stmt->execute();
			}
		}
	}
	
	//削除	
	if(isset($_POST['delebtn'])){
		$delepass = $_POST['delepass'];
		
		if(!empty($_POST['delete']) && $delepass == "pass"){
			$dele = $_POST['delete'];
			$sql = 'DELETE FROM comtable WHERE id=:id';
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':id',$dele,PDO::PARAM_INT);
			$stmt->execute();
		}
	}			
?>
	
	<body>
	<div class = "title"><h1>掲示板ああああ</h1></div>
	
	<div class = "form">
	<form method="POST" action=""> 

	<h3>【投稿フォーム】</h3>
		<table align = "center">
		<tr><td>名前：</td>
		<td><input type="text" name="namae" placeholder="名前" value="<?php
		if(isset($_POST['editbtn'])){
			$editpass = $_POST['editpass'];
			
			if(!empty($_POST['edit']) && $editpass == 'pass'){
				$sql = 'SELECT*FROM comtable WHERE id=:id';
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id',$edit,PDO::PARAM_INT);
				$edit = $_POST['edit'];
				$stmt->execute();
				$result = $stmt->fetch();
				echo $result['name'];
			}
		}?>"> </td></tr>
		
		<tr><td>コメント：</td>
		<td><textarea name="comment"  rows="4" cols="30"placeholder="コメント"><?php
		if(isset($_POST['editbtn'])){
			$editpass = $_POST['editpass'];
			
			if(!empty($_POST['edit']) && $editpass == "pass"){
				$sql = 'SELECT*FROM comtable WHERE id=:id';
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id',$edit,PDO::PARAM_INT);
				$edit = $_POST['edit'];
				$stmt->execute();
				$result = $stmt->fetch();
				echo $result['comment'];
			}
		}?></textarea></td></tr>
		
		<tr><td>パスワード：</td>
		<td><input type="password" name="toukoupass" pattern="^[0-9A-Za-z]+$" maxlength="8"></td></tr> 
		</table>
		<input type="submit" name="btn" value="送信">  <br><br>
		

	<h3>【削除フォーム】</h3>
		<table align = "center">
		<tr><td>投稿番号：</td>
		<td><input type="number" name="delete" placeholder="削除指定番号"> </td></tr>
		
		<tr><td>パスワード：</td>
		<td><input type="password" name="delepass" pattern="^[0-9A-Za-z]+$" maxlength="8"> </td></tr>
		</table>
		<input type="submit" name="delebtn" value="削除"> <br><br>

	<h3>【編集フォーム】</h3>
		<table align = "center">
		<tr><td>投稿番号：</td> 
		<td><input type="number" name="edit" placeholder="編集番号(半角)"> </td></tr>
		
		<tr><td>パスワード：</td>
		<td><input type="password" name="editpass" pattern="^[0-9A-Za-z]+$" maxlength="8"> </td></tr>
		</table>
		<input type="submit" name="editbtn" value="編集"> 
		<input type="hidden" name="hidedit" value="<?php 
		if(isset($_POST['editbtn'])){
			if(!empty($_POST['edit'])){
			 	$edit = $_POST['edit'];
			 	echo $edit;
			 }
		}?>">
			
	</form>
	<hr>
	
	<font color= "crimson">
	<?php
	//エラー表示
	//投稿フォームに関して
	if(isset($_POST['btn'])){
		if(empty($_POST['namae'])){
			echo "名前を入力してください<br>";
		}
		if(empty($_POST['comment'])){
			echo "コメントを入力してください<br>";
		}
		if(empty($_POST['toukoupass'])){
			echo "パスワードを入力してください<br>";
		}
	}
	
	//削除フォームに関して
	if(isset($_POST['delebtn'])){
		if(empty($_POST['delete'])){
			echo "削除したい投稿番号を入力してください<br>";
		}
		if(empty($_POST['delepass'])){
			echo "パスワードを入力してください<br>";
		}
	}
	
	//編集フォームに関して
	if(isset($_POST['editbtn'])){
		if(empty($_POST['edit'])){
			echo "編集したい投稿番号を入力してください<br>";
		}
		if(empty($_POST['editpass'])){
			echo "パスワードを入力してください<br>";
		}
	}
	?>
	</font>
	
	<hr>
	<h4>【投稿一覧】</h4>
	<div class = "post">
	
<?php	
	
	//投稿表示
	$sql = 'SELECT*FROM comtable';
	$stmt = $pdo->query($sql);
	$result = $stmt->fetchALL();
	foreach($result as $loop){
		echo $loop['id'].":".$loop['name']."&nbsp;".$loop['time']."<br>"."&emsp;".$loop['comment']."<br>";
	}

	/*
	$sql =' DROP table comtable';
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	*/
?>
</div>
</div>
</body>

</html>