<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Trang chủ - FOODDAILY</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <link href="{{ asset('logo.jpg') }}" rel="icon">
    <link href="{{ asset('client/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Amatic+SC:wght@400;700&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">

    <link href="{{ asset('client/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('client/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('client/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('client/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('client/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <link href="{{ asset('client/assets/css/main.css') }}" rel="stylesheet">

    <style>
        .menu-item {
            cursor: pointer;
            transition: transform 0.3s;
        }

        .menu-item:hover {
            transform: scale(1.02);
        }

        /* Đồng bộ kích thước hình ảnh món ăn */
        .menu-item .menu-img {
            width: 100% !important;
            height: 250px !important;
            object-fit: cover !important;
            border-radius: 8px;
        }

        .modal-header-custom {
            background-color: #ce1126;
            color: white;
        }

        .btn-order-submit {
            background-color: #ce1126;
            color: white;
            border: none;
        }

        .btn-order-submit:hover {
            background-color: #a00d20;
            color: white;
        }
    </style>
</head>

<body class="index-page">
    <header id="header" class="header d-flex align-items-center sticky-top">
        <div class="container position-relative d-flex align-items-center justify-content-between">

            <a href="index.html" class="logo d-flex align-items-center me-auto me-xl-0">
                <img src="{{ asset('logo.jpg') }}" alt="">
                <h1 class="sitename">FOODDAILY</h1>
                <span>.</span>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="#hero" class="active">Trang chủ<br></a></li>
                    <li><a href="#about">Thông tin</a></li>
                    <li class="dropdown"><a href="#menu"><span>Thực đơn</span> <i
                                class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li><a href="#menu">Ăn sáng</a></li>
                            <li><a href="#menu">Tráng miệng</a></li>
                        </ul>
                    </li>
                    <li><a href="#events">Chương trình</a></li>
                    <li><a href="#gallery">Kho ảnh</a></li>
                    <li><a href="#contact">Liên hệ</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('giohang') }}" class="btn-cart" title="Giỏ hàng">
                    <i class="bi bi-cart-fill fs-5"></i>
                </a>
                <a class="btn-getstarted" href="index.html#book-a-table">Đăng nhập</a>
            </div>

        </div>
    </header>

    <main class="main">

        <section id="hero" class="hero section light-background">
            <div class="container">
                <div class="row gy-4 justify-content-center justify-content-lg-between">
                    <div class="col-lg-5 order-2 order-lg-1 d-flex flex-column justify-content-center">
                        <h1 data-aos="fade-up">Enjoy Your Healthy<br>Delicious Food</h1>
                        <p data-aos="fade-up" data-aos-delay="100">We are team of talented designers making websites
                            with Bootstrap</p>
                        <div class="d-flex" data-aos="fade-up" data-aos-delay="200">
                            <a href="#book-a-table" class="btn-get-started">Đăng nhập</a>
                            <a href="https://www.youtube.com/watch?v=Y7f98aduVJ8"
                                class="glightbox btn-watch-video d-flex align-items-center"><i
                                    class="bi bi-play-circle"></i><span>Watch Video</span></a>
                        </div>
                    </div>
                    <div class="col-lg-5 order-1 order-lg-2 hero-img" data-aos="zoom-out">
                        <img src="{{ asset('client/assets/img/hero-img.png') }}" class="img-fluid animated" alt="">
                    </div>
                </div>
            </div>
        </section>

        <section id="menu" class="menu section">
            <div class="container section-title" data-aos="fade-up">
                <h2>Our Menu</h2>
                <p><span>Check Our</span> <span class="description-title">Yummy Menu</span></p>
            </div>

            <div class="container">
                <ul class="nav nav-tabs d-flex justify-content-center" data-aos="fade-up" data-aos-delay="100">
                    <li class="nav-item">
                        <a class="nav-link active show" data-bs-toggle="tab" data-bs-target="#menu-starters">
                            <h4>Starters</h4>
                        </a>
                    </li>
                </ul>

                <div class="tab-content" data-aos="fade-up" data-aos-delay="200">
                    <div class="tab-pane fade active show" id="menu-starters">
                        <div class="tab-header text-center">
                            <p>Menu</p>
                            <h3>Starters</h3>
                        </div>

                        <div class="row gy-5">
                            <div class="col-lg-4 menu-item" data-bs-toggle="modal" data-bs-target="#orderModal"
                                data-product-name="Phở Bò Đặc Biệt" data-product-price="45.000 đ">
                                <a href="javascript:void(0)"><img
                                        src="{{ asset('client/assets/img/menu/menu-item-1.png') }}"
                                        class="menu-img img-fluid" alt=""></a>
                                <h4>Phở Bò Đặc Biệt</h4>
                                <p class="ingredients">Bánh phở, sườn bò, bò tái nạm, nước dùng phin truyền thống</p>
                                <p class="price">45.000 đ</p>
                            </div>

                            <div class="col-lg-4 menu-item" data-bs-toggle="modal" data-bs-target="#orderModal"
                                data-product-name="Bánh Mì Thịt Nướng" data-product-price="35.000 đ">
                                <a href="javascript:void(0)"><img
                                        src="{{ asset('client/assets/img/menu/menu-item-2.png') }}"
                                        class="menu-img img-fluid" alt=""></a>
                                <h4>Bánh Mì Thịt Nướng</h4>
                                <p class="ingredients">Bánh mì giòn, thịt xiên nướng, pa-tê, rau thơm đồ chua</p>
                                <p class="price">35.000 đ</p>
                            </div>

                            <div class="col-lg-4 menu-item" data-bs-toggle="modal" data-bs-target="#orderModal"
                                data-product-name="Cà Phê Sữa Đá" data-product-price="29.000 đ">
                                <a href="javascript:void(0)"><img
                                        src="{{ asset('client/assets/img/menu/menu-item-3.png') }}"
                                        class="menu-img img-fluid" alt=""></a>
                                <h4>Cà Phê Sữa Đá</h4>
                                <p class="ingredients">Hạt Robusta xay nguyên chất, sữa đặc đặc sánh, đá viên</p>
                                <p class="price">29.000 đ</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title font-weight-bold" id="orderModalLabel"><i
                            class="bi bi-cart-plus-fill me-2"></i>Xác nhận đặt mua sản phẩm</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-shadow="none"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="POST">
                    <div class="modal-body text-dark">
                        <div class="alert alert-secondary d-flex justify-content-between align-items-center py-2 mb-3">
                            <div>Món ăn chọn: <strong id="modal-product-name" class="text-danger">N/A</strong></div>
                            <div>Đơn giá: <strong id="modal-product-price" class="text-dark">0 đ</strong></div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4 mb-3">
                                <label for="quantity" class="form-label font-weight-bold">Số lượng đặt <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="quantity" name="quantity" value="1"
                                    min="1" required>
                            </div>
                            <div class="form-group col-md-8 mb-3">
                                <label for="delivery_time" class="form-label font-weight-bold">Thời gian nhận
                                    hàng</label>
                                <select class="form-select" id="delivery_time" name="delivery_time">
                                    <option value="now" selected>Ngay bây giờ (Giao hỏa tốc)</option>
                                    <option value="tomorrow_morning">Ngày mai - Buổi sáng (07:00 - 11:00)</option>
                                    <option value="tomorrow_afternoon">Ngày mai - Buổi chiều (13:00 - 18:00)</option>
                                    <option value="other">Khung giờ khác (Ghi chú phía dưới)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 mb-3">
                                <label for="phone" class="form-label font-weight-bold">Số điện thoại nhận hàng <span
                                        class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    placeholder="Nhập số điện thoại" required>
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label for="email" class="form-label font-weight-bold">Địa chỉ Email xác nhận <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="name@example.com" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="address" class="form-label font-weight-bold">Địa chỉ giao hàng cụ thể <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address" name="address"
                                placeholder="Số nhà, tên đường, tòa nhà, phân hiệu phường/quận..." required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold d-block">Phương thức thanh toán</label>
                            <div class="form-check form-check-inline me-4">
                                <input class="form-check-input" type="radio" name="payment_method" id="pay_cod"
                                    value="cod" checked>
                                <label class="form-check-check-label" for="pay_cod"><i
                                        class="bi bi-cash-stack text-success me-1"></i> Tiền mặt khi nhận (COD)</label>
                            </div>
                            <div class="form-check form-check-inline me-4">
                                <!-- Ví MoMo removed: only bank transfer and COD are supported -->
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment_method" id="pay_atm"
                                    value="atm">
                                <label class="form-check-check-label" for="pay_atm"><i
                                        class="bi bi-credit-card me-1"></i> Chuyển khoản ATM</label>
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <label for="order_notes" class="form-label font-weight-bold">Ghi chú món ăn / Yêu cầu
                                thêm</label>
                            <textarea class="form-control" id="order_notes" name="order_notes" rows="2"
                                placeholder="Ví dụ: Không hành, cay nhiều, giao tới cổng trường phân hiệu gọi trước..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Hủy
                            bỏ</button>
                        <button type="submit" class="btn btn-order-submit shadow-sm px-4">Xác nhận đặt hàng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer id="footer" class="footer dark-background">
        <div class="container copyright text-center mt-4">
            <p>© <span>Copyright</span> <strong class="px-1 sitename">Yummy</strong> <span>All Rights Reserved</span>
            </p>
        </div>
    </footer>

    <script src="{{ asset('client/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('client/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('client/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('client/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('client/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

    <script src="{{ asset('client/assets/js/main.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const menuItems = document.querySelectorAll(".menu-item");
            menuItems.forEach(item => {
                item.addEventListener("click", function () {
                    const name = this.getAttribute("data-product-name");
                    const price = this.getAttribute("data-product-price");

                    document.getElementById("modal-product-name").innerText = name;
                    document.getElementById("modal-product-price").innerText = price;
                });
            });
        });
    </script>
</body>

</html>
