<?php
    include "common.php";
    include "main_top.php";

    // --- 기존 PHP 로직은 유지하되, 변수 초기화 추가 ---
    $total = 0;
    $cart = isset($_COOKIE['cart']) ? $_COOKIE['cart'] : [];
    $n_cart = 0;
    // 실제 담긴 상품 수만 카운트
    if (is_array($cart)) {
        foreach ($cart as $item) {
            if ($item) $n_cart++;
        }
    }
?>
<script>
    // 기존 cart_edit 함수 유지
    function cart_edit(kind, pos, num = 1) { // num 기본값 추가
        if (kind == "deleteall") {
            if (confirm("장바구니를 모두 비우시겠습니까?")) {
                location.href = "cart_edit.php?kind=deleteall";
            }
        } else if (kind == "delete") {
            location.href = "cart_edit.php?kind=delete&pos="+pos;
        } else if (kind == "update") {
            var new_num = document.getElementById("num"+pos).value;
            location.href = "cart_edit.php?kind=update&pos="+pos+"&num="+new_num;
        }
    }
    
    // 수량 +, - 버튼을 위한 새로운 함수
    function change_num(pos, delta) {
        var num_input = document.getElementById("num"+pos);
        var current_num = parseInt(num_input.value);
        var new_num = current_num + delta;

        if (new_num > 0) {
            num_input.value = new_num;
            cart_edit('update', pos); // 변경된 수량으로 바로 업데이트
        }
    }
</script>

<div class="container my-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold">장바구니</h2>
    </div>

    <? if ($n_cart == 0) : ?>
        <div class="text-center py-5 card shadow-sm">
            <i class="bi bi-cart-x" style="font-size: 4rem;"></i>
            <h4 class="mt-3">장바구니에 담긴 상품이 없습니다.</h4>
            <p class="text-muted">마음에 드는 상품을 담아보세요.</p>
            <div class="mt-4">
                <a href="main.php" class="btn btn-dark">쇼핑 계속하기</a>
            </div>
        </div>

    <? else : ?>
        <form name="form2" method="post" action="">
            <div class="row">
                <div class="col-lg-8">
                    <?php
                        // ★★★★★ 로직 오류 수정 ★★★★★
                        // 기존 코드는 반복문 구조가 잘못되어 있었습니다.
                        // if($cart[$i]) 블록 안에 상품 표시 HTML 전체가 들어가도록 수정했습니다.
                        for ($i = 1; $i <= count($cart); $i++) {
                            if (isset($cart[$i]) && $cart[$i]) {
                                list($id, $num, $opts_id1, $opts_id2) = explode("^", $cart[$i]);

                                $sql_prod = "select * from product where id = $id";
                                $res_prod = mysqli_query($db, $sql_prod);
                                if (!$res_prod) continue;
                                $row_prod = mysqli_fetch_array($res_prod);

                                $opt1_name = "";
                                if ($opts_id1) {
                                    $sql_opt1 = "select name from opts where id=$opts_id1";
                                    $res_opt1 = mysqli_query($db, $sql_opt1);
                                    if ($res_opt1) $row_opt1 = mysqli_fetch_array($res_opt1);
                                    $opt1_name = $row_opt1['name'];
                                }
                                
                                $opt2_name = "";
                                if ($opts_id2) {
                                    $sql_opt2 = "select name from opts where id=$opts_id2";
                                    $res_opt2 = mysqli_query($db, $sql_opt2);
                                    if ($res_opt2) $row_opt2 = mysqli_fetch_array($res_opt2);
                                    $opt2_name = $row_opt2['name'];
                                }
                                
                                $unit_price = $row_prod["price"];
                                if ($row_prod["icon_sale"] == 1) {
                                    $unit_price = round($row_prod["price"] * (100 - $row_prod["discount"]) / 100, -2);
                                }
                                $line_total = $unit_price * $num;
                                $total += $line_total;
                    ?>
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <a href="product.php?id=<?=$id?>">
                                    <img src="product/<?=$row_prod['image1']?>" width="90" class="rounded">
                                </a>
                                <div class="flex-grow-1 mx-3">
                                    <a href="product.php?id=<?=$id?>" class="text-decoration-none text-dark fw-bold"><?=$row_prod['name']?></a>
                                    <div class="small text-muted">
                                        <? if($opt1_name) echo "[옵션1: $opt1_name] "; ?>
                                        <? if($opt2_name) echo "[옵션2: $opt2_name]"; ?>
                                    </div>
                                    <div class="mt-1 small">단가: <?=number_format($unit_price)?>원</div>
                                </div>
                                <div style="min-width: 120px;">
                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="change_num(<?=$i?>, -1)">-</button>
                                        <input type="text" class="form-control form-control-sm text-center" name="num<?=$i?>" id="num<?=$i?>" value="<?=$num?>" onchange="cart_edit('update', <?=$i?>)">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="change_num(<?=$i?>, 1)">+</button>
                                    </div>
                                </div>
                                <div class="text-end fw-bold mx-4" style="min-width: 100px;">
                                    <?=number_format($line_total)?>원
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="cart_edit('delete', <?=$i?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                            } // end if
                        } // end for
                    ?>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm" style="position: sticky; top: 2rem;">
                        <div class="card-header">
                            <h5 class="mb-0">주문 요약</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    상품 금액
                                    <span><?=number_format($total)?> 원</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    배송비
                                    <span>
                                        <?php
                                            $shipping_fee = 0;
                                            if ($total < $max_baesongbi && $total > 0) {
                                                $shipping_fee = $baesongbi;
                                            }
                                            echo "+ " . number_format($shipping_fee) . " 원";
                                            $total += $shipping_fee;
                                        ?>
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center fw-bold fs-5">
                                    총 합계
                                    <span class="text-danger"><?=number_format($total)?> 원</span>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer p-3">
                            <div class="d-grid gap-2">
                                <a href="order.php" class="btn btn-dark btn-lg">결제하기</a>
                                <a href="javascript:cart_edit('deleteall',0)" class="btn btn-outline-secondary btn-sm">장바구니 비우기</a>
                                <a href="main.php" class="btn btn-outline-secondary btn-sm">쇼핑 계속하기</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <? endif; ?>
</div>

<?
    include "main_bottom.php";
?>