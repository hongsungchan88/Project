<?
    include "main_top.php";
    include "common.php";

    // --- 기존 PHP 로지은 변경 없음 ---
    $page_line = 15; // 한 페이지에 표시할 상품 수
    $page_size = 4; // 한 화면에 표시할 상품 수 (예: 4개씩)

    // SQL 쿼리 (신규 상품을 랜덤하게 가져옴)
    $sql="select * from product where icon_new=1 and status=1 order by rand()";

    $result = mypagination($sql, $args, $count, $pagebar);
    if (!$result) exit("에러: $sql");
?>
<div class="container mt-5 mb-4">
    <div class="row">
        <div class="col-12 text-center">
            <h2 class="fw-bold">신규 상품</h2>
            <p class="text-muted">새롭게 등록된 상품들을 만나보세요.</p>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <?
            if ($count == 0) {
                echo("<div class='col-12 text-center py-5'>신규 상품이 없습니다.</div>");
            } else {
                foreach($result as $row) {
                    $price = number_format($row['price']);
                    $sale_price = 0;
                    if ($row['icon_sale'] == 1) {
                        $sale_price = number_format(round($row['price'] * (100 - $row['discount']) / 100, -2)); // 10원 단위 반올림
                    }
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100 border-0 shadow-sm product-card">
                
                <div class="position-relative">
                    <a href="product.php?id=<?=$row['id']; ?>">
                        <img src="product/<?=$row['image1']; ?>" class="card-img-top">
                    </a>
                    <div class="position-absolute top-0 start-0 p-2">
                        <?
                            if($row['icon_new'] == 1) { echo "<span class='badge bg-primary me-1'>NEW</span>"; }
                            if($row['icon_hit'] == 1) { echo "<span class='badge bg-success me-1'>HIT</span>"; }
                            if($row['icon_sale'] == 1) { echo "<span class='badge bg-danger'>SALE</span>"; }
                        ?>
                    </div>
                </div>

                <div class="card-body text-center d-flex flex-column">
                    <h5 class="card-title flex-grow-1">
                        <a href="product.php?id=<?=$row['id']; ?>" class="text-decoration-none text-dark stretched-link">
                            <?=$row['name']; ?>
                        </a>
                    </h5>
                    
                    <div class="mt-2">
                    <?
                        if ($row['icon_sale'] == 1) {
                            // 세일 상품일 경우
                    ?>
                        <span class="text-muted text-decoration-line-through me-2"><?=$price; ?>원</span>
                        <strong class="fs-5 text-danger"><?=$sale_price; ?>원</strong>
                    <?
                        } else {
                            // 일반 상품일 경우
                    ?>
                        <strong class="fs-5 text-dark"><?=$price; ?>원</strong>
                    <?
                        }
                    ?>
                    </div>
                </div>
            </div>
        </div>
        <?
                }
            }
        ?>
    </div>
</div>

<div class="row">
    <div class="col">
        <?=$pagebar; ?>
    </div>
</div>

<?
    include "main_bottom.php";
?>