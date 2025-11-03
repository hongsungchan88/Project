<?
    include "main_top.php";
?>
<script>
    function FindZip(zip_kind) {
        window.open("zipcode.php?zip_kind="+zip_kind, "zip", 
            "width=440,height=320,scrollbars=no");
    }

    function check_id() {
        if (!form2.uid.value) {
            alert("ID를 입력해 주세요.");
            form2.uid.focus();
            return;
        }
        window.open("member_idcheck.php?uid="+form2.uid.value,"",
            "width=300,height=200,scrollbar=no");
    }

    function Check_Value() {
        if (form2.check_id.value !== "checked") { // ID 중복 확인 여부를 정확하게 체크
            alert("ID 중복 확인을 먼저 진행해 주세요.");
            form2.uid.focus();
            return;
        }
        if (!form2.uid.value) {
            alert("아이디를 입력해 주세요."); form2.uid.focus(); return;
        }
        if (!form2.pwd.value) {
            alert("비밀번호를 입력해 주세요."); form2.pwd.focus(); return;
        }
        if (form2.pwd.value !== form2.pwd1.value) {
            alert("비밀번호가 일치하지 않습니다.");
            form2.pwd1.focus(); return;
        }
        if (!form2.name.value) {
            alert("이름을 입력해 주세요."); form2.name.focus(); return;
        }
        if (!form2.tel1.value || !form2.tel2.value || !form2.tel3.value) {
            alert("휴대폰 번호를 올바르게 입력해 주세요."); form2.tel1.focus(); return;
        }
        if (!form2.zip.value || !form2.juso.value) {
            alert("주소를 입력해 주세요."); form2.zip.focus(); return;
        }
        if (!form2.email.value) {
            alert("이메일을 입력해 주세요."); form2.email.focus(); return;
        }
        form2.submit();
    }
</script>

<div class="container" style="max-width: 768px;">
    <div class="my-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold">회원가입</h2>
            <p class="text-muted">INDUK Mall 회원이 되어 다양한 혜택을 누리세요.</p>
        </div>
        
        <div class="card p-4 shadow-sm">
            <form name="form2" method="post" action="member_insert.php">
                <input type="hidden" name="check_id" value=""> 

                <div class="mb-3">
                    <label for="uid" class="form-label">아이디 <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="uid" id="uid" placeholder="아이디를 입력하세요">
                        <button class="btn btn-outline-secondary" type="button" onclick="check_id();">ID 중복 확인</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="pwd" class="form-label">비밀번호 <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="pwd" id="pwd" placeholder="영문, 숫자, _ 사용 (6자 이상)">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pwd1" class="form-label">비밀번호 확인 <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="pwd1" id="pwd1" placeholder="비밀번호를 한번 더 입력하세요">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">이름 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="이름을 입력하세요">
                </div>

                <div class="mb-3">
                    <label for="tel1" class="form-label">휴대폰 <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="tel1" id="tel1" maxlength="3" value="010">
                        <span class="input-group-text">-</span>
                        <input type="text" class="form-control" name="tel2" maxlength="4" placeholder="1234">
                        <span class="input-group-text">-</span>
                        <input type="text" class="form-control" name="tel3" maxlength="4" placeholder="5678">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">주소 <span class="text-danger">*</span></label>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="zip" id="zip" placeholder="우편번호" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="FindZip(0);">우편번호 찾기</button>
                    </div>
                    <input type="text" class="form-control" name="juso" id="juso" placeholder="검색된 주소" readonly>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">이메일 <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="example@induk.ac.kr">
                </div>

                <div class="mb-4">
                    <label class="form-label">생년월일</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="birthday1" maxlength="4" placeholder="년(4자)">
                        <span class="input-group-text">년</span>
                        <input type="text" class="form-control" name="birthday2" maxlength="2" placeholder="월">
                        <span class="input-group-text">월</span>
                        <input type="text" class="form-control" name="birthday3" maxlength="2" placeholder="일">
                        <span class="input-group-text">일</span>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="button" class="btn btn-dark btn-lg" onclick="Check_Value();">가입하기</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?
    include "main_bottom.php";
?>