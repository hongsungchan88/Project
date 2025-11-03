<?php
	include "../common.php";

	$day1_default = date("Y-m-d", strtotime("-1 month"));
    $day2_default = date("Y-m-d");

    $day1 = (!empty($_REQUEST["day1"])) ? $_REQUEST["day1"] : $day1_default;
    $day2 = (!empty($_REQUEST["day2"])) ? $_REQUEST["day2"] : $day2_default;

	$sel1 = $_REQUEST["sel1"] ?? "0";
    $sel2 = $_REQUEST["sel2"] ?? "1";
    $text1 = $_REQUEST["text1"] ?? "";

	$base_sql = "SELECT * FROM jumun";
    $conditions = [];

	$conditions[] = "jumunday >= '$day1'";
    $conditions[] = "jumunday <= '$day2'";

	if ($sel1 !== "0" && !empty($sel1)) {
		$conditions[] = "state = " . intval($sel1);
	}

	if (!empty($text1)) {
        $search_term_placeholder = "%" . $text1 . "%";

        switch ($sel2) {
            case "1":
                $conditions[] = "CAST(id AS CHAR) LIKE '$search_term_placeholder'";
                break;
            case "2":
                $conditions[] = "o_name LIKE '$search_term_placeholder'";
                break;
            case "3":
                $conditions[] = "product_names LIKE '$search_term_placeholder'";
                break;
        }
    }

	$sql = $base_sql;
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

	$sql .= " ORDER BY id DESC";

	$args = "day1=" . urlencode($day1) . "&day2=" . urlencode($day2) .
            "&sel1=" . urlencode($sel1) . "&sel2=" . urlencode($sel2) .
            "&text1=" . urlencode($text1);

	$result = mypagination($sql, $args, $count, $pagebar);

	$count = mysqli_num_rows($result);
?>
<!doctype html>
<html lang="kr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>INDUK Mall</title>
	<link  href="../css/bootstrap.min.css" rel="stylesheet">
	<link  href="../css/my.css" rel="stylesheet">
	<script src="../js/jquery-3.7.1.min.js"></script>
	<script src="../js/bootstrap.bundle.min.js"></script>
	<script src="../js/my.js"></script>
</head>
<body>

<div class="container">
<!-------------------------------------------------------------------------------------------->	
<script> document.write(admin_menu());</script>
<!-------------------------------------------------------------------------------------------->	

<script>
	function go_update(id, anchorElement) {

		var frm = document.form1;
		var selectedState = anchorElement.previousElementSibling.value;
		var currentPage = frm.elements['page'] ? frm.elements['page'].value : '1';
		var url = "jumun_update.php?id=" + id +
				"&state=" + selectedState +
				"&page=" + currentPage +
				"&sel1=" + frm.elements['sel1'].value +
				"&sel2=" + frm.elements['sel2'].value +
				"&text1=" + encodeURIComponent(frm.elements['text1'].value) +
				"&day1=" + frm.elements['day1'].value +
				"&day2=" + frm.elements['day2'].value;
		location.href = url;
	}
</script>

<div class="row mx-1 justify-content-center">
	<div class="col" align="center">

		<h4 class="m-0 mb-3">주문</h4>

		<form name="form1" method="post" action="jumun.php">
		
		<table class="table table-sm table-borderless m-0 p-0">
			<tr>
				<td width="20%" align="left" style="padding-top:8px">
					주문수 : <font color="red"><?= $count ?></font>
				</td>
				<td align="right">
				
					기간:
					<div class="d-inline-flex">
						<input type="date" name="day1" value="<?= htmlspecialchars($day1) ?>" 
							class="form-control form-control-sm"  style="width:120px" >~
						<input type="date" name="day2" value="<?= htmlspecialchars($day2) ?>" 
							class="form-control form-control-sm" style="width:120px" >
					</div>
					<div class="d-inline-flex">
						<select name="sel1" class="form-select form-select-sm bg-light myfs12" style="width:100px">				
							<option value="0" <?= ($sel1 == "0") ? "selected" : "" ?>>전체</option>
							<option value="1" <?= ($sel1 == "1") ? "selected" : "" ?>>주문신청</option>
							<option value="2" <?= ($sel1 == "2") ? "selected" : "" ?>>주문확인</option>
							<option value="3" <?= ($sel1 == "3") ? "selected" : "" ?>>입금확인</option>
							<option value="4" <?= ($sel1 == "4") ? "selected" : "" ?>>배달중</option>
							<option value="5" <?= ($sel1 == "5") ? "selected" : "" ?>>주문완료</option>
							<option value="6" <?= ($sel1 == "6") ? "selected" : "" ?>>주문취소</option>
						</select>&nbsp;
						<select name="sel2" class="form-select bg-light myfs12" style="width:105px">
							<option value="1" <?= ($sel2 == "1") ? "selected" : "" ?>>주문번호</option>
							<option value="2" <?= ($sel2 == "2") ? "selected" : "" ?>>고객명</option>
							<option value="3" <?= ($sel2 == "3") ? "selected" : "" ?>>상품명</option>
						</select>
					</div>
					<div class="d-inline-flex">
						<div class="input-group input-group-sm">
							<input type="text" name="text1" value="<?= htmlspecialchars($text1) ?>" 
								class="form-control myfs12" style="width:100px" 
								onKeydown="if (event.keyCode == 13) { form1.submit(); }"> 
							<button class="btn mycolor1 myfs12" type="button" 
								onClick="form1.submit();">검색</button>
						</div>
					</div>
					
				</td>
			</tr>
		</table>
		
		<table class="table table-sm table-bordered table-hover my-1">
			<tr class="bg-light">
				<td>주문번호</td>
				<td>주문일</td>
				<td width="30%">제품명</td>
				<td width="5%">제품수</td>
				<td>금액</td>
				<td>주문자</td>
				<td width="5%">결제</td>
				<td width="20%">주문상태</td>
				<td width="5%">삭제</td>
			</tr>
			<?
				foreach($result as $row) {
			?>
			<tr>
				<td class="mywordwrap">
					<a href="jumun_info.php?id=<?= $row["id"] ?>" style="color:#0085dd"><?= $row["id"] ?></a>
				</td>
				<td><?= $row["jumunday"] ?></td>
				<td align="left" class="mywordwrap"><?= $row["product_names"] ?></td>
				<td><?= $row["product_nums"] ?></td>
				<td align="right" class="mywordwrap"><?= number_format($row["totalprice"]) ?></td>
				<td><?= $row["o_name"] ?></td>
				<?
					if($row["pay_kind"] == 0) {
						echo("<td>카드</td>");
					}else{
						echo("<td>무통장</td>");
					}
				?>
				<td>
					<div class="d-sm-inline-flex">
						<?php
							$state_val = $row["state"];
							$color = "black";
							if ($state_val == 5) $color = "blue";
							if ($state_val == 6) $color = "red";
							echo("<select name='state' class='form-select form-select-sm myfs12 me-1' style='color:$color'>");
							$options = [
								1 => "주문신청", 2 => "주문확인", 3 => "입금확인",
								4 => "배송중", 5 => "주문완료", 6 => "주문취소",
							];
							foreach ($options as $value => $text) {
								$selected = ($state_val == $value) ? "selected" : "";
								echo("<option value='$value' $selected>$text</option>");
							}
							echo("</select>");
						?>
						<a href="#" onclick="go_update('<?= $row['id'] ?>', this); return false;"
   							class="btn btn-sm mybutton-blue" style="width:50px;">수정</a>
					</div>
				</td>
				<td>
					<a href="jumun_delete.php?id=<?= $row["id"]; ?>"
						class="btn btn-sm mybutton-red" 
						onclick="javascript:return confirm('삭제할까요 ?');">삭제</a>				
				</td>
			</tr>
			<?
				}
			?>
		</table>

		<input type="hidden" name="state">
		
		</form>

		<?
			echo $pagebar;
		?>
	</div>
</div>
<!-------------------------------------------------------------------------------------------->	
</div>

</body>
</html>

