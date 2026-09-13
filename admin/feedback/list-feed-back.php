<?php
require_once __DIR__ . '/../quan-ly-feedback.php';
require_once __DIR__ . '../../../models_admin/feedback.php';
require_once __DIR__ . '../../../partials/header-admin.php';
$pdo = connect_db();
$controller = new FeedbackController($pdo);
$rating = isset($_GET['rating']) ? (int)$_GET['rating'] : null;

try {
    if ($rating !== null && $rating >= 1 && $rating <= 5) {
        $feedbacks = $controller->fillterFeedBack($rating);
    } else {
        $feedbacks = $controller->index();
    }
} finally {
    $pdo = disconnect_db($pdo);
}
$ratings = array_column($feedbacks, 'ProductRating');
$averageRating = !empty($ratings) ? array_sum($ratings) / count($ratings) : 0;


$averageRating = round($averageRating, 1);


$fullStars = floor($averageRating);
$halfStar = ($averageRating - $fullStars) >= 0.5 ? 1 : 0;
$emptyStars = 5 - $fullStars - $halfStar;

?>

<div class="head-page">
    <h2>Phản hồi khách hàng</h2>

</div>
<main class="main-content" id="listFb">
    <div class="container">
        <div class="row">
            <div class="rating-container">
                <div class="rating-display"><?php echo htmlspecialchars(number_format($averageRating, 1)); ?></div>
                <div class="rating-stars">
                    <?php
                    for ($i = 0; $i < $fullStars; $i++) : ?>
                        <span class="star">&#9733;</span>
                    <?php endfor; ?>
                    <?php if ($halfStar) : ?>
                        <span class="star">&#9733;</span>
                    <?php endif; ?>
                    <?php
                    for ($i = 0; $i < $emptyStars; $i++) : ?>
                        <span class="star">&#9734;</span>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="filter-buttons-container">
                <div class="filter-buttons">
                    <a href="?"><button>Tất cả</button></a>
                    <a href="?rating=5"><button>5 Sao</button></a>
                    <a href="?rating=4"><button>4 Sao</button></a>
                    <a href="?rating=3"><button>3 Sao</button></a>
                    <a href="?rating=2"><button>2 Sao</button></a>
                    <a href="?rating=1"><button>1 Sao</button></a>
                </div>
            </div>
        </div>
    </div>

    <div class="list-feed-back-container">
        <?php foreach ($feedbacks as $feedback) : ?>
            <div class="items-fb-container">
                <div class="image-user">
                    <?php
                    $user_image = !empty($feedback['user_image']) ? '/greenplanet_copy/assets/images/' . $feedback['user_image'] : '/greenplanet_copy/assets/images/user.jpg';
                    ?>
                    <img src="<?php echo htmlspecialchars($user_image); ?>" alt="Hình ảnh người dùng" />
                </div>
                <div class="content-fb-container">
                    <div class="name-user-fb">
                        <div class="name-user">
                            <?php echo htmlspecialchars(!empty($feedback['hoten']) ? $feedback['hoten'] : "Chưa có họ tên khách"); ?>

                        </div>
                        <div class="dots-container">
                            <div class="dots"></div>
                            <div class="dots"></div>
                            <div class="dots"></div>
                        </div>
                    </div>
                    <div class="rating-stars">
                        <?php
                        $totalStars = 5;
                        $fullStars = (int)$feedback['ProductRating'];
                        $emptyStars = $totalStars - $fullStars;

                        for ($i = 0; $i < $fullStars; $i++) : ?>
                            <span class="star">&#9733;</span>
                        <?php endfor; ?>

                        <?php for ($i = 0; $i < $emptyStars; $i++) : ?>
                            <span class="star">&#9734;</span>
                        <?php endfor; ?>
                    </div>
                    <div class="name-product-fb">
                        <p class="type">Phân loại: <span class="name-prd"><?php echo htmlspecialchars($feedback['ProductName']); ?></span></p>
                    </div>
                    <div class="comment">
                        <?php echo htmlspecialchars($feedback['Comment'] ?? "Không có đánh giá nào"); ?>
                    </div>
                    <div class="image-fb">
                        <?php if (!empty($feedback['product_image'])) : ?>
                            <img src="<?php echo htmlspecialchars( $feedback['product_image']); ?>" alt="Hình ảnh sản phẩm" />
                        <?php else : ?>
                            <p>Chưa có ảnh sản phẩm</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>


    </div>
</main>

</div>
</div>

</body>

</html>