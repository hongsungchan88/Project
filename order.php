<?php
    include "main_top.php";
    include "common.php";

    // 쿠키에서 카트 정보 가져오기
    $cart = isset($_COOKIE['cart']) ? $_COOKIE['cart'] : [];
    $n_cart = 0;
    if (is_array($cart)) {
        foreach ($cart as $item) { if ($item) $n_cart++; }
    }

    // 로그인한 사용자 정보 가져오기
    $cookie_id = isset($_COOKIE["cookie_id"]) ? $_COOKIE["cookie_id"] : "";
    $row_member = null;
    if ($cookie_id) {
        $sql = "select * from member where uid='$cookie_id'";
        $result = mysqli_query($db, $sql);
        if ($result) $row_member = mysqli_fetch_array($result);
    }
?>
<script>
    // 유효성 검사 (form2 이름에 맞게 수정됨)
    function Check_Value() {
        if (!form2.o_name.value) { alert("주문자 이름을 입력해주세요."); form2.o_name.focus(); return; }
        if (!form2.o_tel1.value || !form2.o_tel2.value || !form2.o_tel3.value) { alert("주문자 휴대폰 번호를 올바르게 입력해주세요."); form2.o_tel1.focus(); return; }
        if (!form2.o_zip.value) { alert("주문자 우편번호를 입력해주세요."); form2.o_zip.focus(); return; }
        if (!form2.o_juso.value) { alert("주문자 주소를 입력해주세요."); form2.o_juso.focus(); return; }

        if (!form2.r_name.value) { alert("받는 분 이름을 입력해주세요."); form2.r_name.focus(); return; }
        if (!form2.r_tel1.value || !form2.r_tel2.value || !form2.r_tel3.value) { alert("받는 분 휴대폰 번호를 올바르게 입력해주세요."); form2.r_tel1.focus(); return; }
        if (!form2.r_zip.value) { alert("받는 분 우편번호를 입력해주세요."); form2.r_zip.focus(); return; }
        if (!form2.r_juso.value) { alert("받는 분 주소를 입력해주세요."); form2.r_juso.focus(); return; }
        form2.submit();
    }
    // 우편번호 찾기
    function FindZip(zip_kind) {
        window.open("zipcode.php?zip_kind="+zip_kind, "", "scrollbars=no,width=490,height=320");
    }
    // 정보 복사
    function SameCopy(checked) {
        if (checked) {
            form2.r_name.value = form2.o_name.value;
            form2.r_zip.value = form2.o_zip.value;
            form2.r_juso.value = form2.o_juso.value;
            form2.r_tel1.value = form2.o_tel1.value;
            form2.r_tel2.value = form2.o_tel2.value;
            form2.r_tel3.value = form2.o_tel3.value;
        } else {
            form2.r_name.value = "";
            form2.r_zip.value = "";
            form2.r_juso.value = "";
            form2.r_tel1.value = "";
            form2.r_tel2.value = "";
            form2.r_tel3.value = "";
        }
    }
</script>

<div class="container my-5">
    <div class="row justify-content-center mb-5">
        <div class="col-md-8">
            <div class="progress-container">
                <div class="progress-step done"><div class="progress-step-icon"><i class="bi bi-cart"></i></div><div class="progress-step-text">장바구니</div></div>
                <div class="progress-line"></div>
                <div class="progress-step active"><div class="progress-step-icon"><i class="bi bi-receipt-cutoff"></i></div><div class="progress-step-text">주문결제</div></div>
                <div class="progress-line"></div>
                <div class="progress-step"><div class="progress-step-icon"><i class="bi bi-check-lg"></i></div><div class="progress-step-text">주문완료</div></div>
            </div>
        </div>
    </div>
    
    <form name="form2" method="post" action="order_pay.php">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header"><h5 class="mb-0">주문자 정보</h5></div>
                    <div class="card-body">
                        <?php
                            // 전화번호 파싱 (substr 사용)
                            $tel1 = $tel2 = $tel3 = "";
                            if ($row_member && isset($row_member["tel"])) {
                                $tel = $row_member["tel"];
                                $tel1 = substr($tel, 0, 3);
                                $tel2 = substr($tel, 3, 4);
                                $tel3 = substr($tel, 7, 4);
                            }
                        ?>
                        <div class="mb-3"><label class="form-label">이름</label><input type="text" name="o_name" class="form-control" value="<?=$row_member['name'] ?? ''?>"></div>
                        <div class="mb-3"><label class="form-label">휴대폰</label>
                            <div class="row gx-2 align-items-center"><div class="col"><input type="text" name="o_tel1" class="form-control text-center" value="<?=$tel1?>" maxlength="3" placeholder="010"></div><div class="col-auto">-</div><div class="col"><input type="text" name="o_tel2" class="form-control text-center" value="<?=$tel2?>" maxlength="4" placeholder="1234"></div><div class="col-auto">-</div><div class="col"><input type="text" name="o_tel3" class="form-control text-center" value="<?=$tel3?>" maxlength="4" placeholder="5678"></div></div>
                        </div>
                        <div class="mb-3"><label class="form-label">주소</label><div class="input-group mb-2"><input type="text" name="o_zip" class="form-control" value="<?=$row_member['zip'] ?? ''?>" readonly><button type="button" class="btn btn-outline-secondary" onclick="FindZip(1)">우편번호</button></div><input type="text" name="o_juso" class="form-control" value="<?=$row_member['juso'] ?? ''?>"></div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0">배송지 정보</h5><div class="form-check"><input class="form-check-input" type="checkbox" id="same_as_orderer" onchange="SameCopy(this.checked)"><label class="form-check-label" for="same_as_orderer">주문자와 동일</label></div></div>
                    <div class="card-body">
                         <div class="mb-3"><label class="form-label">받는 분 이름</label><input type="text" name="r_name" class="form-control"></div>
                         <div class="mb-3"><label class="form-label">휴대폰</label><div class="row gx-2 align-items-center"><div class="col"><input type="text" name="r_tel1" class="form-control text-center" maxlength="3" placeholder="010"></div><div class="col-auto">-</div><div class="col"><input type="text" name="r_tel2" class="form-control text-center" maxlength="4" placeholder="1234"></div><div class="col-auto">-</div><div class="col"><input type="text" name="r_tel3" class="form-control text-center" maxlength="4" placeholder="5678"></div></div></div>
                         <div class="mb-3"><label class="form-label">주소</label><div class="input-group mb-2"><input type="text" name="r_zip" class="form-control" readonly><button type="button" class="btn btn-outline-secondary" onclick="FindZip(2)">우편번호</button></div><input type="text" name="r_juso" class="form-control"></div>
                         <div><label class="form-label">배송 메모</label><textarea name="memo" class="form-control" rows="3"></textarea></div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-5">
                <div class="card shadow-sm" style="position: sticky; top: 2rem;">
                    <div class="card-header"><h5 class="mb-0">주문 요약</h5></div>
                    <div class="card-body">
                        <?
                        $total = 0;
                        for ($i = 1; $i <= count($cart); $i++) {
                            if (isset($cart[$i]) && $cart[$i]) {
                                list($id, $num, ,) = explode("^", $cart[$i]);
                                $sql = "select * from product where id = $id";
                                $res = mysqli_query($db, $sql);
                                if (!$res) continue;
                                $row_prod = mysqli_fetch_array($res);
                                $unit_price = $row_prod["icon_sale"] == 1 ? round($row_prod["price"] * (100 - $row_prod["discount"]) / 100, -2) : $row_prod["price"];
                                $line_total = $unit_price * $num;
                                $total += $line_total;
                        ?>
                        <div class="d-flex mb-3"><img src="product/<?=$row_prod['image1']?>" width="60" class="rounded me-3"><div class="flex-grow-1"><div class="small"><?=$row_prod['name']?></div><div class="small text-muted">수량: <?=$num?>개</div></div><div class="small text-end ms-3"><?=number_format($line_total)?>원</div></div>
                        <?
                            }
                        }
                        ?>
                        <hr>
                        <ul class="list-group list-group-flush"><li class="list-group-item d-flex justify-content-between">상품 금액<span><?=number_format($total)?> 원</span></li><li class="list-group-item d-flex justify-content-between">배송비<span><? $shipping_fee = ($total > 0 && $total < $max_baesongbi) ? $baesongbi : 0; echo "+ " . number_format($shipping_fee) . " 원"; $total += $shipping_fee; ?></span></li><li class="list-group-item d-flex justify-content-between fw-bold fs-5">총 합계<span class="text-danger"><?=number_format($total)?>원</span></li></ul>
                    </div>
                    <div class="card-footer p-3"><div class="d-grid"><button type="button" class="btn btn-dark btn-lg" onclick="Check_Value()">결제 정보 입력하기</button></div></div>
                </div>
            </div>
        </div>
    </form>
</div>