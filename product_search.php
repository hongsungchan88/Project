<!---------------------------------------------------------------------------------------------
	제목 : 내 손으로 만드는 PHP 쇼핑몰무 (실습용 디자인 HTML)

	소속 : 인덕대학교 컴퓨터소프트웨어학과
	이름 : 교수 윤형태 (2024.02)
---------------------------------------------------------------------------------------------->
<?
	include "main_top.php";
	include "common.php";

	$findtext = $_REQUEST["find_text"];
	$sql="select * from product where name like '%$findtext%' order by name";

	$result = mypagination($sql, $args, $count, $pagebar);
	if (!$result) exit("에러: $sql");
?>
<!-------------------------------------------------------------------------------------------->	
<!-- 시작 : 다른 웹페이지 삽입할 부분 -->
<!-------------------------------------------------------------------------------------------->	
<div class="row m-1 mt-4 mb-0">
	<div class="col" align="center">

		<h4 class="m-3">상품검색</h4>

		<hr class="m-0">
		<table class="table table-sm mb-4">
			<tr height="40" class="bg-light">
				<td width="15%">이미지</td>
				<td width="45%">상품정보</td>
				<td width="20%">판매가</td>
				<td width="20%">금액</td>
			</tr>
			<?
				foreach($result as $row) {
			?>
			<tr height="85" style="font-size:14px;">
				<td>
					<?
						$imagename2=$row["image2"] ? $row["image2"] : "nopic.png";
					?>
					<a href="product.php?id=<?=$row["id"]?>"><img src="product/<?=$imagename2 ?>" width="60" height="70"></a>
				</td>
				<td align="left" valign="middle">
					<a href="product.php?id=<?=$row["id"]?>" style="color:#0066CC"><?=$row["name"] ?></a><br>
					<?
						$discount = $row['discount'];
						if($row['icon_new'] == 1) { echo "<img src='images/i_new.gif'>"; }
						if($row['icon_hit'] == 1) { echo "<img src='images/i_hit.gif'>"; }
						if($row['icon_sale'] == 1) {
							echo "<img src='images/i_sale.gif'>";
							echo "<font color='red' size='2'> $discount%</font>";
						}
					?>
				</td>
				<?
					$price = $row["price"];
					$discount = $row["discount"];

					$sal_price = number_format($discount ? round($price * (100 - $discount) / 100, -3) : $price);
					$price = number_format($price);
					if ($row["discount"]) {
						echo "<td><strike>$price</strike>원</td>";
						echo "<td><b>$sal_price<a>원</b></td>";
					} else {
						echo "<td>$price<a>원</td>";
						echo "<td><b>$price<a>원</b></td>";
					}
				?>
			</tr>
			<?
				}
			?>
		</table>
	</div>
</div>

<!--  Pagination -->
<?
	echo $pagebar;
?>

<br><br><br>

<!-------------------------------------------------------------------------------------------->	
<!-- 끝 : 다른 웹페이지 삽입할 부분 -->
<!-------------------------------------------------------------------------------------------->	
<?
    include "main_bottom.php";
?>