<?php
$sessionLifetime = 300;

// Cấu hình thời gian sống của session (cookie)
session_set_cookie_params($sessionLifetime);

// Cấu hình thời gian sống của session dữ liệu trên server
ini_set('session.gc_maxlifetime', $sessionLifetime);

session_start();

// Cập nhật thời gian hết hạn của cookie mỗi khi người dùng có tương tác
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $sessionLifetime) {
    session_unset();
    session_destroy();
    header('location: index.php');
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Cập nhật thời gian hoạt động cuối cùng
?>
<?php
include '../models/db-connect.php';
include '../models/product.php';
include '../models/category.php';
include '../models/user.php';
include '../partials/global.php';
include '../models/giohang.php';
include '../models/feedback.php';
include '../models/promotion.php';
include '../models/order.php';
include '../models_admin/insertchitiet.php';


// Khởi tạo kết nối cơ sở dữ liệu nếu chưa có
$dbconn = connect_db();

$products_home = get_products_with_discounts();
$new_products = get_newest_products_with_discounts();
$products_sp = get_all_products();
$feedback_home = get_all_feedback_home();
$products_sale = get_products_sale();
$promotions = get_all_promotions();
$grandTotal = 0;
$cart_count = 0;
if (isset($_SESSION['user'])) {
    // Lấy ID người dùng từ session
    $id_user = $_SESSION['user']['id_user'];

    // Lấy các sản phẩm từ giỏ hàng của người dùng
    $cart_items = getCartItems($id_user);
    // Lấy tổng số lượng sản phẩm trong giỏ hàng
    $cart_count = get_cart_count($id_user);
    $order_history = get_order_history($id_user);
}


include '../partials/header.php';

if (isset($_GET['act']) && ($_GET['act'] != '')) {
    $act = $_GET['act'];
    switch ($act) {
        case 'trangchu':
            include 'trangchu.php';
            break;

        case 'gioithieu':
            include 'gioithieu.php';
            break;

        case 'dangnhap':
            dangnhap();
            include 'dangnhap.php';
            break;

        case 'dangky':
            dangky();
            include 'dangky.php';
            break;

        case 'dangxuat':
            dangxuat();
            break;
        case 'lienhe':
            include 'lienhe.php';
            break;

        case 'khuyenmai':
            include 'khuyenmai.php';
            break;
        case 'updateCart':
            handleUpdateCart($id_user);
            break;

        case 'addToCart':
            if (isset($_POST['ProductID']) && isset($_POST['Quantity'])) {
                $ProductID = (int)$_POST['ProductID'];
                $Quantity = (int)$_POST['Quantity'];

                add_to_cart($id_user, $ProductID, $Quantity);
            }
            header('Location: index.php?act=giohang');
            break;

        case 'removeFromCart':
            if (isset($_POST['ProductID'])) {
                $ProductID = (int)$_POST['ProductID'];
                remove_from_cart($id_user, $ProductID);
            }
            header('Location: index.php?act=giohang');
            break;

        case 'giohang':
            if (!isset($_SESSION['user'])) {
                echo "<h2>Bạn cần đăng nhập để xem giỏ hàng.</h2>";
                break;
            }
            unset($_SESSION['buy_now']);
            
            $cart_items = getCartItems($id_user);
            $cart_count = get_cart_count($id_user);

            include 'giohang.php';
            break;
        case 'dathang':
            if (!isset($_SESSION['user'])) {
                echo "<h2>Bạn cần đăng nhập để được đặt hàng.</h2>";
                break;
            }

            include 'dathang.php';
            break;

        case 'thanhcong':

            unset($_SESSION['freeShipping']);
            unset($_SESSION['discount']);
            unset($_SESSION['giftItem']);
            unset($_SESSION['promoCode']);
            unset($_SESSION['promotionId']);

            // clearCart($userId);
            include 'thanhcong.php';
            break;
        case 'thanhtoan':
            include 'thanhtoan.php';
            break;
        case 'confirm_bank_payment':
            $orderInfo = $_SESSION['order_info'] ?? null;

            if (!$orderInfo) {
                echo "Không có thông tin đơn hàng. Vui lòng quay lại giỏ hàng và thử lại.";
                exit;
            }

            $orderId = $orderInfo['OrderID'];
            $userId = $orderInfo['UserID'];
            $shippingAddress = $orderInfo['shippingAddress'];
            $shipping_fee = $orderInfo['shipping_fee'];
            $grandTotal = $orderInfo['grandTotal'];
            $discount = $orderInfo['discount'];
            $finalPrice = $orderInfo['finalPrice'];
            $paymentMethod = $orderInfo['paymentMethod'];
            $cart_items = $orderInfo['cart_items'];
            $promoCode = $orderInfo['promoCode'];
            $promotionId = $orderInfo['promotionId'];
            $giftItem = $orderInfo['giftItem'] ?? null;

            // Cập nhật trạng thái đơn hàng thành "Chờ xác nhận chuyển khoản"
            updateOrderStatus($orderId, 'Chờ xác nhận chuyển khoản');

            // Tiếp tục xử lý các sản phẩm và khuyến mãi như trước
            foreach ($cart_items as $item) {
                $productId = $item['ProductID'];
                $quantity = $item['Quantity'];
                $totalPrice = $item['CurrentPrice'] * $quantity;
                addOrderItem($orderId, $productId, $quantity, $totalPrice);
                updateProductStock($productId, $quantity);
            }

            if ($giftItem) {
                $giftProductId = $giftItem['ProductID'];
                addOrderItem($orderId, $giftProductId, 1, 0); // Giá của quà tặng là 0
                updateProductStock($giftProductId, 1);
            }

            if (!empty($promoCode)) {
                applyPromotionUsageWithOrderId($promoCode, $userId, $orderId);
                decrementPromotionUsage($promotionId);
            }

            clearCart($userId);

            header('Location: index.php?act=thanhcong');
            break;

        case 'danhgia':
            include 'danhgia.php';
            break;
            case 'submit_review':
                $productID = $_POST['productID'];
                $orderID = $_POST['orderID'];
                $rating = (int)$_POST['rating'];
                $comment = $_POST['comment'];
                $userID = $_SESSION['user']['id_user'];
            
                $result = addProductReview($productID, $userID, $rating, $comment);
            
                if ($result) {
                    $_SESSION['review_message'] = "Đánh giá của bạn đã được gửi thành công.";                    
                    header("Location: index.php?act=chitietsp&ProductID=$productID");
                    exit;
                } else {
                    $_SESSION['review_message'] = "Không thể gửi đánh giá. Vui lòng thử lại.";                    
                    header("Location: index.php?act=chitietsp&ProductID=$productID");
                    exit;
                }
                break;
            

        case 'hoso':
            if (isset($_SESSION['user'])) {
                // Lấy thông tin từ session 'user'
                $hoten = $_SESSION['user']['hoten'];
                $email = $_SESSION['user']['email'];
                $sdt = $_SESSION['user']['sdt'];
                $diachi = $_SESSION['user']['diachi'];
                $gioitinh = $_SESSION['user']['gioitinh'];
                $ngaysinh = $_SESSION['user']['ngaysinh'];
                $avatar = $_SESSION['user']['avatar'];
            } else {
                // Nếu người dùng chưa đăng nhập, điều hướng đến trang đăng nhập
                header('Location: index.php?act=dangnhap');
                exit();
            }

            include 'hoso.php'; // Bao gồm trang hồ sơ người dùng
            break;

        case 'suahoso':
            // Kiểm tra nếu người dùng đã đăng nhập
            if (isset($_SESSION['user'])) {
                // Lấy thông tin hiện tại từ session để hiển thị trên form
                $hoten = $_SESSION['user']['hoten'] ?? '';
                $email = $_SESSION['user']['email'] ?? '';
                $sdt = $_SESSION['user']['sdt'] ?? '';
                $diachi = $_SESSION['user']['diachi'] ?? '';
                $gioitinh = $_SESSION['user']['gioitinh'] ?? '';
                $ngaysinh = $_SESSION['user']['ngaysinh'] ?? '';
                $avatar = $_SESSION['user']['avatar'] ?? '';
            } else {
                // Nếu người dùng chưa đăng nhập, điều hướng về trang đăng nhập
                header('Location: index.php?act=dangnhap');
                exit();
            }

            // Xử lý khi form cập nhật hồ sơ được gửi
            if (isset($_POST['capnhat'])) {
                $new_hoten = $_POST['hoten'] ?? '';
                $new_email = $_POST['email'] ?? '';
                $new_sdt = $_POST['sdt'] ?? '';
                $new_diachi = $_POST['diachi'] ?? '';
                $new_gioitinh = $_POST['gender'] ?? '';
                $new_ngaysinh = $_POST['birthday'] ?? '';
                $new_avatar = '';

                // Kiểm tra nếu có file ảnh được upload
                if (isset($_FILES['Avatar']) && $_FILES['Avatar']['error'] === UPLOAD_ERR_OK) {
                    $avatar_file = $_FILES['Avatar']['name'];
                    $avatar_temp = $_FILES['Avatar']['tmp_name'];
                    $upload_dir = "../assets/images/";
                    move_uploaded_file($avatar_temp, $upload_dir . $avatar_file);
                    $new_avatar = $avatar_file;
                }

                // Lấy ID người dùng từ session
                $id_user = $_SESSION['user']['id_user'];

                // Cập nhật thông tin người dùng trong cơ sở dữ liệu
                $result = update_user($id_user, $new_hoten, $new_email, $new_sdt, $new_diachi, $new_gioitinh, $new_ngaysinh, $new_avatar);

                if ($result === true) {
                    // Cập nhật thông tin trong session
                    $_SESSION['user']['hoten'] = $new_hoten;
                    $_SESSION['user']['email'] = $new_email;
                    $_SESSION['user']['sdt'] = $new_sdt;
                    $_SESSION['user']['diachi'] = $new_diachi;
                    $_SESSION['user']['gioitinh'] = $new_gioitinh;
                    $_SESSION['user']['ngaysinh'] = $new_ngaysinh;
                    if ($new_avatar) {
                        $_SESSION['user']['avatar'] = $new_avatar;
                    }

                    // Điều hướng về trang hồ sơ sau khi cập nhật thành công
                    include 'hoso.php';
                    break;
                } else {
                    echo "Đã xảy ra lỗi khi cập nhật hồ sơ!";
                }
            }

            // Bao gồm file view để hiển thị form cập nhật hồ sơ
            include 'suahoso.php';
            break;

        case 'baomat':
            // Kiểm tra nếu người dùng đã đăng nhập
            if (isset($_SESSION['user'])) {
                $id_user = $_SESSION['user']['id_user'];
            } else {
                // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
                header('Location: index.php?act=dangnhap');
                exit();
            }

            // Xử lý khi form đổi mật khẩu được gửi
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $current_password = $_POST['current_password'] ?? '';
                $new_password = $_POST['new_password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';

                // Kiểm tra xem mật khẩu mới và xác nhận mật khẩu có trùng khớp không
                if ($new_password !== $confirm_password) {
                    $error = "Mật khẩu mới và nhập lại mật khẩu không khớp.";
                } elseif (strlen($new_password) < 8) {
                    $error = "Mật khẩu mới phải có ít nhất 8 ký tự.";
                } else {
                    // Gọi hàm đổi mật khẩu
                    $result = update_password($id_user, $current_password, $new_password);

                    if ($result === true) {
                        $success = "Mật khẩu của bạn đã được cập nhật thành công!";
                    } else {
                        $error = $result;  // Thông báo lỗi trả về từ hàm update_password
                    }
                }
            }

            // Bao gồm file view để hiển thị form đổi mật khẩu
            include 'baomat.php';
            break;


        case 'sanpham':
            include 'filter.php';
            $priceFilter = [];
            $sort = 'name_asc';

            // Xử lý yêu cầu POST để lấy dữ liệu bộ lọc và sắp xếp
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (!empty($_POST['filter'])) {
                    $priceFilter = $_POST['filter'];
                }

                if (!empty($_POST['sort'])) {
                    $sort = $_POST['sort'];
                }
            }

            // Lấy sản phẩm đã lọc và sắp xếp
            $products = getFilteredProducts($priceFilter, $sort);

            // Bao gồm trang sản phẩm
            include 'sanpham.php';
            break;

        case 'chitietsp':

            if (isset($_GET['ProductID'])) {
                $productID = (int)$_GET['ProductID'];
                $feedbacks = get_all_feedback_by_product_id($productID) ?? [];
                $product = getProductDetails($productID);

                if (!$product) {
                    echo "Sản phẩm không tồn tại.";
                    exit;
                }


                // Lấy ID danh mục từ chi tiết sản phẩm nếu nó tồn tại.
                $categoryID = $product['CategoryID'];
                $result_info = getProductInfo($dbconn, $productID) ?? [];
                // $result_feedback = getProductFeedback($productID) ?? [];
                $message = handleFeedbackSubmission($dbconn, $productID);
                $images = getProductImages($dbconn, $productID) ?? [];
                $relatedProducts = getRelatedProducts($dbconn, $productID, $product['CategoryID']) ?? [];

                disconnect_db($dbconn);
            }

            include 'chitietsp.php';
            break;

        case 'lsdonhang':
            include 'lsdonhang.php';
            break;

            // index.php
        case 'search':
            if (isset($_GET['search'])) {
                $keyword = $_GET['search'];  // Lấy từ khóa tìm kiếm từ input search
                $searchResults = searchProducts($keyword);  // Gọi hàm tìm kiếm sản phẩm từ product.php

                // Nếu chỉ có 1 sản phẩm trả về, điều hướng thẳng tới trang chi tiết sản phẩm
                if (count($searchResults) == 1) {
                    $product = $searchResults[0];
                    header("Location: index.php?act=chitietsp&ProductID=" . $product['ProductID']);
                    exit();
                }

                // Nếu có nhiều hơn 1 sản phẩm, hiển thị danh sách kết quả tìm kiếm
                include 'searchsp.php';  // Hiển thị kết quả tìm kiếm ra trang web
            }
            break;

        default:
            include 'trangchu.php';
            break;
    }
} else {
    include 'trangchu.php';
}

include '../partials/footer.php';
