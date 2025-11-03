<?php
    include "main_top.php";
    include "common.php";

    $id = $_REQUEST["id"] ?? null;
    if ($id === null) exit("주문 ID가 전달되지 않았습니다.");
    
    // 주문 기본 정보 조회
    $sql_jumun = "SELECT * FROM jumun WHERE id = ?";
    $stmt_jumun = mysqli_prepare($db, $sql_jumun);
    mysqli_stmt_bind_param($stmt_jumun, "s", $id);
    mysqli_stmt_execute($stmt_jumun);
    $result_jumun = mysqli_stmt_get_result($stmt_jumun);
    $row_jumun = mysqli_fetch_array($result_jumun);
    mysqli_stmt_close($stmt_jumun);
    if (!$row_jumun) exit("해당 주문 정보를 찾을 수 없습니다. ID: " . htmlspecialchars($id));

    // 주문 상품 목록 조회
    $order_items = [];
    $sql_items = "SELECT ji.*, p.name as product_name, o1.name as opts1_name, o2.name as opts2_name, p.image1 as p_image
                  FROM jumuns ji
                  LEFT JOIN product p ON ji.product_id = p.id
                  LEFT JOIN opts o1 ON ji.opts_id1 = o1.id
                  LEFT JOIN opts o2 ON ji.opts_id2 = o2.id
                  WHERE ji.jumun_id = ?";
    $stmt_items = mysqli_prepare($db, $sql_items);
    mysqli_stmt_bind_param($stmt_items, "s", $id);
    mysqli_stmt_execute($stmt_items);
    $result_items = mysqli_stmt_get_result($stmt_items);
    if ($result_items) {
        while ($item_row = mysqli_fetch_array($result_items)) {
            $order_items[] = $item_row;
        }
    }
    mysqli_stmt_close($stmt_items);
    
    // 상태값 배열 정의
    $status_map = [
        1 => ['text' => "주문신청", 'color' => "primary"], 2 => ['text' => "주문확인", 'color' => "info"],
        3 => ['text' => "입금확인", 'color' => "warning"], 4 => ['text' => "배송중", 'color' => "success"],
        5 => ['text' => "주문완료", 'color' => "dark"], 6 => ['text' => "주문취소", 'color' => "danger"],
        7 => ['text' => "반품", 'color' => "secondary"],
    ];
    $status_info = $status_map[$row_jumun['state']] ?? ['text' => '알수없음', 'color' => 'light'];

    // ★★★★★ 오류 해결을 위한 계산 로직 추가 ★★★★★
    $subtotal = 0;
    $shipping_fee = 0;
    foreach ($order_items as $item) {
        if ($item['product_id'] == 0) { // 배송비 항목
            $shipping_fee = $item['price'];
        } else { // 일반 상품 항목
            $subtotal += $item['prices'];
        }
    }
?>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h2 class="fw-bold mb-1">주문 상세 내역</h2>
            <div class="small text-muted">
                <span class="me-3"><strong>주문번호:</strong> <?=$row_jumun["id"]?></span>
                <span><strong>주문일:</strong> <?=$row_jumun["jumunday"]?></span>
            </div>
        </div>
        <div>
            <span class="badge bg-<?=$status_info['color']?>" style="font-size: 1rem;"><?=$status_info['text']?></span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0">주문 상품 목록</h5></div>
                <ul class="list-group list-group-flush">
                    <? foreach ($order_items as $item): ?>
                        <li class="list-group-item d-flex align-items-center">
                            <a href="product.php?id=<?=$item['product_id']?>">
                                <img src="product/<?=$item['p_image'] ?? 'nopic.png'?>" width="70" class="rounded">
                            </a>
                            <div class="flex-grow-1 mx-3">
                                <? if($item['product_id'] == 0): ?>
                                    <span class="text-dark fw-bold">배송비</span>
                                <? else: ?>
                                    <a href="product.php?id=<?=$item['product_id']?>" class="text-decoration-none text-dark fw-bold"><?=$item['product_name']?></a>
                                    <div class="small text-muted">
                                        <? if($item['opts1_name'] || $item['opts2_name']): ?>
                                            [옵션] <?=$item['opts1_name']?> <?=$item['opts2_name']?>
                                        <? endif; ?>
                                    </div>
                                    <div class="small text-muted">
                                        <?=number_format($item['price'])?>원 / <?=$item['num']?>개
                                    </div>
                                <? endif; ?>
                            </div>
                            <div class="text-end fw-bold" style="min-width: 80px;">
                                <?=number_format($item['prices'])?>원
                            </div>
                        </li>
                    <? endforeach; ?>
                </ul>
            </div>

            <div class="card shadow-sm">
                <div class="card-header"><h5 class="mb-0">배송 정보</h5></div>
                <div class="card-body">
                    <p><i class="bi bi-person-fill me-2"></i><strong>받는 분:</strong> <?=$row_jumun['r_name']?></p>
                    <p><i class="bi bi-telephone-fill me-2"></i><strong>연락처:</strong> <?=$row_jumun['r_tel']?></p>
                    <p><i class="bi bi-geo-alt-fill me-2"></i><strong>주소:</strong> [<?=$row_jumun['r_zip']?>] <?=$row_jumun['r_juso']?></p>
                    <? if ($row_jumun['memo']): ?>
                        <hr>
                        <p class="mb-0"><i class="bi bi-chat-left-text-fill me-2"></i><strong>배송 메모:</strong> <?=nl2br(htmlspecialchars($row_jumun['memo']))?></p>
                    <? endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0">결제 정보</h5></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"><span>상품 합계</span> <span><?=number_format($subtotal)?>원</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span>배송비</span> <span>+ <?=number_format($shipping_fee)?>원</span></li>
                        <li class="list-group-item d-flex justify-content-between fs-5 fw-bold"><span>총 결제금액</span> <span class="text-danger"><?=number_format($row_jumun['totalprice'])?>원</span></li>
                    </ul>
                    <hr>
                    <? if($row_jumun['pay_kind'] == 0): // 카드 결제 ?>
                        <p><i class="bi bi-credit-card-fill me-2"></i><strong>결제 방식:</strong> 카드 결제</p>
                    <? else: // 무통장 입금 ?>
                        <p><i class="bi bi-bank me-2"></i><strong>결제 방식:</strong> 무통장 입금</p>
                    <? endif; ?>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header"><h5 class="mb-0">주문자 정보</h5></div>
                <div class="card-body">
                    <p><i class="bi bi-person-fill me-2"></i><strong>이름:</strong> <?=$row_jumun['o_name']?></p>
                    <p class="mb-0"><i class="bi bi-telephone-fill me-2"></i><strong>연락처:</strong> <?=$row_jumun['o_tel']?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-5">
        <a href="javascript:history.back();" class="btn btn-dark">&nbsp;돌아가기&nbsp;</a>
    </div>
</div>

<?
    include "main_bottom.php";
?>