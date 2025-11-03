<?
	error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
	ini_set("display_errors", 1);
	
	mysqli_report(MYSQLI_REPORT_OFF);
	
	$db = mysqli_connect("localhost", "shop43", "1234", "shop43");
	if (!$db) exit("서버연결에러");
	
	$page_line=5;
	$page_block=5;
	
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