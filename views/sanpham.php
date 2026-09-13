<section class="products-section">
    <h2>Sản phẩm</h2>

    <div class="sort-filter">
        <span>Sắp xếp theo</span>
        <label><input type="radio" name="sort" value="name_asc" <?= isset($sort) && $sort == 'name_asc' ? 'checked' : '' ?>> Tên A-Z</label>
        <label><input type="radio" name="sort" value="name_desc" <?= isset($sort) && $sort == 'name_desc' ? 'checked' : '' ?>> Tên Z-A</label>
        <label><input type="radio" name="sort" value="price_desc" <?= isset($sort) && $sort == 'price_desc' ? 'checked' : '' ?>> Giá cao đến thấp</label>
        <label><input type="radio" name="sort" value="price_asc" <?= isset($sort) && $sort == 'price_asc' ? 'checked' : '' ?>> Giá thấp đến cao</label>
    </div>

    <div class="product-container">
        <div class="sidebar">
            <h3>Danh Mục Sản Phẩm</h3>
            <ul>
                <?php if (count($categories) > 0) : ?>
                    <?php foreach ($categories as $category) : ?>
                        <li>
                            <button class="category-btn" data-category-id="<?php echo $category['CategoryID']; ?>">
                                <?php echo htmlspecialchars($category['Name']); ?>
                            </button>
                        </li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li>Không có danh mục nào.</li>
                <?php endif; ?>
            </ul>
            <h3>Lọc Sản Phẩm</h3>
            <form id="filterForm" method="POST" action="">
                <ul>
                    <li><input type="radio" name="filter[]" value="all" checked> Tất Cả Sản Phẩm</li>
                    <li><input type="radio" name="filter[]" value="under100"> Giá Dưới 100.000đ</li>
                    <li><input type="radio" name="filter[]" value="100to200"> 100.000đ - 200.000đ</li>
                    <li><input type="radio" name="filter[]" value="200to500"> 200.000đ - 500.000đ</li>
                    <li><input type="radio" name="filter[]" value="500to1000"> 500.000đ - 1.000.000đ</li>
                    <li><input type="radio" name="filter[]" value="1000to3000"> 1.000.000đ - 3.000.000đ</li>
                    <li><input type="radio" name="filter[]" value="3000to5000"> 3.000.000đ - 5.000.000đ</li>
                    <li><input type="radio" name="filter[]" value="above5000"> Giá Trên 5.000.000đ</li>
                </ul>
            </form>
        </div>

        <div class="product-list" id="productList">
            <?php
            // Khởi tạo biến để chứa kết quả sản phẩm
            $result = [];
            // Nếu không có POST request, lấy tất cả sản phẩm
            if ($_SERVER["REQUEST_METHOD"] != "POST") {
                $result = getProducts($conn);
            }
            // Gọi hàm để hiển thị sản phẩm
            displayProducts($result);
            ?>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        // Lắng nghe sự kiện khi radio của bộ lọc hoặc sắp xếp thay đổi
        $('input[name="filter[]"], input[name="sort"]').change(function() {
            applyFilterAndSort();
        });

        // Lắng nghe sự kiện click của nút danh mục
        $('.category-btn').click(function() {
            var categoryID = $(this).data('category-id');
            applyFilterAndSort(categoryID); // Gọi hàm với categoryID
        });

        // Hàm để áp dụng bộ lọc và sắp xếp
        function applyFilterAndSort(categoryID = null) {
            var filters = [];
            var sortOption = $('input[name="sort"]:checked').val();

            if ($('input[name="filter[]"][value="all"]').is(':checked')) {
                filters = ['all'];
            } else {
                $('input[name="filter[]"]:checked').each(function() {
                    filters.push($(this).val());
                });
            }

            $.ajax({
                url: 'filter.php',
                method: 'POST',
                data: {
                    filter: filters,
                    sort: sortOption,
                    categoryID: categoryID
                },
                success: function(data) {
                    $('#productList').html(data); // Cập nhật danh sách sản phẩm
                }
            });
        }
    });
</script>