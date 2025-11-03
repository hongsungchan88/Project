<?
    include "main_top.php";
?>
<script>
    function Check_Value() {
        if (!form_login.uid.value) {
            alert("아이디를 입력하세요.");
            form_login.uid.focus();
            return;
        }
        if (!form_login.pwd.value) {
            alert("암호를 입력하세요.");
            form_login.pwd.focus();
            return;
        }
        form_login.submit();
    }

    // 엔터 키로 로그인 실행
    function handleEnter(event) {
        if (event.key === "Enter") {
            Check_Value();
        }
    }
</script>

<div class="container" style="max-width: 450px;">
    <div class="my-5">

        <div class="text-center mb-4">
            <h2 class="fw-bold">로그인</h2>
            <p class="text-muted">아이디와 비밀번호를 입력해 주세요.</p>
        </div>

        <div class="card p-4 shadow-sm">
            <form name="form_login" method="post" action="member_check.php">

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="uid" id="floatingUid" placeholder="아이디" onkeydown="handleEnter(event)">
                    <label for="floatingUid">아이디</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="password" class="form-control" name="pwd" id="floatingPwd" placeholder="비밀번호" onkeydown="handleEnter(event)">
                    <label for="floatingPwd">비밀번호</label>
                </div>

                <div class="d-grid">
                    <button type="button" class="btn btn-dark btn-lg" onclick="Check_Value();">로그인</button>
                </div>
            </form>
            
            <div class="d-flex justify-content-between mt-4 small">
                <a href="member_idpw.html" class="text-muted text-decoration-none">아이디/비밀번호 찾기</a>
                <a href="member_join.php" class="text-muted text-decoration-none">아직 회원이 아니신가요? <span class="fw-bold">회원가입</span></a>
            </div>
        </div>
    </div>
</div>

<?
    include "main_bottom.php";
?>