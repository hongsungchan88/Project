<?
    include "common.php";

    $uid = $_REQUEST["uid"];
    $pwd = $_REQUEST["pwd"];

    $sql = "select id from member where uid = '$uid' and pwd = '$pwd'";
    echo($sql);

    if ($sql > 0) {

        // 고객번호 id를 쿠키변수 cookie_id로 저장
        setcookie("cookie_id", $uid, time() + 3600);

        // 테스트용
        // echo("<script>alert(\"로그인 가능.\");</script>");

        // index.html로 이동동
        header("Location: index.html");
    }
    else
        // member_login.php로 이동
        
        header("Location: member_login.php");
?>