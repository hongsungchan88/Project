<?php
	include "../common.php";

	$id = $_REQUEST["id"] ?? null;

	if ($id === null) {
        exit("주문 ID가 전달되지 않았습니다.");
    }
	$sql_jumun = "SELECT * FROM jumun WHERE id = ?";
    $stmt_jumun = mysqli_prepare($db, $sql_jumun);
	mysqli_stmt_bind_param($stmt_jumun, "s", $id);
    mysqli_stmt_execute($stmt_jumun);
    $result_jumun = mysqli_stmt_get_result($stmt_jumun);
    $row_jumun = mysqli_fetch_array($result_jumun);
    mysqli_stmt_close($stmt_jumun);

	if (!$row_jumun) {
        exit("해당 주문 정보를 찾을 수 없습니다. ID: " . htmlspecialchars($id));
    }

    $states_array = [
        0 => "오류/알수없음", 1 => "주문신청", 2 => "주문확인",
        3 => "입금확인", 4 => "배송중",   5 => "주문완료",
        6 => "주문취소"
    ];
    $order_state_text = $states_array[$row_jumun['state'] ?? 0] ?? "알수없음";

    $member_status_text = (!empty($row_jumun['member_id']) && $row_jumun['member_id'] > 0) ? "회원" : "비회원";

    $order_items = [];
    $sql_items = "SELECT ji.*, p.name as product_name, o1.name as opts1_name, o2.name as opts2_name
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
?>
<!doctype html>
<html lang="kr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>INDUK Mall</title>
	<link  href="../css/bootstrap.min.css" rel="stylesheet">
	<link  href="../css/my.css" rel="stylesheet">
	<script src="..js/jquery-3.7.1.min.js"></script>
	<script src="../js/bootstrap.bundle.min.js"></script>
	<script src="../js/my.js"></script>
</head>
<body>

<div class="container">
<!-------------------------------------------------------------------------------------------->	
<script> document.write(admin_menu());</script>
<!-------------------------------------------------------------------------------------------->	

<div class="row mx-1 justify-content-center">
	<div class="col-sm-10" align="center">

	<h4 class="m-0 mb-3">주문 ( </b><?= htmlspecialchars($id) ?><b> )</h4>

	<table class="table table-sm table-bordered mb-3">
		<tr>
			<td width="15%" class="bg-light">상태</td>
			<td width="35%">주문신청</td>
			<td width="15%" class="bg-light">주문일</td>
			<td width="35%"><?= htmlspecialchars($row_jumun['jumunday'] ?? '') ?></td>
		</tr>
	</table>

	<table class="table table-sm table-bordered mb-2">
		<tr>
			<td width="15%" class="bg-light"><b>주문자</b></td>
			<td width="35%"><?= htmlspecialchars($row_jumun['o_name'] ?? '') ?></td>
			<td width="15%" class="bg-light">구분</td>
			<td width="35%"><?= htmlspecialchars($member_status_text) ?></td>
		</tr>
		<tr>
			<td class="bg-light">전화</td><td><?= htmlspecialchars($row_jumun['o_tel'] ?? '') ?></td>
			<td class="bg-light">E-Mail</td><td><?= htmlspecialchars($row_jumun['o_email'] ?? '') ?></td>
		</tr>
		<tr>
			<td class="bg-light">주소</td>
			<td align="left" colspan="3">&nbsp;(<?= htmlspecialchars($row_jumun['o_zip'] ?? '') ?>) <?= htmlspecialchars($row_jumun['o_juso'] ?? '') ?></td>
		</tr>
	</table>

	<table class="table table-sm table-bordered mb-3">
		<tr>
			<td width="15%" class="bg-light"><b>수신자</b></td>
			<td width="35%"><?= htmlspecialchars($row_jumun['r_name'] ?? '') ?></td>
			<td width="15%" class="bg-light"></td>
			<td></td>
		</tr>
		<tr>
			<td class="bg-light">전화</td>
			<td><?= htmlspecialchars($row_jumun['r_tel'] ?? '') ?></td>
			<td class="bg-light">E-Mail</td>
			<td><?= htmlspecialchars($row_jumun['r_email'] ?? '') ?></td>
		</tr>
		<tr>
			<td class="bg-light">주소</td>
			<td align="left" colspan="3">&nbsp;(<?= htmlspecialchars($row_jumun['r_zip'] ?? '') ?>) <?= htmlspecialchars($row_jumun['r_juso'] ?? '') ?></td>
		</tr>
		<tr height="50">
			<td class="bg-light">메모</td>
			<td align="left" valign="top" colspan="3">&nbsp;<?= nl2br(htmlspecialchars($row_jumun['memo'] ?? '')) ?></td>
		</tr>
	</table>

	<table class="table table-sm table-bordered mb-3">
		<?
			if($row_jumun['pay_kind'] == 0) {
		?>
		<tr>
			<td width="15%" class="bg-light"><b>카드</b></td>
			<td width="35%">
				<?
					$cards = [
						1 => "국민카드", 2 => "신한카드",
						3 => "우리카드", 4 => "하나카드",
					];
				?>
				<?= htmlspecialchars($cards[$row_jumun['card_kind']] ?? '') ?>
			</td>
			<td width="15%" class="bg-light">승인</td>
			<td width="35%"><?= htmlspecialchars($row_jumun['card_okno'] ?? '') ?></td>
		</tr>
		<tr>
			<td class="bg-light">할부</td>
			<td>
				<?
					$halbus = [
						3 => "3개월",
						6 => "6개월",
						12 => "12개월",
					];
				?>
				<?= htmlspecialchars($halbus[$row_jumun['card_halbu']] ?? '일시불') ?>
			</td>
			<td class="bg-light"></td><td></td>
		</tr>
		<?
			} else {
		?>
		<tr>
			<td class="bg-light"><b>무통장</b></td>
			<td>
				<?
					$banks = [
						1 => "국민은행 : 111-11111-1111",
						2 => "신한은행 : 222-22222-2222",
					];
				?>
				<?= htmlspecialchars($banks[$row_jumun['bank_kind']] ?? '') ?>
			</td>
			<td class="bg-light">입금자</td><td><?= htmlspecialchars($row_jumun['bank_sender'] ?? '') ?></td>
		</tr>
		<?
			}
		?>
	</table>

	<table class="table table-sm table-bordered mb-3">
		<tr class="bg-light">
			<td>제품명</td>
			<td width="10%">수량</td>
			<td width="10%">단가</td>
			<td width="10%">금액</td>
			<td width="10%">할인</td>
			<td width="20%">옵션</td>
		</tr>
		<?php
			foreach ($order_items as $item) {
				$price = number_format($item['price']);
				$prices = number_format($item['prices']);
				if($item['product_id'] == 0) {
					echo("<td align='left'>택배비</td>");
					$num = $item['num'];
					echo("<td>$num</td>");
					echo("<td align='right'>$price</td>");
					echo("<td align='right'>$prices</td>");
					echo("<td></td>");
					echo("<td></td>");
				} else {
		?>
		<tr>
			<td align="left"><?= $item['product_name'] ?></td>
			<td><?= $item['num'] ?></td>
			<td align="right"><?= $price ?></td>
			<td align="right"><?= $prices ?></td>
			<td>
				<?
					if($item['discount'] > 0) {
						$discount_text = $item['discount'] . "%";
					} else {
						$discount_text = "";
					}
				?>
				<?= $discount_text ?>
			</td>
			<td>
				<?= htmlspecialchars($item['opts2_name']) ?>/<?= htmlspecialchars($item['opts1_name']) ?>
			</td>
		</tr>
		<?
				}
			}
		?>
	</table>

	<table class="table table-sm table-bordered mb-3 p-2">
		<tr>
			<td width="15%" class="bg-light">총금액</td>
			<td width="85%" align="right" style="font-size:18px"><b><?= number_format($row_jumun['totalprice'] ?? 0) ?> 원</b>&nbsp;</td>
		</tr>
	</table>

	<a href="javascript:print();"  class="btn btn-sm btn-dark text-white my-2">&nbsp;프린트&nbsp;</a>&nbsp;
	<a href="javascript:history.back();"  class="btn btn-sm btn-outline-dark my-2">&nbsp;돌아가기&nbsp;</a>

	</div>
</div>
<!-------------------------------------------------------------------------------------------->	
</div>

</body>
</html>

