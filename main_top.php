<!doctype html>
<html lang="kr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>INDUK Mall</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="css/my.css" rel="stylesheet">
    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container">

<header class="border-bottom mb-4">
    <div class="d-flex justify-content-end align-items-center py-2" style="font-size: 12px;">
        <ul class="nav">
            <?
                if (isset($_COOKIE["cookie_id"])) {
                    echo("<li class='nav-item'><a href='member_logout.php' class='nav-link link-secondary px-2'>로그아웃</a></li>");
                    echo("<li class='nav-item'><a href='member_edit.php' class='nav-link link-secondary px-2'>정보수정</a></li>");
                } else {
                    echo("<li class='nav-item'><a href='member_login.php' class='nav-link link-secondary px-2'><i class='bi bi-box-arrow-in-right'></i> 로그인</a></li>");
                    echo("<li class='nav-item'><a href='member_join.php' class='nav-link link-secondary px-2'><i class='bi bi-person-plus'></i> 회원가입</a></li>");
                }
            ?>
            <li class="nav-item"><a href="cart.php" class="nav-link link-secondary px-2"><i class="bi bi-cart"></i> 장바구니</a></li>
            <?
                if (isset($_COOKIE["cookie_id"])) {
                    echo("<li class='nav-item'><a href='jumun.php' class='nav-link link-secondary px-2'><i class='bi bi-receipt'></i> 주문조회</a></li>");
                } else {
                    echo("<li class='nav-item'><a href='jumun_login.php' class='nav-link link-secondary px-2'><i class='bi bi-receipt'></i> 주문조회</a></li>");
                }
            ?>
            <li class="nav-item"><a href="qa.html" class="nav-link link-secondary px-2">Q&A</a></li>
            <li class="nav-item"><a href="faq.html" class="nav-link link-secondary px-2">FAQ</a></li>
        </ul>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid px-0">
            <a class="navbar-brand fs-2 fw-bold text-dark" href="index.html">INDUK Mall</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item zoom_a"><a class="nav-link" href="menu.php?menu=1">스니커즈</a></li>
                    <li class="nav-item zoom_a"><a class="nav-link" href="menu.php?menu=2">구두</a></li>
                    <li class="nav-item zoom_a"><a class="nav-link" href="menu.php?menu=3">샌들</a></li>
                    <li class="nav-item zoom_a"><a class="nav-link" href="menu.php?menu=4">스포츠</a></li>
                    <li class="nav-item zoom_a"><a class="nav-link" href="menu.php?menu=5">캐주얼</a></li>
                    <li class="nav-item zoom_a"><a class="nav-link" href="menu.php?menu=6">부츠</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            기타
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">SMenu1</a></li>
                            <li><a class="dropdown-item" href="#">SMenu2</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">SMenu3</a></li>
                        </ul>
                    </li>
                </ul>

                <script>
                    function check_findproduct() {
                        if (!document.form_search.find_text.value) {
                            alert('검색어를 입력하세요.');
                            document.form_search.find_text.focus();
                            return;
                        }
                        document.form_search.submit();
                    }
                </script>
                <form name="form_search" method="post" action="product_search.php" class="d-flex">
                    <input class="form-control form-control-sm me-2" type="search" name="find_text" placeholder="상품 검색" aria-label="Search">
                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="check_findproduct()"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>
    </nav>
</header>


<div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel"  data-bs-interval="4000">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" aria-label="Slide 1" class="active" aria-current="true" ></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3" aria-label="Slide 4"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="images/main1.png" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
            <img src="images/main2.png" class="d-block w-100"alt="...">
        </div>
        <div class="carousel-item">
            <img src="images/main3.png" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
            <img src="images/main4.png" class="d-block w-100" alt="...">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>