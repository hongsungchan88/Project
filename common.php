<?
	error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
	ini_set("display_errors", 1);
	
	mysqli_report(MYSQLI_REPORT_OFF);
	
	$db = mysqli_connect("localhost", "shop43", "1234", "shop43");
	if (!$db) exit("서버연결에러");

	$admin_id="admin";
	$admin_pw="1234";
	
	$page_line=5;
	$page_block=5;

	$a_menu = array("분류선택", "스니커즈", "구두", "샌들", "스포츠", "캐주얼", "부츠", "기타");
	$n_menu = count($a_menu);
	
	$a_status=array("상품상태", "판매중", "판매중지", "품절");
	$n_status=count($a_status);
	$a_icon=array("아이콘 선택", "New", "Hit", "Sale");
	$n_icon=count($a_icon);
	$a_text1=array("", "제품이름", "제품번호");
	$n_text1=count($a_text1);

	$baesongbi=2500;
	$max_baesongbi=100000;

	$a_state=array("전체","주문신청","주문확인","입금확인","배송중","주문완료","주문취소");
	$n_state=count($a_state);

	// echo("<select name='$sel1'>");
	// for($i=0;$i<$n_status;$i++)
	// {
	// 	$tmp = ($i==$sel1) ? "selected" : "";
	// 	echo("<option value='$i' $tmp>$a_status[$i]</option>");
	// }
	// echo("<select>");
	
	function mypagination( $query, $args, &$count, &$pagebar ) {
		
		global $db, $page_line, $page_block;

		$page=$_REQUEST["page"] ? $_REQUEST["page"] : 1;
	
		$url=basename($_SERVER['PHP_SELF']) . "?" . $args;
		
		$sql = strtolower($query);
		$sql = "select count(*) " . substr($sql, strpos($sql, "from"));
		$result = mysqli_query($db, $sql);
		if (!$result) exit("에러: $sql");
		$row = mysqli_fetch_array($result);
		$count = $row[0];
		
		$first = ($page-1) * $page_line;
		
		$sql = str_replace(";","",$query);
		$sql .= " limit $first, $page_line";
		$result = mysqli_query($db, $sql);
		if (!$result) exit("에러: $sql");
		
		$pages = ceil($count/$page_line);
		$blocks = ceil($pages/$page_block);
		$block = ceil($page/$page_block);
		$page_s = $page_block * ($block-1);
		$page_e = $page_block * $block;
		if ($blocks <= $block) $page_e = $pages;
		
		$pagebar = "<nav>
			<ul class='pagination pagination-sm justify-content-center py-1'>";
			
		if ($block > 1)
			$pagebar .= "<li class='page-item'>
				<a class='page-link' href='$url&page=$page_s'>◀</a>
			</li>";
			
		for($i=$page_s+1; $i<=$page_e; $i++) {
			if ($page == $i)
				$pagebar .= "<li class='page-item active'>
						<span class='page-link mycolor1'>$i</span>
					</li>";
			else
				$pagebar .= "<li class='page-item'>
						<a class='page-link' href='$url&page=$i'>$i</a>
					</li>";
		}
		
		if ($block < $blocks)
			$pagebar .= "<li class='page-item'>
					<a class='page-link' href='$url&page=" .$page_e+1 . "'>▶</a>
				</li>";

		$pagebar .="</ul>
			</nav>";
			
		return $result;
	}
?>