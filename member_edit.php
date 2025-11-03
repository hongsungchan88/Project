<?
    include "main_top.php";
    include "common.php";

    // --- 기존 PHP 로직은 변경 없습니다 ---
    $cookie_id = $_COOKIE["cookie_id"];
    $sql = "select * from member where uid='$cookie_id'";
    $result = mysqli_query($db, $sql);
    if (!$result) exit("에러: $sql");
    $row = mysqli_fetch_array($result);
?>
<script>
    function FindZip(zip_kind) {
        window.open("zipcode.php?zip_kind="+zip_kind, "", "scrollbars=no,width=490,height=320");
    }

    // 유효성 검사 (form2 이름에 맞게 수정됨)
    function Check_Value() {
        if (form2.pwd.value && (form2.pwd.value != form2.pwd1.value)) {
            alert("새 비밀번호가 일치하지 않습니다.");
            form2.pwd.focus(); return;
        }
        if (!form2.name.value) {
            alert("이름을 입력해주세요."); form2.name.focus(); return;
        }
        if (!form2.tel1.value || !form2.tel2.value || !form2.tel3.value) {
            alert("휴대폰 번호를 올바르게 입력해주세요."); form2.tel1.focus(); return;
        }
        if (!form2.zip.value || !form2.juso.value) {
            alert("주소를 입력해주세요."); form2.zip.focus(); return;
        }
        if (!form2.email.value) {
            alert("이메일을 입력해주세요."); form2.email.focus(); return;
        }
        form2.submit();
    }
</script>

<div class="container" style="max-width: 768px;">
    <div class="my-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold">회원 정보 수정</h2>
            <p class="text-muted">개인 정보를 최신 상태로 유지해주세요.</p>
        </div>
        
        <div class="card p-4 shadow-sm">
            <form name="form2" method="post" action="member_update.php">
                <div class="mb-3">
                    <label for="uid" class="form-label">아이디</label>
                    <input type="text" class="form-control" name="uid" id="uid" value="<?=$row['uid']?>" readonly disabled>
                </div>

                <div class="mb-3">
                    <label for="pwd" class="form-label">새 비밀번호</label>
                    <input type="password" class="form-control" name="pwd" id="pwd" placeholder="새 비밀번호">
                    <div class="form-text">비밀번호를 변경할 경우에만 입력해주세요.</div>
                </div>

                <div class="mb-4">
                    <label for="pwd1" class="form-label">새 비밀번호 확인</label>
                    <input type="password" class="form-control" name="pwd1" id="pwd1" placeholder="새 비밀번호 확인">
                </div>
                
                <hr class="mb-4">

                <div class="mb-3">
                    <label for="name" class="form-label">이름 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="name" value="<?=$row['name']?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">휴대폰 <span class="text-danger">*</span></label>
                    <?
                        $tel = $row["tel"];
                        $tel1 = substr($tel, 0, 3);
                        $tel2 = substr($tel, 3, 4);
                        $tel3 = substr($tel, 7, 4);
                    ?>
                    <div class="row gx-2 align-items-center">
                        <div class="col"><input type="text" name="tel1" class="form-control text-center" value="<?=$tel1?>" maxlength="3"></div>
                        <div class="col-auto">-</div>
                        <div class="col"><input type="text" name="tel2" class="form-control text-center" value="<?=$tel2?>" maxlength="4"></div>
                        <div class="col-auto">-</div>
                        <div class="col"><input type="text" name="tel3" class="form-control text-center" value="<?=$tel3?>" maxlength="4"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">주소 <span class="text-danger">*</span></label>
                    <div class="input-group mb-2">
                        <input type="text" name="zip" class="form-control" value="<?=$row['zip']?>" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="FindZip(0);">우편번호 찾기</button>
                    </div>
                    <input type="text" name="juso" class="form-control" value="<?=$row['juso']?>">
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">이메일 <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" id="email" value="<?=$row['email']?>">
                </div>

                <div class="mb-4">
                    <label class="form-label">생년월일</label>
                     <?
                        $birthday1 = substr($row["birthday"], 0, 4);
                        $birthday2 = substr($row["birthday"], 5, 2);
                        $birthday3 = substr($row["birthday"], 8, 2);
                    ?>
                    <div class="row gx-2 align-items-center">
                        <div class="col"><input type="text" name="birthday1" class="form-control text-center" value="<?=$birthday1?>" maxlength="4" placeholder="년(4자)"></div>
                        <div class="col-auto">년</div>
                        <div class="col"><input type="text" name="birthday2" class="form-control text-center" value="<?=$birthday2?>" maxlength="2" placeholder="월"></div>
                        <div class="col-auto">월</div>
                        <div class="col"><input type="text" name="birthday3" class="form-control text-center" value="<?=$birthday3?>" maxlength="2" placeholder="일"></div>
                        <div class="col-auto">일</div>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="button" class="btn btn-dark btn-lg" onclick="Check_Value();">회원정보 수정</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?
    include "main_bottom.php";
?>