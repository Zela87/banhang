<!-- searchsp.php -->
<h2>Kết quả tìm kiếm cho: <?php echo htmlspecialchars($keyword); ?></h2>
<div class="products container">
<!-- <div class="product-list" id="productList"> -->
    <?php if (!empty($searchResults)) : ?>
        <?php foreach ($searchResults as $product) : ?>
            <form action="index.php?act=addToCart" method="post">
                <div class="product-card">
                    <!-- Hiển thị phần trăm khuyến mãi nếu có -->
                    <?php if (!empty($product['DiscountPercentage'])) : ?>
                        <div class="sale">
                            <p>-<?= htmlspecialchars($product['DiscountPercentage']) ?>%</p>
                        </div>
                    <?php endif; ?>

                    <a href="index.php?act=chitietsp&ProductID=<?= htmlspecialchars($product['ProductID']) ?>">
                        <div class="img">
                            <img src="<?= htmlspecialchars($img_path . $product['ImageURL']) ?>" alt="<?= htmlspecialchars($product['Name']) ?>">
                        </div>
                        <h3><?= htmlspecialchars($product['Name']) ?></h3>
                        <div class="price">
                            <!-- Hiển thị giá trước và sau giảm giá nếu có khuyến mãi -->
                            <?php if (!empty($product['DiscountPercentage']) || !empty($product['DiscountAmount'])) : ?>
                                <p class="original_price"><?= number_format($product['Price'], 0, ',', '.') ?>đ</p>
                                <p class="current_price"><?= number_format($product['CurrentPrice'], 0, ',', '.') ?>đ</p>
                            <?php else : ?>
                                <!-- Nếu không có giảm giá thì chỉ hiển thị giá gốc -->
                                <p class="current_price"><?= number_format($product['Price'], 0, ',', '.') ?>đ</p>
                            <?php endif; ?>
                        </div>
                    </a>
                    <input type="hidden" name="ProductID" value="<?= $product['ProductID'] ?>">
                    <input type="hidden" name="Name" value="<?= htmlspecialchars($product['Name']) ?>">
                    <input type="hidden" name="Quantity" value="1">

                    <input type="submit" value="Thêm vào giỏ" name="addToCart">
                </div>
            </form>
        <?php endforeach; ?>
    <?php else : ?>
        <p>Không có sản phẩm nào phù hợp với từ khóa tìm kiếm của bạn.</p>
    <?php endif; ?>
</div>
<!-- </div> -->
