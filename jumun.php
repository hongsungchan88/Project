<?
    include "main_top.php";
    include "common.php";

    // --- 기존 PHP 로직은 변경 없습니다 ---
    $cookie_id = $_COOKIE["cookie_id"] ?? null;
    $name_from_request = $_REQUEST["name"] ?? null;
    $email_from_request = $_REQUEST["email"] ?? null;

    $name_escaped = null; $email_escaped = null;

    if ($name_from_request !== null) $name_escaped = mysqli_real_escape_string($db, $name_from_request);
    if ($email_from_request !== null) $email_escaped = mysqli_real_escape_string($db, $email_from_request);
    
    $sql = "";
    if ($cookie_id) {
        $sql = "SELECT * FROM jumun WHERE member_id = " . intval($cookie_id) . " ORDER BY id DESC";
    } elseif ($name_escaped && $email_escaped) {
        $sql = "SELECT * FROM jumun WHERE o_name = '$name_escaped' AND o_email = '$email_escaped' ORDER BY id DESC";
    }
    
    // SQL 쿼리가 없으면 로그인 페이지로 리디렉션
    if (empty($sql)) {
        // 비회원 조회 시도 시 정보가 부족한 경우
        if ($name_from_request !== null || $email_from_request !== null) {
            echo "<script>alert('이름과 이메일을 모두 입력해주세요.'); location.href='jumun_login.php';</script>";
            exit;
        }
        header("Location: jumun_login.php");
        exit;
    }
    
    $current_args_array = [];
    if ($name_from_request !== null) $current_args_array['name'] = $name_from_request;
    if ($email_from_request !== null) $current_args_array['email'] = $email_from_request;
    
    $args_for_pagination = http_build_query($current_args_array);
    $result = mypagination($sql, $args_for_pagination, $count, $pagebar);
?>
<div class="container my-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold">주문 조회</h2>
    </div>

    <? if ($count == 0) : ?>
        <div class="text-center py-5 card shadow-sm">
            <i class="bi bi-box-seam" style="font-size: 4rem;"></i>
            <h4 class="mt-3">주문 내역이 없습니다.</h4>
            <p class="text-muted">지금 바로 쇼핑을 시작해보세요.</p>
            <div class="mt-4">
                <a href="main.php" class="btn btn-dark">쇼핑하러 가기</a>
            </div>
        </div>
    <? else: ?>
        <?php
            // 주문 상태에 따른 텍스트와 뱃지 색상을 미리 정의
            $status_map = [
                1 => ['text' => "주문신청", 'color' => "primary"],
                2 => ['text' => "주문확인", 'color' => "info"],
                3 => ['text' => "입금확인", 'color' => "warning"],
                4 => ['text' => "배송중",   'color' => "success"],
                5 => ['text' => "주문완료", 'color' => "dark"],
                6 => ['text' => "주문취소", 'color' => "danger"],
                7 => ['text' => "반품", 'color' => "secondary"],
            ];
            
            foreach($result as $row) {
                $status_info = $status_map[$row['state']] ?? ['text' => '알수없음', 'color' => 'light'];
        ?>
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center">
                <div class="small">
                    <span class="me-3"><strong>주문일:</strong> <?=$row["jumunday"]?></span>
                    <span><strong>주문번호:</strong> <?=$row["id"]?></span>
                </div>
                <div>
                    <span class="badge bg-<?=$status_info['color']?> fs-6"><?=$status_info['text']?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title">주문 상품</h6>
                        <p class="card-text text-muted"><?=$row["product_names"]?></p>
                    </div>
                    <div class="text-end ms-4" style="min-width: 150px;">
						<div class="text-muted small">결제금액</div>
						<h5 class="fw-bold"><?=number_format($row["totalprice"])?>원</h5>
						<a href="jumun_info.php?id=<?=$row["id"]?>" class="btn btn-sm btn-outline-dark mt-2">상세보기</a>
					</div>
                </div>
            </div>
        </div>
        <?
            }
        ?>
        <div class="d-flex justify-content-center mt-4">
            <?=$pagebar; ?>
        </div>
    <? endif; ?>
</div>

<?
    include "main_bottom.php";
?>