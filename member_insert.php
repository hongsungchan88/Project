<?
	include "common.php";
	
		$uid = $_REQUEST["uid"];
		$pwd = $_REQUEST["pwd"];
		$name = $_REQUEST["name"];
		$tel1 = $_REQUEST["tel1"];
		$tel2 = $_REQUEST["tel2"];
		$tel3 = $_REQUEST["tel3"];
		$tel = sprintf("%-3s%-4s%-4s", $tel1, $tel2, $tel3);
		$zip = $_REQUEST["zip"];
		$juso = $_REQUEST["juso"];
		$email = $_REQUEST["email"];
		$birthday1 = $_REQUEST["birthday1"];
		$birthday2 = $_REQUEST["birthday2"];
		$birthday3 = $_REQUEST["birthday3"];
		$birthday = sprintf("%04d-%02d-%02d", $birthday1, $birthday2, $birthday3);
		
		$sql="insert into member (uid, pwd, name, tel, zip, juso, email, birthday)
				values('$uid', '$pwd', '$name', '$tel', '$zip', '$juso', '$email', '$birthday')";
		$result=mysqli_query($db, $sql);

		if (!$result) exit("에러: $sql");
		
		echo("<script>location.href='member_joinend.php'</script>");
?>