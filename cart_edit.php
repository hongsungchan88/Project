<?php
    include "common.php";

    $cookie_path = "/~shop43";
    $cookie_domain = "gamejigix.induk.ac.kr";
    $cookie_expiry_time = time() + 60 * 60 * 24 * 30;

    $kind = isset($_REQUEST["kind"]) ? $_REQUEST["kind"] : "";
    $id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : 0;
    $num = isset($_REQUEST["num"]) ? intval($_REQUEST["num"]) : 0;
    $opts_id1 = isset($_REQUEST["opts1"]) ? intval($_REQUEST["opts1"]) : 0;

    $opts_id2_req = isset($_REQUEST["opts2"]) ? $_REQUEST["opts2"] : ""; 
    $opts_id2 = ($opts_id2_req === "" || $opts_id2_req === null) ? 0 : intval($opts_id2_req);

    $pos = isset($_REQUEST["pos"]) ? intval($_REQUEST["pos"]) : 0;
    
    $next_action = isset($_REQUEST["next_action"]) ? $_REQUEST["next_action"] : '';

    $n_cart_current = isset($_COOKIE["n_cart"]) ? intval($_COOKIE["n_cart"]) : 0;
    $cart_array = isset($_COOKIE["cart"]) && is_array($_COOKIE["cart"]) ? $_COOKIE["cart"] : [];

    switch ($kind) {
        case "insert":
            $n_cart_new = $n_cart_current + 1;
            $new_item_value = implode("^", array($id, $num, $opts_id1, $opts_id2));
            
            setcookie("cart[$n_cart_new]", $new_item_value, $cookie_expiry_time, $cookie_path, $cookie_domain, false, false);
            setcookie("n_cart", (string)$n_cart_new, $cookie_expiry_time, $cookie_path, $cookie_domain, false, false);
            break;

        case "delete":
            if ($pos > 0 && $pos <= $n_cart_current && isset($cart_array[$pos])) {
                setcookie("cart[$pos]", "", time() - 3600, $cookie_path, $cookie_domain, false, false);

                unset($cart_array[$pos]);
                
                $temp_cart = [];
                $new_idx = 1;
                foreach ($cart_array as $item_value) {
                    if (!empty($item_value)) {
                       $temp_cart[$new_idx] = $item_value;
                       $new_idx++;
                    }
                }
                $cart_array = $temp_cart;
                $n_cart_new = count($cart_array);

                for ($i = $n_cart_new + 1; $i <= $n_cart_current; $i++) {
                    setcookie("cart[$i]", "", time() - 3600, $cookie_path, $cookie_domain, false, false);
                }
                
                foreach ($cart_array as $idx => $item_value) {
                    setcookie("cart[$idx]", $item_value, $cookie_expiry_time, $cookie_path, $cookie_domain, false, false);
                }
                setcookie("n_cart", (string)$n_cart_new, $cookie_expiry_time, $cookie_path, $cookie_domain, false, false);
            }
            break;

        case "update":
            if ($pos > 0 && $pos <= $n_cart_current && isset($cart_array[$pos]) && $num > 0) {
                $item_parts = explode("^", $cart_array[$pos]);
                $item_parts[1] = $num;
                $updated_item_value = implode("^", $item_parts);
                
                setcookie("cart[$pos]", $updated_item_value, $cookie_expiry_time, $cookie_path, $cookie_domain, false, false);
            }
            break;

        case "deleteall":
            if ($n_cart_current > 0 && !empty($cart_array)) {
                for ($i = 1; $i <= $n_cart_current; $i++) {
                    if (isset($cart_array[$i])) {
                        setcookie("cart[$i]", "", time() - 3600, $cookie_path, $cookie_domain, false, false);
                    }
                }
            }
            if (isset($_COOKIE["n_cart"])) {
                setcookie("n_cart", "0", time() - 3600, $cookie_path, $cookie_domain, false, false);
            }
            break;
    }

    if (($kind == "insert") && $next_action == "direct_order") {
        header("Location: order.php");
    } else {
        header("Location: cart.php");
    }
    exit();
?>