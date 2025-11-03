<?
	include "../common.php";

	$uid = $_REQUEST["uid"];

	$sql="select * from member where uid='$uid'";
	$result=mysqli_query($db, $sql);
	if (!$result) exit("에러: $sql");
	
	$row=mysqli_fetch_array($result);
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

<script>
	function FindZip(zip_kind) 
	{
		w=window.open("../zipcode.php?zip_kind="+zip_kind, "zip", "scrollbars=no,width=490,height=320");
	}
</script>

<!-- form2 시작 -->
<form name="form2" method="post" action="member_update.php">

<input type="hidden" name="uid" value="<?=$row["uid"]; ?>">

<div class="row mx-1  justify-content-center">
	<div class="col-sm-10" align="center">

		<h4 class="m-0 mb-3">회원</h4>

		<table class="table table-sm table-bordered myfs12">
			<tr height="40">
				<td width="15%" class="bg-light">아이디</td>
				<td align="left" class="ps-2"><?=$row["uid"]; ?></td>
			</tr>
			<tr>
				<td class="bg-light">암호</td>
				<td align="left" class="ps-2">
					<div class="d-inline-flex">
						<input type="text" name="pwd" size="20" value="<?=$row["pwd"]; ?>" class="form-control form-control-sm">
					</div>
				</td>
			</tr>
			<tr>
				<td class="bg-light">이름</td>
				<td align="left" class="ps-2">
					<div class="d-inline-flex">
						<input type="text" name="name" size="20" value="<?=$row["name"]; ?>" class="form-control form-control-sm">
					</div>
				</td>
			</tr>
			<tr>
				<td class="bg-light">휴대폰</td>
				<td align="left" class="ps-2">
					<div class="d-inline-flex">
						<?
							$tel = $row["tel"];
							$tel1 = substr($tel, 0, 3);
							$tel2 = substr($tel, 3, 4);
							$tel3 = substr($tel, 7, 4);
						?>
						<input type="text" name="tel1" size="3" maxlength="3" 
							value="<?=$tel1; ?>" class="form-control form-control-sm">-
						<input type="text" name="tel2" size="4" maxlength="4" 
							value="<?=$tel2; ?>" class="form-control form-control-sm">-
						<input type="text" name="tel3" size="4" maxlength="4" 
							value="<?=$tel3; ?>" class="form-control form-control-sm">
					</div>
				</td>
			</tr>
			<tr>
				<td class="bg-light">주소</td>
				<td align="left" class="ps-2">
					<div class="d-inline-flex mb-1">
						<input type="text" name="zip" size="5" maxlength="5" value="<?=$row["zip"]; ?>" class="form-control form-control-sm">&nbsp;
					</div>
					<a href="javascript:FindZip(0);" class="btn btn-sm btn-secondary text-white mb-1 myfs12">우편번호 찾기</a><br>
					<div class="d-inline-flex">
						<input type="text" name="juso" size="50" value="<?=$row["juso"]; ?>" class="form-control form-control-sm">
					</div>
				</td>
			</tr>
			<tr>
				<td class="bg-light">E-Mail</td>
				<td align="left" class="ps-2">
					<div class="d-inline-flex">
						<input type="text" name="email" size="50" value="<?=$row["email"]; ?>" class="form-control form-control-sm">
					</div>
				</td>
			</tr>
			<tr>
				<td class="bg-light">생일</td>
				<td align="left" class="ps-2">
					<div class="d-inline-flex pe-4">
						<?
							$birthday1 = substr($row["birthday"], 0, 4);
							$birthday2 = substr($row["birthday"], 5, 2);
							$birthday3 = substr($row["birthday"], 8, 2);
						?>
						<input type="text" name="birthday1" size="4" value="<?=$birthday1; ?>" 
							class="form-control form-control-sm"> -
						<input type="text" name="birthday2" size="2" value="<?=$birthday2; ?>" 
							class="form-control form-control-sm"> -
						<input type="text" name="birthday3" size="2" value="<?=$birthday3; ?>" 
							class="form-control form-control-sm">
					</div>
				</td>
			</tr>
			<tr height="40">
				<td class="bg-light">구분</td>
				<td align="left" class="ps-2 pt-2">
					<div class="d-inline-flex">
						<div class="form-check">
							<?
								if ($row["gubun"]==0)
									echo("<input class='form-check-input' type='radio' name='gubun' value='0' checked>
									<label class='form-check-label me-2'>회원</label>");
								else
									echo("<input class='form-check-input' type='radio' name='gubun' value='0'>
									<label class='form-check-label me-2'>회원</label>");
							?>
						</div>
						<div class="form-check">
							<?
								if ($row["gubun"]==1)
									echo("<input class='form-check-input' type='radio' name='gubun' value='1' checked>
									<label class='form-check-label'>탈퇴</label>");
								else
									echo("<input class='form-check-input' type='radio' name='gubun' value='1'>
									<label class='form-check-label'>탈퇴</label>");
							?>
						</div>
					</div>
				</td>
			</tr>
		</table>

		<a href="javascript:form2.submit();"  class="btn btn-sm btn-dark text-white my-2">&nbsp;저 장&nbsp;</a>&nbsp;
		<a href="javascript:history.back();"  class="btn btn-sm btn-outline-dark my-2">&nbsp;돌아가기&nbsp;</a>

	</div>
</div>

<!-------------------------------------------------------------------------------------------->	
</div>

</body>
</html>

