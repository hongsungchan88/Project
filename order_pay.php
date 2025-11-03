<?php
    include "main_top.php";
    include "common.php";
    
    // 이전 페이지(order.php)에서 POST로 받은 데이터 변수 할당
    foreach ($_POST as $key => $val) { $$key = $val; }
    $o_tel = $o_tel1 . "-" . $o_tel2 . "-" . $o_tel3;
    $r_tel = $r_tel1 . "-" . $r_tel2 . "-" . $r_tel3;
    
    // 카트 정보 재계산
    $cart = isset($_COOKIE['cart']) ? $_COOKIE['cart'] : [];
    $total = 0;
    for ($i = 1; $i <= count($cart); $i++) {
        if (isset($cart[$i]) && $cart[$i]) {
            list($id, $num, ,) = explode("^", $cart[$i]);
            $sql = "select * from product where id = $id";
            $res = mysqli_query($db, $sql);
            if (!$res) continue;
            $row_prod = mysqli_fetch_array($res);
            $unit_price = $row_prod["icon_sale"] == 1 ? round($row_prod["price"] * (100 - $row_prod["discount"]) / 100, -2) : $row_prod["price"];
            $total += $unit_price * $num;
        }
    }
    $shipping_fee = ($total > 0 && $total < $max_baesongbi) ? $baesongbi : 0;
    $order_total = $total + $shipping_fee;
?>
<script>
    // 유효성 검사 (form_pay 이름에 맞게 수정됨)
    function Check_Value() {
        var payMethodRadio = document.querySelector('input[name="pay_kind"]:checked');
        if (payMethodRadio && payMethodRadio.value == 0) { // 카드
            if (form_pay.card_kind.value == "0") { alert("카드사를 선택하세요."); form_pay.card_kind.focus(); return; }
            if (!form_pay.card_no1.value || !form_pay.card_no2.value || !form_pay.card_no3.value || !form_pay.card_no4.value) { alert("카드번호를 모두 입력하세요."); form_pay.card_no1.focus(); return; }
            if (!form_pay.card_month.value || !form_pay.card_year.value) { alert("카드 유효기간을 입력하세요."); form_pay.card_month.focus(); return; }
            if (!form_pay.card_pw.value) { alert("카드 비밀번호 뒷 2자리를 입력하세요."); form_pay.card_pw.focus(); return; }
        } else { // 무통장
            if (form_pay.bank_kind.value == "0") { alert("입금할 은행을 선택하세요."); form_pay.bank_kind.focus(); return; }
            if (!form_pay.bank_sender.value) { alert("입금자 이름을 입력하세요."); form_pay.bank_sender.focus(); return; }
        }
        form_pay.submit();
    }
    // 페이지 로드 및 탭 변경 시, 숨겨진 라디오 버튼의 상태를 동기화
    document.addEventListener("DOMContentLoaded", function() {
        function sync_pay_kind() {
            let cardTabActive = document.getElementById('pills-card-tab').classList.contains('active');
            document.querySelector('input[name="pay_kind"][value="0"]').checked = cardTabActive;
            document.querySelector('input[name="pay_kind"][value="1"]').checked = !cardTabActive;
        }
        sync_pay_kind(); // 페이지 첫 로드 시 동기화
        var cardTab = document.getElementById('pills-card-tab');
        var bankTab = document.getElementById('pills-bank-tab');
        cardTab.addEventListener('shown.bs.tab', sync_pay_kind);
        bankTab.addEventListener('shown.bs.tab', sync_pay_kind);
    });
</script>

<div class="container my-5">
    <div class="row justify-content-center mb-5">
        <div class="col-md-8">
             <div class="progress-container"><div class="progress-step done"><div class="progress-step-icon"><i class="bi bi-cart"></i></div><div class="progress-step-text">장바구니</div></div><div class="progress-line"></div><div class="progress-step active"><div class="progress-step-icon"><i class="bi bi-receipt-cutoff"></i></div><div class="progress-step-text">주문결제</div></div><div class="progress-line"></div><div class="progress-step"><div class="progress-step-icon"><i class="bi bi-check-lg"></i></div><div class="progress-step-text">주문완료</div></div></div>
        </div>
    </div>
    
    <form name="form_pay" method="post" action="order_insert.php">
        <input type="hidden" name="o_name" value="<?=$o_name?>"><input type="hidden" name="o_tel" value="<?=$o_tel?>"><input type="hidden" name="o_juso" value="<?=$o_juso?>">
        <input type="hidden" name="r_name" value="<?=$r_name?>"><input type="hidden" name="r_tel" value="<?=$r_tel?>"><input type="hidden" name="r_juso" value="<?=$r_juso?>"><input type="hidden" name="memo" value="<?=$memo?>">
        <input type="hidden" name="order_total" value="<?=$order_total?>">

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header"><h5 class="mb-0">결제 수단 선택</h5></div>
                    <div class="card-body">
                        <ul class="nav nav-pills nav-fill mb-4" id="pills-tab" role="tablist"><li class="nav-item" role="presentation"><button class="nav-link active" id="pills-card-tab" data-bs-toggle="pill" data-bs-target="#pills-card" type="button" role="tab" aria-selected="true"><i class="bi bi-credit-card-fill"></i> 카드 결제</button></li><li class="nav-item" role="presentation"><button class="nav-link" id="pills-bank-tab" data-bs-toggle="pill" data-bs-target="#pills-bank" type="button" role="tab" aria-selected="false"><i class="bi bi-bank"></i> 무통장 입금</button></li></ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-card" role="tabpanel">
                                <input type="radio" name="pay_kind" value="0" class="d-none">
                                <div class="mb-3"><label class="form-label">카드 종류</label><select name="card_kind" class="form-select"><option value="0" selected>카드사를 선택하세요</option><option value="1">국민카드</option><option value="2">신한카드</option></select></div>
                                <div class="mb-3"><label class="form-label">카드 번호</label><div class="input-group"><input type="text" name="card_no1" class="form-control text-center" maxlength="4"><span class="input-group-text">-</span><input type="text" name="card_no2" class="form-control text-center" maxlength="4"><span class="input-group-text">-</span><input type="text" name="card_no3" class="form-control text-center" maxlength="4"><span class="input-group-text">-</span><input type="text" name="card_no4" class="form-control text-center" maxlength="4"></div></div>
                                <div class="row"><div class="col-md-7 mb-3"><label class="form-label">유효기간</label><div class="input-group"><input type="text" name="card_month" class="form-control text-center" maxlength="2" placeholder="MM"><span class="input-group-text">/</span><input type="text" name="card_year" class="form-control text-center" maxlength="2" placeholder="YY"></div></div><div class="col-md-5 mb-3"><label class="form-label">비밀번호 앞 2자리</label><input type="password" name="card_pw" class="form-control text-center" maxlength="2" placeholder="**"></div></div>
                                <div class="mb-3"><label class="form-label">할부 기간</label><select name="card_halbu" class="form-select"><option value="0" selected>일시불</option><option value="3">3개월</option><option value="6">6개월</option></select></div>
                            </div>
                            <div class="tab-pane fade" id="pills-bank" role="tabpanel">
                                <input type="radio" name="pay_kind" value="1" class="d-none">
                                <div class="mb-3"><label class="form-label">입금 은행</label><select name="bank_kind" class="form-select"><option value="0" selected>은행을 선택하세요</option><option value="1">국민은행 111-111-1111</option></select></div>
                                <div class="mb-3"><label class="form-label">입금자명</label><input type="text" name="bank_sender" class="form-control"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-sm" style="position: sticky; top: 2rem;">
                    <div class="card-header"><h5 class="mb-0">최종 결제 금액</h5></div>
                    <div class="card-body">
                         <ul class="list-group list-group-flush"><li class="list-group-item d-flex justify-content-between">상품 금액<span><?=number_format($total)?> 원</span></li><li class="list-group-item d-flex justify-content-between">배송비<span><?= "+ " . number_format($shipping_fee) . " 원"?></span></li><li class="list-group-item d-flex justify-content-between fw-bold fs-5">총 합계<span class="text-danger"><?=number_format($order_total)?> 원</span></li></ul>
                    </div>
                    <div class="card-footer p-3"><div class="d-grid"><button type="button" class="btn btn-dark btn-lg" onclick="Check_Value()"><h3><?=number_format($order_total)?>원</h3>결제하기</button></div></div>
                </div>
            </div>
        </div>
    </form>
</div>