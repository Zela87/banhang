// review
let reviewIndex = 0;
let autoSlide;

// Show the current review and set up the next timeout
function showReviews() {
  let i;
  let reviews = document.getElementsByClassName("review");
  let dots = document.getElementsByClassName("dot");

  // Hide all reviews
  for (i = 0; i < reviews.length; i++) {
    reviews[i].style.display = "none";
  }

  // Increment review index
  reviewIndex++;

  // If the index exceeds the number of reviews, reset it to the first one
  if (reviewIndex > reviews.length) {
    reviewIndex = 1;
  }

  // Remove "active" class from all dots
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }

  // Display the current review and add the "active" class to the corresponding dot
  reviews[reviewIndex - 1].style.display = "flex";
  dots[reviewIndex - 1].className += " active";

  // Reset the timeout for auto sliding
  autoSlide = setTimeout(showReviews, 3000);
}

// Add click events to the dots
document.querySelectorAll(".dot").forEach((dot, idx) => {
  dot.addEventListener("click", () => {
    reviewIndex = idx; // Set reviewIndex to the clicked dot's index
    clearTimeout(autoSlide); // Clear current auto sliding timeout
    showReviews(); // Show the review corresponding to the clicked dot
  });
});

// Initialize the first review immediately
showReviews(); // Show the first review right after the page loads

// Giỏ hàng
document.querySelectorAll(".quantity-input").forEach((input) => {
  input.addEventListener("change", function () {
    const productID = this.dataset.productId;
    const newQuantity = this.value;

    fetch("index.php?act=updateCart", {
      method: "POST",
      body: new URLSearchParams({
        ProductID: productID,
        Quantity: newQuantity,
        updateCart: true,
      }),
    }).then((response) => {
      if (response.ok) {
        location.reload(); // Làm mới trang để cập nhật giá tiền
      }
    });
  });
});

//Search
document.getElementById('search-input').addEventListener('input', function () {
  var keyword = this.value;

  if (keyword.length > 2) {  // Chỉ tìm kiếm nếu từ khóa dài hơn 2 ký tự
      var xhr = new XMLHttpRequest();
      xhr.open('GET', 'index.php?act=search&search=' + keyword, true);
      xhr.onreadystatechange = function () {
          if (xhr.readyState == 4 && xhr.status == 200) {
              var response = xhr.responseText;

              // Kiểm tra nếu trả về 1 sản phẩm duy nhất và điều hướng thẳng
              if (response.startsWith("REDIRECT:")) {
                  var productUrl = response.replace("REDIRECT:", "").trim();
                  window.location.href = productUrl;
              } else {
                  // Hiển thị kết quả tìm kiếm
                  document.getElementById('searchsp').innerHTML = response;
              }
          }
      };
      xhr.send();
  }
});

// Copy mã
function copyToClipboard(elementId) {
  var promoCodeElement = document.getElementById(elementId);
  var promoCodeText = promoCodeElement.innerText.replace("Mã: ", "");

  var tempInput = document.createElement('input');
  tempInput.style.position = 'absolute';
  tempInput.style.left = '-9999px';
  tempInput.value = promoCodeText;
  document.body.appendChild(tempInput);
  tempInput.select();
  document.execCommand('copy');
  document.body.removeChild(tempInput);

  alert("Đã sao chép mã khuyến mại: " + promoCodeText);
}

//sp
document.querySelectorAll('input[name="sort"]').forEach((input) => {
  input.addEventListener("change", function () {
    const url = new URL(window.location.href);
    url.searchParams.set("sort", this.value);
    window.location.href = url.toString();
  });
});

// Tự động submit form khi thay đổi lọc giá trên trang sản phẩm
document.getElementById('filterForm').addEventListener('change', function() {
  this.submit();
});

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
// Hàm để cập nhật giá trị của các ô input ẩn trong các form
function updateQuantityForForms() {
  var quantity = document.getElementById('quantity').value;
  document.getElementById('addQuantity').value = quantity;
  document.getElementById('buyNowQuantity').value = quantity;
}

// Giảm số lượng
function decreaseQuantity() {
  var quantityInput = document.getElementById('quantity');
  var currentQuantity = parseInt(quantityInput.value);
  
  if (currentQuantity > 1) {
    quantityInput.value = currentQuantity - 1;
    updateQuantityForForms(); // Cập nhật giá trị trong các form
  } else {
    alert("Số lượng không thể nhỏ hơn 1.");
  }
}

// Tăng số lượng
function increaseQuantity() {
  var quantityInput = document.getElementById('quantity');
  var currentQuantity = parseInt(quantityInput.value);
  var maxQuantity = parseInt(quantityInput.getAttribute('max')); // Lấy giá trị tối đa từ thuộc tính max

  if (currentQuantity < maxQuantity) {
    quantityInput.value = currentQuantity + 1;
    updateQuantityForForms(); // Cập nhật giá trị trong các form
  } else {
    alert("Sản phẩm này chỉ còn " + maxQuantity + " sản phẩm trong kho.");
  }
}

// Đồng bộ số lượng khi người dùng thay đổi trực tiếp trong ô nhập số lượng
document.getElementById('quantity').addEventListener('input', function() {
  var maxQuantity = parseInt(this.getAttribute('max'));
  var currentQuantity = parseInt(this.value);

  // Kiểm tra nếu số lượng vượt quá giới hạn tồn kho
  if (currentQuantity > maxQuantity) {
    alert("Sản phẩm này chỉ còn " + maxQuantity + " sản phẩm trong kho.");
    this.value = maxQuantity; // Điều chỉnh số lượng về mức tối đa
  }

  // Kiểm tra nếu số lượng nhỏ hơn 1
  if (currentQuantity < 1) {
    alert("Số lượng không thể nhỏ hơn 1.");
    this.value = 1; // Điều chỉnh số lượng về tối thiểu là 1
  }

  updateQuantityForForms(); // Cập nhật giá trị trong các form
});


function changeMainImage(imageURL) {
  document.getElementById('mainImage').src = imageURL;
}


// ảnh đại diện
function previewImage(event) {
  var reader = new FileReader();
  reader.onload = function () {
    var output = document.getElementById("profileImage");
    output.src = reader.result; // Đặt ảnh mới vào thẻ <img>
  };
  reader.readAsDataURL(event.target.files[0]); // Đọc file ảnh đã chọn
}



$(document).ready(function() {
    $('#applyPromo').on('click', function() {
        const promoCode = $('#promoCode').val();
        $.ajax({
            url: 'index.php?act=applyPromo',
            method: 'POST',
            data: { promoCode: promoCode },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#error').text(''); // Xóa thông báo lỗi nếu có
                    alert('Mã khuyến mại đã được áp dụng thành công');
                    $('.discount').text(response.discountText);
                    $('.shipping-fee').text(response.shippingFeeText);
                    $('.grand-total').text(response.finalPriceText);
                } else {
                    $('#error').text(response.message);
                }
            },
            error: function() {
                $('#error').text('Có lỗi xảy ra khi áp dụng mã khuyến mại.');
            }
        });
    });

    $('.submit-btn').on('click', function() {
        const data = {
            hoten: $('#hoten').val(),
            diachi: $('#diachi').val(),
            email: $('#email').val(),
            sdt: $('#sdt').val(),
            payment: $('#payment').val(),
            promoCode: $('#promoCode').val() // Gửi mã khuyến mại nếu có
        };

        $.ajax({
            url: 'index.php?act=placeOrder',
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (data.payment === 'cod') {
                        window.location.href = 'index.php?act=orderSuccess';
                    } else {
                        window.location.href = 'index.php?act=paymentGateway';
                    }
                } else {
                    alert('Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.');
                }
            },
            error: function() {
                alert('Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.');
            }
        });
    });
});

document.getElementById("reviewForm").addEventListener("submit", function(e) {
        e.preventDefault(); // Ngăn form gửi theo cách thông thường

        // Lấy dữ liệu từ form
        const formData = new FormData(this);

        // Gửi yêu cầu AJAX
        fetch("index.php?act=submit_review", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data === "success") {
                alert("Đánh giá của bạn đã được gửi thành công.");
                // Xóa các giá trị nhập trong form
                document.getElementById("reviewForm").reset();
            } else {
                alert("Không thể gửi đánh giá. Vui lòng thử lại.");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Có lỗi xảy ra. Vui lòng thử lại.");
        });
    });