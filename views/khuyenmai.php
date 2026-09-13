<section class="promotions">
    <h2>Khuyến mại hiện tại</h2>
    <div class="container_km">
        <?php if (!empty($promotions)): ?>
            <?php foreach ($promotions as $promo): ?>
                <div class="promotion-item">
                    <div class="promo-left">
                        <h3 class="promo-type"><?= htmlspecialchars($promo['Title']); ?></h3>
                    </div>
                    <div class="promo-right">
                        <!-- Hiển thị giảm giá tối đa hoặc giảm phần trăm -->
                        <?php if (!empty($promo['DiscountPercentage'])): ?>
                            <p><strong>Giảm <?= htmlspecialchars($promo['DiscountPercentage']); ?>%</strong></p>
                        <?php elseif (!empty($promo['DiscountAmount'])): ?>
                            <p><strong>Giảm tối đa <?= number_format($promo['DiscountAmount'], 0, ',', '.'); ?>đ</strong></p>
                        <?php endif; ?>

                        <p>Đơn tối thiểu <?= number_format($promo['MinOrderValue'], 0, ',', '.'); ?>đ</p>
                        
                        <p class="date-range">Có hiệu lực từ <?= htmlspecialchars($promo['StartDate']); ?> - <?= htmlspecialchars($promo['EndDate']); ?></p>

                        <?php if (!empty($promo['UsageLimit'])): ?>
                            <p>Giới hạn sử dụng: <?= htmlspecialchars($promo['UsageLimit']); ?> lần</p>
                        <?php endif; ?>

                        <?php if (!empty($promo['PromoCode'])): ?>
                            <p class="promo-code" id="promo-code-<?= htmlspecialchars($promo['PromoCode']); ?>">
                                Mã: <?= htmlspecialchars($promo['PromoCode']); ?>
                            </p>
                            <button class="save-btn" onclick="copyToClipboard('promo-code-<?= htmlspecialchars($promo['PromoCode']); ?>')">Sao chép mã</button>
                        <?php else: ?>
                            <button class="save-btn" disabled>Không có mã</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Hiện không có khuyến mại nào.</p>
        <?php endif; ?>
    </div>
</section>


<section class="promotions">
    <h2>Sản phẩm được giảm giá</h2>
    <div class="products container">
    <?php if (!empty($products_sale)): ?>
        <?php foreach ($products_sale as $product) : ?>
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
                </div>
            </form>
        <?php endforeach; ?>
        <?php else: ?>
            <p>Hiện không có sản phẩm nào được giảm giá.</p>
        <?php endif; ?>
    </div>
</section>