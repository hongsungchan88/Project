<?
    include "common.php";

    $text1 = $_REQUEST["text1"] ?? "";
    $sel = $_REQUEST["sel"] ?? 1;
    $zip_kind = $_REQUEST["zip_kind"] ?? "";

    $result = null;
    $count = 0;
    if ($text1) {
        $sql = "select * from zip$sel where juso4 like '%$text1%' or juso7 like '%$text1%'";
        $args = "text1=$text1&sel=$sel&zip_kind=$zip_kind";
        $result = mypagination($sql, $args, $count, $pagebar);
    }
?>
<!doctype html>
<html lang="kr" style="overflow-y: hidden;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>우편번호 검색</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/my.css" rel="stylesheet">
    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body class="p-3">

<script>
    // 검색 버튼 클릭 시
    function SearchZip() {
        if (!form_search.text1.value) {
            alert("검색할 도로명이나 건물명을 입력해 주십시오.");
            form_search.text1.focus();
            return;
        }
        form_search.submit();
    }
    
    // 주소 목록에서 항목 클릭 시
    function selectAddress(element) {
        let listItems = document.querySelectorAll('.list-group-item');
        listItems.forEach(item => {
            item.classList.remove('active');
        });
        element.classList.add('active');
        
        document.getElementById('selected_zip').value = element.dataset.zip;
        document.getElementById('selected_juso').value = element.dataset.juso;
    }

    // 최종 '확인' 버튼 클릭 시 (form2를 사용하도록 수정)
    function SendZip(zip_kind) {
        let zip = document.getElementById('selected_zip').value;
        let juso_base = document.getElementById('selected_juso').value;
        let juso_detail = document.getElementById('jusor_detail').value;

        if (!zip) {
            alert("주소를 먼저 선택해주세요.");
            return;
        }
        
        let final_juso = juso_base + " " + juso_detail;

        // 부모창(opener)의 'form2'라는 이름의 폼을 직접 찾아 값을 전달합니다.
        if (zip_kind == 1) { // 주문자
            opener.form2.o_zip.value = zip;
            opener.form2.o_juso.value = final_juso;
        } else if (zip_kind == 2) { // 배송지
            opener.form2.r_zip.value = zip;
            opener.form2.r_juso.value = final_juso;
        } else { // 회원가입/수정 (zip_kind == 0)
            opener.form2.zip.value = zip;
            opener.form2.juso.value = final_juso;
        }
        self.close();
    }
</script>

<form name="form_search" method="post" action="zipcode.php">
    <input type="hidden" name="zip_kind" value="<?=$zip_kind ?>">
    <h5 class="mb-3">우편번호 검색</h5>
    <div class="input-group">
        <select name="sel" class="form-select" style="flex: 0 0 100px;">
            <option value="1" <?=($sel==1?"selected":"")?>>서울</option>
            <option value="2" <?=($sel==2?"selected":"")?>>경기</option>
            </select>
        <input type="text" name="text1" value="<?=$text1?>" class="form-control" placeholder="도로명 또는 건물명 입력">
        <button class="btn btn-dark" type="button" onclick="SearchZip()">검색</button>
    </div>
</form>

<hr>

<div>
    <p class="small text-muted mb-2">
        <? if ($text1): ?>
            "<strong><?=$text1?></strong>"에 대한 검색 결과 <strong><?=$count?></strong> 건 입니다. 주소를 클릭하세요.
        <? else: ?>
            검색어를 입력하고 검색 버튼을 눌러주세요.
        <? endif; ?>
    </p>

    <ul class="list-group mb-2" style="max-height: 180px; overflow-y: auto;">
        <?
        if ($result) {
            foreach ($result as $row) {
                $zip = $row["zip"];
                $A = $row["juso1"] . " " . $row["juso2"] . " " . $row["juso3"] . " " . $row["juso4"];
                if ($row["juso5"]) $A .= $row["juso5"];
                if ($row["juso6"] != "0") $A .= "-" . $row["juso6"];
                if ($row["juso7"]) $A .= " " . $row["juso7"];
                
                // data-* 속성에 우편번호와 기본주소 저장
                echo("<li class='list-group-item list-group-item-action' style='cursor:pointer;'
                        data-zip='$zip' data-juso='$A' onclick='selectAddress(this)'>
                        [$zip] $A
                      </li>");
            }
        }
        ?>
    </ul>

    <input type="hidden" id="selected_zip">
    <input type="hidden" id="selected_juso">

    <div class="input-group">
        <span class="input-group-text">나머지 주소</span>
        <input type="text" id="jusor_detail" class="form-control" placeholder="상세 주소 (예: 아파트, 동, 호수)">
    </div>
</div>

<hr>

<div class="text-center">
    <button type="button" class="btn btn-dark" onclick="SendZip('<?=$zip_kind?>')">확 인</button>
</div>

</body>
</html>