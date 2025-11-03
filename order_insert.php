<?php
    include "common.php";

    $today_date_ymd = date("ymd");
    $today_date_sql = date("Y-m-d");

    $NewOdNum = "";
    $sql_last_id = "SELECT id FROM jumun WHERE jumunday = CURDATE() ORDER BY id DESC LIMIT 1";
    $result_last_id = mysqli_query($db, $sql_last_id);

    if (!$result_last_id) {
        die("주문번호 조회 오류: " . mysqli_error($db));
    }

    if (mysqli_num_rows($result_last_id) > 0) {
        $row_last_id = mysqli_fetch_assoc($result_last_id);
        $last_full_id = $row_last_id['id'];
        $last_sequence_str = substr($last_full_id, -4);
        $next_sequence_num = intval($last_sequence_str) + 1;
        $NewOdNum = $today_date_ymd . str_pad($next_sequence_num, 4, '0', STR_PAD_LEFT);
    } else {
        $NewOdNum = $today_date_ymd . "0001";
    }

    $n_cart = isset($_COOKIE['n_cart']) ? intval($_COOKIE['n_cart']) : 0;
    $cart_cookie_items = isset($_COOKIE['cart']) && is_array($_COOKIE['cart']) ? $_COOKIE['cart'] : [];

    $product_nums_for_jumun = 0;
    $first_product_name_for_jumun = "";
    $total_order_value = 0;

    if ($n_cart > 0 && !empty($cart_cookie_items)) {
        mysqli_begin_transaction($db);

        try {
            for ($i = 1; $i <= $n_cart; $i++) {
                if (isset($cart_cookie_items[$i]) && !empty($cart_cookie_items[$i])) {
                    list($product_id_str, $num_str, $opts_id1_str, $opts_id2_str) = explode("^", $cart_cookie_items[$i]);

                    $product_id = intval($product_id_str);
                    $num = intval($num_str);

                    $opts_id1_val = (!empty($opts_id1_str) && intval($opts_id1_str) != 0) ? intval($opts_id1_str) : null;
                    $opts_id2_val = (!empty($opts_id2_str) && intval($opts_id2_str) != 0) ? intval($opts_id2_str) : null;

                    if ($product_id <= 0 || $num <= 0) continue;

                    $sql_product_details = "SELECT name, price, discount, icon_sale FROM product WHERE id = $product_id";
                    $res_product_details = mysqli_query($db, $sql_product_details);

                    if ($res_product_details && $row_product = mysqli_fetch_assoc($res_product_details)) {
                        $original_price = floatval($row_product['price']);
                        $discount_percent = floatval($row_product['discount']);
                        $is_on_sale = intval($row_product['icon_sale']);

                        $unit_price_paid = $original_price;
                        if ($is_on_sale == 1 && $discount_percent > 0) {
                            $unit_price_paid = round(($original_price * (100 - $discount_percent)) / 100, -3);
                        }

                        $item_total_price = $unit_price_paid * $num;
                        $total_order_value += $item_total_price;

                        $product_nums_for_jumun++;
                        if ($product_nums_for_jumun == 1) {
                            $first_product_name_for_jumun = mysqli_real_escape_string($db, $row_product['name']);
                        }
                        
                        $opts_id1_sql = ($opts_id1_val === null) ? "NULL" : $opts_id1_val;
                        $opts_id2_sql = ($opts_id2_val === null) ? "NULL" : $opts_id2_val;

                        $sql_insert_jumuns = "INSERT INTO jumuns (jumun_id, product_id, num, price, prices, discount, opts_id1, opts_id2)
                                            VALUES ('$NewOdNum', $product_id, $num, $unit_price_paid, $item_total_price, $discount_percent, $opts_id1_sql, $opts_id2_sql)";

                        if (!mysqli_query($db, $sql_insert_jumuns)) {
                            throw new Exception("jumuns 테이블 상품 추가 오류: " . mysqli_error($db) . " (Query: $sql_insert_jumuns)");
                        }
                    } else {
                        throw new Exception("상품 ID $product_id 를 찾을 수 없거나 상세 정보 조회 오류: " . mysqli_error($db));
                    }
                }
            }

            if ($product_nums_for_jumun == 0) {
                throw new Exception("장바구니에 유효한 상품이 없습니다. 주문을 진행할 수 없습니다.");
            }

            if ($total_order_value < $max_baesongbi) {
                $total_order_value += $baesongbi;
                $shipping_product_id = 0;
                $shipping_num = 1;
                $shipping_price = $baesongbi;
                $shipping_prices = $baesongbi;
                $shipping_discount = 0;

                $sql_insert_shipping = "INSERT INTO jumuns (jumun_id, product_id, num, price, prices, discount, opts_id1, opts_id2)
                    VALUES ('$NewOdNum', $shipping_product_id, $shipping_num, $shipping_price, $shipping_prices, $shipping_discount, NULL, NULL)";
                if (!mysqli_query($db, $sql_insert_shipping)) {
                    throw new Exception("jumuns 테이블 배송비 항목 추가 오류: " . mysqli_error($db) . " (Query: $sql_insert_shipping)");
                }    
        }

            $resolved_member_id = 0;
            if (isset($_COOKIE["cookie_id"])) {
                $resolved_member_id = $_COOKIE["cookie_id"];
            }

            $product_names_for_jumun_table = "";
            if ($product_nums_for_jumun > 0) {
                if ($product_nums_for_jumun == 1) {
                    $product_names_for_jumun_table = $first_product_name_for_jumun;
                } else {
                    $product_names_for_jumun_table = $first_product_name_for_jumun . " 외 " . ($product_nums_for_jumun - 1) . "건";
                }
            }

            $o_name_val = isset($_POST['o_name']) ? mysqli_real_escape_string($db, trim($_POST['o_name'])) : '';
            $o_tel_val = isset($_POST['o_tel']) ? mysqli_real_escape_string($db, $_POST['o_tel']) : '';
            $o_email_val = isset($_POST['o_email']) ? mysqli_real_escape_string($db, trim($_POST['o_email'])) : '';
            $o_zip_val = isset($_POST['o_zip']) ? mysqli_real_escape_string($db, trim($_POST['o_zip'])) : '';

            $temp_o_juso = isset($_POST['o_juso']) ? trim($_POST['o_juso']) : '';
            if (empty($temp_o_juso)) {
                $o_juso_val = '';
            } else {
                $o_juso_val = mysqli_real_escape_string($db, $temp_o_juso);
            }

            $r_name_val = isset($_POST['r_name']) ? mysqli_real_escape_string($db, trim($_POST['r_name'])) : '';
            $r_tel_val = isset($_POST['r_tel']) ? mysqli_real_escape_string($db, $_POST['r_tel']) : '';
            $r_email_val = isset($_POST['r_email']) ? mysqli_real_escape_string($db, trim($_POST['r_email'])) : '';
            $r_zip_val = isset($_POST['r_zip']) ? mysqli_real_escape_string($db, trim($_POST['r_zip'])) : '';

            $temp_r_juso = isset($_POST['r_juso']) ? trim($_POST['r_juso']) : '';
            if (empty($temp_r_juso)) {
                $r_juso_val = '';
            } else {
                $r_juso_val = mysqli_real_escape_string($db, $temp_r_juso);
            }

            $temp_memo = isset($_POST['memo']) ? trim($_POST['memo']) : '';
            if (empty($temp_memo)) {
                $memo_val = '';
            } else {
                $memo_val = mysqli_real_escape_string($db, $temp_memo);
            }

            $pay_kind_val = isset($_POST['pay_kind']) ? intval($_POST['pay_kind']) : 1;
            $card_okno_val = ($pay_kind_val == 0 && isset($_POST['card_okno'])) ? mysqli_real_escape_string($db, trim($_POST['card_okno'])) : '';

        $card_halbu_for_sql = "NULL";
        if ($pay_kind_val == 0) {
            if (isset($_POST['card_halbu']) && trim((string)$_POST['card_halbu']) !== '') {
                $halbu_input = trim((string)$_POST['card_halbu']);
                if (ctype_digit($halbu_input)) {
                    $card_halbu_for_sql = intval($halbu_input);
                } else {
                    $card_halbu_for_sql = 0;
                }
            } else {
                $card_halbu_for_sql = 0;
            }
        }
        $card_kind_for_sql = "NULL";
        if ($pay_kind_val == 0) {
            if (isset($_POST['card_kind']) && trim((string)$_POST['card_kind']) !== '') {
                $ck_input = trim((string)$_POST['card_kind']);
                if (ctype_digit($ck_input) && intval($ck_input) > 0) {
                    $card_kind_for_sql = intval($ck_input);
                }
            }
        }

        $bank_kind_for_sql = "NULL";
        if ($pay_kind_val == 1) {
            if (isset($_POST['bank_kind']) && trim((string)$_POST['bank_kind']) !== '') {
                $bk_input = trim((string)$_POST['bank_kind']);
                if (ctype_digit($bk_input) && intval($bk_input) > 0) {
                    $bank_kind_for_sql = intval($bk_input);
                }
            }
        }
        $bank_sender_val = ($pay_kind_val == 1 && isset($_POST['bank_sender'])) ? mysqli_real_escape_string($db, trim($_POST['bank_sender'])) : '';

        $state_val = 1;

        $sql_insert_jumun = "INSERT INTO jumun (
                                id, member_id, jumunday, product_names, product_nums,
                                o_name, o_tel, o_email, o_zip, o_juso,
                                r_name, r_tel, r_email, r_zip, r_juso,
                                memo,
                                pay_kind, card_okno, card_halbu, card_kind,
                                bank_kind, bank_sender,
                                totalprice, state
                             ) VALUES (
                                '$NewOdNum', '$resolved_member_id', '$today_date_sql', '$product_names_for_jumun_table', $product_nums_for_jumun,
                                '$o_name_val', '$o_tel_val', '$o_email_val', '$o_zip_val', '$o_juso_val',
                                '$r_name_val', '$r_tel_val', '$r_email_val', '$r_zip_val', '$r_juso_val',
                                '$memo_val',
                                $pay_kind_val, '$card_okno_val', $card_halbu_for_sql, $card_kind_for_sql,
                                $bank_kind_for_sql, '$bank_sender_val',
                                $total_order_value, $state_val
                             )";

            if (!mysqli_query($db, $sql_insert_jumun)) {
                throw new Exception("jumun 테이블 주문 추가 오류: " . mysqli_error($db) . " (Query: $sql_insert_jumun)");
            }

            mysqli_commit($db);

            $cookie_path = "/~shop43";
            $cookie_domain = "gamejigix.induk.ac.kr";
            $expiry_in_past = time() - 3600;

            if ($n_cart > 0 && !empty($cart_cookie_items)) {
                for ($i = 1; $i <= $n_cart; $i++) {
                    if (isset($_COOKIE['cart'][$i])) {
                        // setcookie("cart[$i]", "", time() - 3600);
                        setcookie("cart[$i]", "", $expiry_in_past, $cookie_path, $cookie_domain, false, false);
                    }
                }
                if (isset($_COOKIE["n_cart"])) {
                    // setcookie("n_cart", "", time() - 3600);
                    setcookie("n_cart", "", $expiry_in_past, $cookie_path, $cookie_domain, false, false);
                }
            }

            echo("<script>location.href='order_ok.php?od_id=" . urlencode($NewOdNum) . "'</script>");
            exit();

        } catch (Exception $e) {
            mysqli_rollback($db);
            echo "오류가 발생하여 주문 처리에 실패했습니다: " . $e->getMessage();
        }

    } else {
        echo "장바구니가 비어있습니다. 추가할 상품이 없습니다.";
        if ($n_cart > 0 && empty($cart_cookie_items)) {
            echo "<br>주의: n_cart 쿠키는 있으나 cart 배열 쿠키가 비어있습니다. 쿠키 설정을 확인해주세요.";
        }
    }
?>