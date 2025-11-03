<?
    include "main_top.php";
    include "common.php";

    // --- 기존 PHP 로직은 변경 없습니다 ---
    $id = $_REQUEST["id"];
    $sql = "select * from product where id=$id";
    $result = mysqli_query($db, $sql);
    $row = mysqli_fetch_array($result);

    $price = $row["price"];
    $discount = $row["discount"];
    $sale_price = $discount ? round($price * (100 - $discount) / 100, -2) : $price;
?>
<script>
    // 수량 변경 및 총 금액 계산
    function cal_price() {
        let num = parseInt(form_purchase.num.value);
        let price = parseInt(form_purchase.price.value);
        let total = num * price;
        document.getElementById("total_price_display").innerText = total.toLocaleString() + "원";
    }

    // 수량 +, - 버튼을 위한 함수
    function change_num(delta) {
        let num_input = form_purchase.num;
        let current_num = parseInt(num_input.value);
        let new_num = current_num + delta;

        if (new_num > 0) {
            num_input.value = new_num;
            cal_price(); // 수량 변경 시 총 금액 즉시 업데이트
        }
    }

    // 장바구니/바로구매 버튼 클릭 시 유효성 검사
    function check_purchase_form(kind) {
        if (<?=$row["opt1"]?> > 0 && form_purchase.opts1.value == 0) {
            alert("옵션1을 선택하세요.");
            form_purchase.opts1.focus();
            return;
        }
        if (<?=$row["opt2"]?> > 0 && form_purchase.opts2.value == 0) {
            alert("옵션2를 선택하세요.");
            form_purchase.opts2.focus();
            return;
        }
        
        form_purchase.kind.value = "insert";
        form_purchase.action = "cart_edit.php";
        
        if (kind == "direct") { // 바로구매
             // 기존 hidden input이 있으면 사용, 없으면 생성
            let nextPageInput = form_purchase.elements['next_action'];
            if (!nextPageInput) {
                nextPageInput = document.createElement('input');
                nextPageInput.type = 'hidden';
                nextPageInput.name = 'next_action';
                form_purchase.appendChild(nextPageInput);
            }
            nextPageInput.value = "direct_order";
        }
        
        form_purchase.submit();
    }
</script>

<div class="container my-5">
    <form name="form_purchase" method="post" action="">
        <input type="hidden" name="kind">
        <input type="hidden" name="id" value="<?=$id?>">
        <input type="hidden" name="price" value="<?=$sale_price?>">

        <div class="row g-5">
            <div class="col-md-6">
                <? $image_path = $row["image2"] ? "product/".$row["image2"] : "product/nopic.png"; ?>
                <img src="<?=$image_path?>" class="img-fluid rounded shadow-sm" style="cursor:zoom-in" data-bs-toggle="modal" data-bs-target="#zoomModal">
            </div>

            <div class="col-md-6">
                <h2 class="fw-bold"><?=$row["name"]?></h2>
                <div class="mb-3">
                    <? if($row['icon_new'] == 1) echo "<span class='badge bg-primary me-1'>NEW</span>"; ?>
                    <? if($row['icon_hit'] == 1) echo "<span class='badge bg-success me-1'>HIT</span>"; ?>
                    <? if($row['icon_sale'] == 1) echo "<span class='badge bg-danger'>SALE $discount%</span>"; ?>
                </div>
                <div class="mb-3">
                    <? if($discount > 0): ?>
                        <span class="text-muted fs-5 me-2"><strike><?=number_format($price)?>원</strike></span>
                    <? endif; ?>
                    <strong class="text-danger" style="font-size: 2rem;"><?=number_format($sale_price)?>원</strong>
                </div>
                <hr>
                <? if($row["opt1"]): ?>
                <div class="mb-3">
                    <label class="form-label"><strong><?=$a_opt_name[$row["opt1"]]?></strong></label>
                    <select name="opts1" class="form-select">
                        <option value="0" selected>선택하세요</option>
                        <?
                            $sql_opt = "select * from opts where opt_id = {$row['opt1']} order by name";
                            $res_opt = mysqli_query($db, $sql_opt);
                            foreach ($res_opt as $row_opt) echo("<option value='{$row_opt['id']}'>{$row_opt['name']}</option>");
                        ?>
                    </select>
                </div>
                <? endif; ?>
                <? if($row["opt2"]): ?>
                <div class="mb-3">
                    <label class="form-label"><strong><?=$a_opt_name[$row["opt2"]]?></strong></label>
                    <select name="opts2" class="form-select">
                        <option value="0" selected>선택하세요</option>
                        <?
                            $sql_opt = "select * from opts where opt_id = {$row['opt2']} order by name";
                            $res_opt = mysqli_query($db, $sql_opt);
                            foreach ($res_opt as $row_opt) echo("<option value='{$row_opt['id']}'>{$row_opt['name']}</option>");
                        ?>
                    </select>
                </div>
                <? endif; ?>
                <div class="mb-4">
                     <label class="form-label"><strong>수량</strong></label>
                     <div class="input-group" style="max-width: 150px;">
                        <button class="btn btn-outline-secondary" type="button" onclick="change_num(-1)">-</button>
                        <input type="text" class="form-control text-center" name="num" value="1" onchange="cal_price()">
                        <button class="btn btn-outline-secondary" type="button" onclick="change_num(1)">+</button>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fs-5">총 합계 금액</span>
                    <span id="total_price_display" class="fs-3 fw-bold text-danger"><?=number_format($sale_price)?>원</span>
                </div>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-dark btn-lg" onclick="check_purchase_form('direct')">바로 구매</button>
                    <button type="button" class="btn btn-outline-dark btn-lg" onclick="check_purchase_form('cart')">장바구니</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="container my-5">
    <hr>
    <ul class="nav nav-tabs mt-5" id="myTab" role="tablist">
        <li class="nav-item" role="presentation"><button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab">상세정보</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">배송/반품 안내</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">상품 후기</button></li>
    </ul>
    <div class="tab-content py-4" id="myTabContent">
        <div class="tab-pane fade show active text-center" id="home" role="tabpanel">
            <?=stripslashes($row["contents"])?><br>
            <? if($row["image3"]) echo("<img src='product/{$row['image3']}' class='img-fluid mt-3'>"); ?>
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel"><p>배송 및 반품 안내 내용입니다.</p></div>
        <div class="tab-pane fade" id="contact" role="tabpanel"><p>상품 후기 내용입니다.</p></div>
    </div>
</div>


<div class="modal fade" id="zoomModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body p-0">
        <img src="<?=$image_path?>" class="img-fluid" data-bs-dismiss="modal">
      </div>
    </div>
  </div>
</div>

<?
    include "main_bottom.php";
?>