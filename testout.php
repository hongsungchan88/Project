<?
	$name1=$_REQUEST["name1"];
	$name2=$_REQUEST["name2"];
?>
<html>
<head>
	<meta charset="utf-8">
	<title>testout</title>
</head>
<body>

name1은 <font color="blue"><?=$name1;?></font>입니다.
<br>
name2는 <font color="blue"><? echo("$name2"); ?></font>입니다.
<br><br>
<a href="javascript:history.back();">돌아가기</a>
	
</body>
</html>



