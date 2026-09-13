<section id="banner">
    <div class="container">
        <div class="banner">
            <h1>Green Planet <br>Your Destination for Plants</h1>
            <p>Khám phá vẻ đẹp của thiên nhiên bằng cách khám phá nhiều loại thực vật của chúng tôi.
                Lựa chọn cây cảnh, cây ăn quả và hoa của chúng tôi sẽ mang lại bầu không khí trong lành
                cho ngôi nhà hoặc khu vườn của bạn. Mua sắm ngay bây giờ và nâng cao thế giới của bạn với
                vẻ đẹp của thiên nhiên!</p>
            <a href="index.php?act=sanpham"><input type="button" class="explore" value="KHÁM PHÁ"></a>
        </div>
        <div class="banner_img">
            <img src="../assets/images/cay.png" alt="" srcset="">
        </div>
    </div>
</section>
<section id="favor">
    <div class="container">
        <h2>Sản phẩm bạn có thể thích</h2>
    </div>
    <div class="products container">
        <?php foreach ($products_home as $product) : ?>
            <form action="index.php?act=addToCart" method="post">
                <div class="product-card">

                    <?php if (!empty($product['DiscountPercentage'])) : ?>
                        <div class="sale">
                            <p>-<?= $product['DiscountPercentage'] ?>%</p>
                        </div>
                    <?php endif; ?>

                    <a href="index.php?act=chitietsp&ProductID=<?= $product['ProductID'] ?>">
                        <div class="img">
                            <img src="<?= $img_path . $product['ImageURL'] ?>" alt="<?= htmlspecialchars($product['Name']) ?>">
                        </div>
                    </a>

                    <a href="index.php?act=chitietsp&ProductID=<?= $product['ProductID'] ?>">
                        <p class="name_product"><?= htmlspecialchars($product['Name']) ?></p>
                    </a>
                    <?php if ($product['StockQuantity'] > 0) : ?>
                        <div class="price">
                            <?php if (!empty($product['DiscountPercentage']) || !empty($product['DiscountAmount'])) : ?>
                                <p class="original_price"><?= number_format($product['Price'], 0, ',', '.') ?>đ</p>
                                <p class="current_price"><?= number_format($product['CurrentPrice'], 0, ',', '.') ?>đ</p>
                            <?php else : ?>
                                <p class="current_price"><?= number_format($product['Price'], 0, ',', '.') ?>đ</p>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="ProductID" value="<?= $product['ProductID'] ?>">
                        <input type="hidden" name="Name" value="<?= htmlspecialchars($product['Name']) ?>">
                        <input type="hidden" name="Quantity" value="1">

                        <input type="submit" value="Thêm vào giỏ" name="addToCart">
                    <?php else : ?>
                        <p class="out-of-stock">Hết hàng</p>
                    <?php endif; ?>
                </div>
            </form>
        <?php endforeach; ?>

    </div>

</section>

<section id="category">
    <div class="head container">
        <h2>Danh mục sản phẩm mới nhất</h2>
    </div>
    <div class="products container">
        <?php foreach ($new_products as $new_product) : ?>
            <form action="index.php?act=addToCart" method="post">
                <div class="product-card">
                    <!-- Hiển thị giảm giá nếu có -->
                    <?php if (!empty($new_product['DiscountPercentage'])) : ?>
                        <div class="sale">
                            <p>-<?= $new_product['DiscountPercentage'] ?>%</p>
                        </div>
                    <?php endif; ?>

                    <!-- Liên kết đến trang chi tiết sản phẩm qua ảnh -->
                    <a href="index.php?act=chitietsp&ProductID=<?= $new_product['ProductID'] ?>">
                        <div class="img">
                            <img src="<?= $img_path . $new_product['ImageURL'] ?>" alt="<?= htmlspecialchars($new_product['Name']) ?>">
                        </div>
                    </a>

                    <!-- Liên kết đến trang chi tiết sản phẩm qua tên -->
                    <a href="index.php?act=chitietsp&ProductID=<?= $new_product['ProductID'] ?>">
                        <p class="name_product"><?= htmlspecialchars($new_product['Name']) ?></p>
                    </a>
                    <?php if ($new_product['StockQuantity'] > 0) : ?>
                        <div class="price">
                            <?php if (!empty($new_product['DiscountPercentage']) || !empty($new_product['DiscountAmount'])) : ?>
                                <p class="original_price"><?= number_format($new_product['Price'], 0, ',', '.') ?>đ</p>
                                <p class="current_price"><?= number_format($new_product['CurrentPrice'], 0, ',', '.') ?>đ</p>
                            <?php else : ?>
                                <p class="current_price"><?= number_format($new_product['Price'], 0, ',', '.') ?>đ</p>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="ProductID" value="<?= $new_product['ProductID'] ?>">
                        <input type="hidden" name="Name" value="<?= htmlspecialchars($new_product['Name']) ?>">
                        <input type="hidden" name="Quantity" value="1">

                        <input type="submit" value="Thêm vào giỏ" name="addToCart">
                    <?php else : ?>
                        <p class="out-of-stock">Hết hàng</p>
                    <?php endif; ?>
                </div>
            </form>
        <?php endforeach; ?>
    </div>
</section>
<section class="promotions">
    <h2>Khuyến mại hiện tại</h2>
    <div class="container_km">
        <?php if (!empty($promotions)) : ?>
            <?php foreach ($promotions as $promo) : ?>
                <div class="promotion-item">
                    <div class="promo-left">
                        <h3 class="promo-type"><?= htmlspecialchars($promo['Title']); ?></h3>
                    </div>
                    <div class="promo-right">
                        <!-- Hiển thị giảm giá tối đa hoặc giảm phần trăm -->
                        <?php if (!empty($promo['DiscountPercentage'])) : ?>
                            <p><strong>Giảm <?= htmlspecialchars($promo['DiscountPercentage']); ?>%</strong></p>
                        <?php elseif (!empty($promo['DiscountAmount'])) : ?>
                            <p><strong>Giảm tối đa <?= number_format($promo['DiscountAmount'], 0, ',', '.'); ?>đ</strong></p>
                        <?php endif; ?>

                        <!-- Hiển thị giá trị đơn hàng tối thiểu -->
                        <p>Đơn tối thiểu <?= number_format($promo['MinOrderValue'], 0, ',', '.'); ?>đ</p>

                        <!-- Hiển thị thời gian khuyến mại -->
                        <p class="date-range">Có hiệu lực từ <?= htmlspecialchars($promo['StartDate']); ?> - <?= htmlspecialchars($promo['EndDate']); ?></p>

                        <!-- Hiển thị giới hạn số lần sử dụng nếu có -->
                        <?php if (!empty($promo['UsageLimit'])) : ?>
                            <p>Giới hạn sử dụng: <?= htmlspecialchars($promo['UsageLimit']); ?> lần</p>
                        <?php endif; ?>

                        <!-- Hiển thị mã khuyến mại và nút Lưu nếu có mã khuyến mại -->
                        <?php if (!empty($promo['PromoCode'])) : ?>
                            <p class="promo-code" id="promo-code-<?= htmlspecialchars($promo['PromoCode']); ?>">
                                Mã: <?= htmlspecialchars($promo['PromoCode']); ?>
                            </p>
                            <button class="save-btn" onclick="copyToClipboard('promo-code-<?= htmlspecialchars($promo['PromoCode']); ?>')">Sao chép mã</button>
                        <?php else : ?>
                            <button class="save-btn" disabled>Không có mã</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>Hiện không có khuyến mại nào.</p>
        <?php endif; ?>
    </div>
</section>

<section id="review">
    <div class="container">
        <div class="rev">
            <div class="reviews" id="reviewsContainer">
                <?php foreach ($feedback_home as $index => $review) : ?>
                    <div class="review <?php echo ($index == 0) ? 'active' : ''; ?>">
                        <div class="img_review">
                            <img src="<?php echo $img_path . $review['product_image']; ?>" alt="<?php echo htmlspecialchars($review['product_name']); ?>">
                        </div>
                        <div class="review_cus">
                            <?php if (!empty($review['Avatar'])) : ?>
                                <img src="../assets/images/<?php echo htmlspecialchars($review['Avatar']); ?>" alt="Profile Picture">
                            <?php else : ?>
                                <img src="../assets/images/user.jpg" alt="Profile Picture">
                            <?php endif; ?>
                            <p><?php echo htmlspecialchars($review['user_name']); ?></p>
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                    <?php if ($i <= $review['ProductRating']) : ?>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                    <?php elseif ($i - 0.5 == $review['ProductRating']) : ?>
                                        <i class="fa fa-star-half-o" aria-hidden="true"></i>
                                    <?php else : ?>
                                        <i class="fa fa-star-o" aria-hidden="true"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <p class="rev_cus"><?php echo htmlspecialchars($review['comment']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="dots">
                <?php for ($i = 0; $i < count($feedback_home); $i++) : ?>
                    <div class="dot <?php echo ($i == 0) ? 'active' : ''; ?>"></div> <!-- Đảm bảo nút đầu tiên có lớp active -->
                <?php endfor; ?>
            </div>
        </div>
    </div>
</section>



<section id="commit">
    <div class="commitions container">
        <div class="commition">
            <div><img src="../assets/images/tu_van.png" alt="" srcset=""></div>
            <h2>Tư vấn</h2>
            <p class="name_commition">Chúng tôi sẽ giúp bạn tìm được loại cây phù hợp cho ngôi nhà của bạn</p>
        </div>
        <div class="commition">
            <div><img src="../assets/images/trong.png" alt="" srcset=""></div>
            <h2>Trồng cây</h2>
            <p class="name_commition">Chúng tôi sẽ hướng dẫn bạn cách trồng cây đúng cách</p>
        </div>
        <div class="commition">
            <div><img src="../assets/images/cay_1.png" alt="" srcset=""></div>
            <h2>Tận hưởng</h2>
            <p class="name_commition">Tận hưởng sự trang trí và sự tích cực của cây mới của bạn</p>
        </div>
        <div class="commition">
            <div><img src="../assets/images/bao_tri.png" alt="" srcset=""></div>
            <h2>Bảo trì</h2>
            <p class="name_commition">Chúng tôi cũng sẽ giúp bạn giữ cho cây khỏe mạnh</p>
        </div>
    </div>
</section>