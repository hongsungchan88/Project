<?
    setcookie("cookie_id", "", time() - 3600);

    header("Location: member_login.php");
?>