<?
    include "main_top.php";
    include "common.php";

    // --- 기존 PHP 로직은 변경 없습니다 ---
    $page_line = 20;

    $menu = $_REQUEST["menu"] ? $_REQUEST["menu"] : 1;
    $sort = $_REQUEST["sort"] ? $_REQUEST["sort"] : 1;
    
    // 정렬 방식에 따른 SQL 조건 분기
    $sql_sort = "";
    if ($sort==1) {
        $sql_sort="and icon_new=1 order by id desc"; // 신상품
    } elseif ($sort==2) {
        $sql_sort="and icon_hit=1 order by id desc"; // 인기상품
    } elseif ($sort==3) {
        $sql_sort="order by name"; // 이름순
    } elseif ($sort==4) {
        $sql_sort="order by (price*(100-discount)/100)"; // 낮은 가격순
    } else {
        $sql_sort="order by (price*(100-discount)/100) desc"; // 높은 가격순
    }
    
    $sql="select * from product where menu=$menu and status=1 " . $sql_sort;

    $result_query=mysqli_query($db,$sql);
    if (!$result_query) exit("에러:$sql");

    $count=mysqli_num_rows($result_query);
    $args="menu=$menu&sort=$sort";
    $result = mypagination($sql, $args, $count, $pagebar);
    if (!$result) exit("에러: $sql");

    $a_sort = array("", "신상품", "인기상품", "상품명", "낮은 가격", "높은 가격");
?>
<div class="container mt-5">
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h2 class="fw-bold"><?=$a_menu[$menu]?></h2>
            <p class="text-muted">다양한 <?=$a_menu[$menu]?> 상품을 만나보세요.</p>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="fw-bold">Total <?=$count ?></span> items
        </div>
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                정렬: <?=$a_sort[$sort]?>
            </button>
            <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                <li><a class="dropdown-item <? if($sort==1) echo'active'; ?>" href="menu.php?menu=<?=$menu?>&sort=1">신상품</a></li>
                <li><a class="dropdown-item <? if($sort==2) echo'active'; ?>" href="menu.php?menu=<?=$menu?>&sort=2">인기상품</a></li>
                <li><a class="dropdown-item <? if($sort==3) echo'active'; ?>" href="menu.php?menu=<?=$menu?>&sort=3">상품명</a></li>
                <li><a class="dropdown-item <? if($sort==4) echo'active'; ?>" href="menu.php?menu=<?=$menu?>&sort=4">낮은 가격</a></li>
                <li><a class="dropdown-item <? if($sort==5) echo'active'; ?>" href="menu.php?menu=<?=$menu?>&sort=5">높은 가격</a></li>
            </ul>
        </div>
    </div>
    <hr class="mt-0 mb-4">

    <div class="row">
        <?
            if ($count == 0) {
                echo("<div class='col-12 text-center py-5'>이 카테고리에는 아직 상품이 없습니다.</div>");
            } else {
                foreach($result as $row) {
                    $price = number_format($row['price']);
                    $sale_price = 0;
                    if ($row['icon_sale'] == 1) {
                        $sale_price = number_format(round($row['price'] * (100 - $row['discount']) / 100, -2));
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
                    ?>
                        <span class="text-muted text-decoration-line-through me-2"><?=$price; ?>원</span>
                        <strong class="fs-5 text-danger"><?=$sale_price; ?>원</strong>
                    <?
                        } else {
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

    <div class="d-flex justify-content-center mt-4">
        <?=$pagebar; ?>
    </div>
</div>

<?
    include "main_bottom.php";
?>