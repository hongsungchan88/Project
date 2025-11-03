<?
	include "common.php";
	
	$id=$_REQUEST["id"];
	
	$sql="delete from juso where id=$id";
	$result=mysqli_query($db, $sql);
	if (!$result) exit("에러: $sql");
	
	echo("<script>location.href='juso_list.php'</script>");
?>