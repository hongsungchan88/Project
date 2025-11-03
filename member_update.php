<?
    include "common.php";

    $cookie_id=$_COOKIE["cookie_id"];
    $name = $_REQUEST["name"];
    $pwd = $_REQUEST["pwd"];
    $tel1 = $_REQUEST["tel1"];
	$tel2 = $_REQUEST["tel2"];
	$tel3 = $_REQUEST["tel3"];
	$tel = sprintf("%s%s%s", $tel1, $tel2, $tel3);
	$zip = $_REQUEST["zip"];
	$juso = $_REQUEST["juso"];
	$email = $_REQUEST["email"];
	$birthday1 = $_REQUEST["birthday1"];
	$birthday2 = $_REQUEST["birthday2"];
	$birthday3 = $_REQUEST["birthday3"];
	$birthday = sprintf("%04d-%02d-%02d", $birthday1, $birthday2, $birthday3);

    if (!empty($pwd))
        $sql="update member set name='$name', pwd=$pwd, tel='$tel', zip=$zip, birthday='$birthday', juso='$juso', email='$email' where uid='$cookie_id'";
    else
        $sql="update member set name='$name', tel='$tel', zip=$zip, birthday='$birthday', juso='$juso', email='$email' where uid='$cookie_id'";
	$result=mysqli_query($db, $sql);
	if (!$result) exit("에러: $sql");
	
	echo("<script>location.href='member_edit.php'</script>");
?>