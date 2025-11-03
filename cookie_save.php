<?
    $name=$_REQUEST["name"];
    setcookie("cookie_value", $name, 0);
    echo("<script>location.href='cookie_view.php'</script>");
?>