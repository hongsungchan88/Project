<?
    include "main_top.php";
?>
<script>
    // 유효성 검사 (form_lookup 이름에 맞게 수정됨)
    function NoMember_Check() {
        if (!form_lookup.name.value) {
            alert("이름을 입력해 주십시오.");
            form_lookup.name.focus();
            return;
        }
        if (!form_lookup.email.value) {
            alert("E-Mail을 입력해 주십시오.");
            form_lookup.email.focus();
            return;
        }
        form_lookup.submit();
    }
    
    // 엔터 키로 조회 실행
    function handleEnter(event) {
        if (event.key === "Enter") {
            NoMember_Check();
        }
    }
</script>

<div class="container" style="max-width: 450px;">
    <div class="my-5">

        <div class="text-center mb-4">
            <h2 class="fw-bold">비회원 주문조회</h2>
            <p class="text-muted">주문 시 입력하신 이름과 이메일 주소를 입력해 주세요.</p>
        </div>

        <div class="card p-4 shadow-sm">
            <form name="form_lookup" method="post" action="jumun.php">

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="name" id="floatingName" placeholder="주문자 이름" onkeydown="handleEnter(event)">
                    <label for="floatingName">이름</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="email" class="form-control" name="email" id="floatingEmail" placeholder="이메일 주소" onkeydown="handleEnter(event)">
                    <label for="floatingEmail">이메일 주소</label>
                </div>

                <div class="d-grid">
                    <button type="button" class="btn btn-dark btn-lg" onclick="NoMember_Check();">주문조회</button>
                </div>
            </form>
            
            <div class="text-center mt-4 small">
                <a href="member_login.php" class="text-muted text-decoration-none">회원이신가요? <span class="fw-bold">로그인</span></a>
            </div>
        </div>
    </div>
</div>


<?
    include "main_bottom.php";
?>