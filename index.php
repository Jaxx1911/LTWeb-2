<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>J&T Express - Giao hàng Chuyển phát nhanh</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Montserrat Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Slick Slider CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar bg-dark text-white py-2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="language-switcher">
                        <a href="#" class="text-white me-3">EN</a>
                        <a href="#" class="text-white active">VI</a>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="text-white me-3">Đăng ký</a>
                    <a href="#" class="text-white">Đăng nhập</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <img src="assets/images/logo.png" alt="J&T Express" height="50">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Trang chủ</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="aboutDropdown" role="button" data-bs-toggle="dropdown">
                                Giới thiệu
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Về chúng tôi</a></li>
                                <li><a class="dropdown-item" href="#">Cam kết</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="trackingDropdown" role="button" data-bs-toggle="dropdown">
                                Tra cứu
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Cước vận chuyển</a></li>
                                <li><a class="dropdown-item" href="#">Bưu cục gần đây</a></li>
                                <li><a class="dropdown-item" href="#">Vận đơn</a></li>
                                <li><a class="dropdown-item" href="#">Bảng giá</a></li>
                                <li><a class="dropdown-item" href="#">Hàng từ chối vận chuyển</a></li>
                                <li><a class="dropdown-item" href="#">Quy cách đóng gói</a></li>
                                <li><a class="dropdown-item" href="#">Bảng phân vùng tính cước</a></li>
                                <li><a class="dropdown-item" href="#">Quy trình vận chuyển</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown">
                                Dịch vụ
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Dịch vụ tươi sống</a></li>
                                <li><a class="dropdown-item" href="#">Vận chuyển quốc tế</a></li>
                                <li><a class="dropdown-item" href="#">Chuyển phát tiêu chuẩn</a></li>
                                <li><a class="dropdown-item" href="#">Dịch vụ Nhanh</a></li>
                                <li><a class="dropdown-item" href="#">Siêu dịch vụ giao hàng</a></li>
                                <li><a class="dropdown-item" href="#">Hậu cần đơn hàng</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Phát triển bền vững</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="careersDropdown" role="button" data-bs-toggle="dropdown">
                                Tuyển dụng
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Cơ hội nghề nghiệp</a></li>
                                <li><a class="dropdown-item" href="#">Cuộc sống J&T Express</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="blogDropdown" role="button" data-bs-toggle="dropdown">
                                Blog
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">J-Magazine</a></li>
                                <li><a class="dropdown-item" href="#">Tin tức nổi bật</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="consultDropdown" role="button" data-bs-toggle="dropdown">
                                Tư vấn
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Liên hệ</a></li>
                                <li><a class="dropdown-item" href="#">Đăng ký tư vấn</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Banner Slider -->
    <div id="bannerCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        <!-- Indicators/dots -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="2"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="3"></button>
        </div>

        <!-- The slideshow/carousel -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/images/banner/b1.jpg" class="d-block w-100" alt="Banner 1">
            </div>
            <div class="carousel-item">
                <img src="assets/images/banner/b2.jpg" class="d-block w-100" alt="Banner 2">
            </div>
            <div class="carousel-item">
                <img src="assets/images/banner/b3.jpg" class="d-block w-100" alt="Banner 3">
            </div>
            <div class="carousel-item">
                <img src="assets/images/banner/b4.jpg" class="d-block w-100" alt="Banner 4">
            </div>
        </div>

        <!-- Left and right controls/icons -->
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Main Content -->
    <main>
        <!-- Quick Links -->
        <section class="quick-links py-4">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-4">
                        <a href="#" class="quick-link-item d-block text-decoration-none">
                            <div class="quick-link-content">
                                <h3 class="quick-link-title">Tra cứu đơn hàng</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="#" class="quick-link-item d-block text-decoration-none">
                            <div class="quick-link-content">
                                <h3 class="quick-link-title">Tra cứu bưu cục</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="#" class="quick-link-item d-block text-decoration-none">
                            <div class="quick-link-content">
                                <h3 class="quick-link-title">Bảng giá</h3>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="tracking-form mt-4">
                    <div class="search-container">
                        <div class="search-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" class="form-control search-input" placeholder="Nhập mã vận đơn của bạn (cách nhau bởi dấu phẩy), tối đa 10 vận đơn">
                        <button class="btn btn-search">Tìm kiếm</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- International Network -->
        <section class="international-network py-5">
            <div class="container">
                <h2 class="text-center Montserrat-Bold">Mạng lưới phủ sóng các nước</h2>
                <p class="text-center mb-8 Montserrat-Medium">J&T Express tự hào đã & đang mở rộng mạng lưới quốc tế để mang đến trải nghiệm tốt nhất</p>
                <div class="wrapper-about-nations more-countries">
                    <div class="flex flex-col items-center item-a">
                        <img src="assets/images/countries/indonesia.png" class="nation-hover w-[50px] h-[50px] shadow-lg rounded-full aos-init aos-animate" data-aos="fade-up" data-aos-duration="1000" alt="Indonesia">
                        <div class="bg-[#454F5B] text-white rounded-[5px] text-[14px] Montserrat-Bold hidden lg:block px-[32px] py-[7px] mt-[10px] nation-arrow">
                            Indonesia
                        </div>
                    </div>
                    <div class="flex flex-col items-center item-b">
                        <img src="assets/images/countries/malaysia.jpg" class="nation-hover w-[50px] h-[50px] shadow-lg rounded-full aos-init aos-animate" data-aos="fade-up" data-aos-duration="1400" alt="Malaysia">
                        <div class="bg-[#454F5B] text-white rounded-[5px] text-[14px] Montserrat-Bold hidden lg:block px-[32px] py-[7px] mt-[10px] nation-arrow">
                            Malaysia
                        </div>
                    </div>
                    <div class="flex flex-col items-center item-c">
                        <img src="assets/images/countries/philippines.jpg" class="nation-hover w-[50px] h-[50px] shadow-lg rounded-full aos-init aos-animate" data-aos="fade-up" data-aos-duration="1500" alt="Philippines">
                        <div class="bg-[#454F5B] text-white rounded-[5px] text-[14px] Montserrat-Bold hidden lg:block px-[32px] py-[7px] mt-[10px] nation-arrow">
                            Philippines
                        </div>
                    </div>
                    <div class="flex flex-col items-center item-d">
                        <img src="assets/images/countries/thailand.jpg" class="nation-hover w-[50px] h-[50px] shadow-lg rounded-full aos-init aos-animate" data-aos="fade-up" data-aos-duration="1700" alt="Thailand">
                        <div class="bg-[#454F5B] text-white rounded-[5px] text-[14px] Montserrat-Bold hidden lg:block px-[32px] py-[7px] mt-[10px] nation-arrow">
                            Thái Lan
                        </div>
                    </div>
                    <div class="flex flex-col items-center item-e">
                        <img src="assets/images/countries/singapore.jpg" class="nation-hover w-[50px] h-[50px] shadow-lg rounded-full aos-init aos-animate" data-aos="fade-up" data-aos-duration="1800" alt="Singapore">
                        <div class="bg-[#454F5B] text-white rounded-[5px] text-[14px] Montserrat-Bold hidden lg:block px-[32px] py-[7px] mt-[10px] nation-arrow">
                            Singapore
                        </div>
                    </div>
                    <div class="flex flex-col items-center item-f">
                        <img src="assets/images/countries/cambodia.jpg" class="nation-hover w-[50px] h-[50px] shadow-lg rounded-full aos-init aos-animate" data-aos="fade-up" data-aos-duration="1900" alt="Cambodia">
                        <div class="bg-[#454F5B] text-white rounded-[5px] text-[14px] Montserrat-Bold hidden lg:block px-[32px] py-[7px] mt-[10px] nation-arrow">
                            Campuchia
                        </div>
                    </div>
                    <div class="flex flex-col items-center item-g">
                        <img src="assets/images/countries/mexico.png" class="nation-hover w-[50px] h-[50px] shadow-lg rounded-full aos-init aos-animate" data-aos="fade-up" data-aos-duration="2200" alt="Mexico">
                        <div class="bg-[#454F5B] text-white rounded-[5px] text-[14px] Montserrat-Bold hidden lg:block px-[32px] py-[7px] mt-[10px] nation-arrow">
                            Mexico
                        </div>
                    </div>
                    <div class="flex flex-col items-center item-h">
                        <img src="assets/images/countries/saudi.png" class="nation-hover w-[50px] h-[50px] shadow-lg rounded-full aos-init aos-animate" data-aos="fade-up" data-aos-duration="2100" alt="Saudi Arabia">
                        <div class="bg-[#454F5B] text-white rounded-[5px] text-[14px] Montserrat-Bold hidden lg:block px-[32px] py-[7px] mt-[10px] nation-arrow">
                            Saudi Arabia
                        </div>
                    </div>
                    <div class="flex flex-col items-center item-i">
                        <img src="assets/images/countries/uae.png" class="nation-hover w-[50px] h-[50px] shadow-lg rounded-full aos-init aos-animate" data-aos="fade-up" data-aos-duration="2000" alt="UAE">
                        <div class="bg-[#454F5B] text-white rounded-[5px] text-[14px] Montserrat-Bold hidden lg:block px-[32px] py-[7px] mt-[10px] nation-arrow">
                            UAE
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="bg-[#F4F4F4] overflow-hidden mb-12">
            <div class="wrapper-about-us container mx-auto">
                <div class="flex flex-wrap">
                    <!-- Left Column -->
                    <div data-aos="fade-right" data-aos-duration="500">
                        <h1 class="text-[40px] mb-5 text-[#161D25] Montserrat-Bold">
                            VỀ CHÚNG TÔI
                        </h1>
                        <p class="text-[16px] leading-[24px] text-[#161D25] Montserrat-Medium">
                        J&T Express là thương hiệu chuyển phát nhanh dựa trên sự phát triển của công nghệ và Internet. Chúng tôi sở hữu mạng lưới rộng khắp nhằm hỗ trợ các hoạt động giao nhận hàng hóa nhanh chóng không chỉ ở nội thành mà còn ở ngoại thành và các vùng xa của các tỉnh thành trong cả nước Việt Nam.
                        </p>
                    </div>

                    <!-- Right Column -->
                    <div class="px-5">
                        <div class="grid grid-cols-2 gap-x-[124px] gap-y-[64px]">
                            <div class="text-left" data-aos="fade-left" data-aos-duration="500">
                                <img src="assets/images/about/63tinh-thanh.png" alt="63 tỉnh thành" class="mx-auto mb-4">
                                <h3 class="Montserrat-Bold">63 TỈNH THÀNH</h3>
                                <p class="Montserrat-Regular">Dịch vụ phủ sóng khắp 63 tỉnh thành</p>
                            </div>
                            <div class="text-left" data-aos="fade-left" data-aos-duration="700">
                                <img src="assets/images/about/1000xe.png" alt="Đa dạng phương tiện" class="mx-auto mb-4">
                                <h3 class="Montserrat-Bold">ĐA DẠNG PHƯƠNG TIỆN</h3>
                                <p class="Montserrat-Regular">Đa dạng phương tiện vận chuyển hàng hóa</p>
                            </div>
                                <div class="text-left" data-aos="fade-left" data-aos-duration="900">
                                <img src="assets/images/about/25000nhan-vien.png" alt="Nhân sự chuyên nghiệp" class="mx-auto mb-4">
                                <h3 class="mb-2 text-[#161D25] Montserrat-Bold">NHÂN SỰ CHUYÊN NGHIỆP</h3>
                                <p class="text-[#161D25] Montserrat-Regular">Nhân sự được đào tạo bài bản & chuyên nghiệp</p>
                            </div>
                            <div class="text-left" data-aos="fade-left" data-aos-duration="1100">
                                <img src="assets/images/about/1900bu-cuc.png" alt="Bưu cục rộng khắp" class="mx-auto mb-4">
                                <h3 class="mb-2 text-[#161D25] Montserrat-Bold">BƯU CỤC RỘNG KHẮP</h3>
                                <p class="text-[#161D25] Montserrat-Regular">Mạng lưới bưu cục rộng khắp hoạt động trên toàn quốc</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="services-section py-5">
            <div class="container">
                <h2 class="text-center mb-5 Montserrat-Bold">Dịch vụ của chúng tôi</h2>
                <div class="row">
                    <div class="col" data-aos="fade-up" data-aos-duration="500">
                        <div class="service-card">
                            <img src="assets/images/services/express.png" alt="J&T Express" class="service-icon">
                            <h3 class="Montserrat-Bold">J&T Express</h3>
                            <p class="Montserrat-Regular">Chuyển phát tiêu chuẩn</p>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-duration="700">
                        <div class="service-card">
                            <img src="assets/images/services/fast.png" alt="J&T Fast" class="service-icon">
                            <h3 class="Montserrat-Bold">J&T Fast</h3>
                            <p class="Montserrat-Regular">Dịch vụ Nhanh</p>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-duration="900">
                        <div class="service-card">
                            <img src="assets/images/services/super.png" alt="J&T Super" class="service-icon">
                            <h3 class="Montserrat-Bold">J&T Super</h3>
                            <p class="Montserrat-Regular">Siêu dịch vụ giao hàng</p>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-duration="1100">
                        <div class="service-card">
                            <img src="assets/images/services/fresh.png" alt="J&T Fresh" class="service-icon">
                            <h3 class="Montserrat-Bold">J&T Fresh</h3>
                            <p class="Montserrat-Regular">Giao hàng tươi sống</p>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-duration="1300">
                        <div class="service-card">
                            <img src="assets/images/services/international.png" alt="J&T International" class="service-icon">
                            <h3 class="Montserrat-Bold">J&T International</h3>
                            <p class="Montserrat-Regular">Giao hàng quốc tế</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Video -->
        <div class="video-container">
            <iframe width="1140" height="605" src="https://www.youtube.com/embed/yvSA11yttxk" title="J&amp;T EXPRESS CHÍNH THỨC NIÊM YẾT TẠI SỞ GIAO DỊCH CHỨNG KHOÁN HỒNG KÔNG (HKEX)" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <!-- Download App -->
        <img class="mt-3"src="assets/images/dowload.JPG" alt="About Us">
        <!-- Blog Section -->
        <section class="blog-section py-16">
            <div class="container">
                <div class="blog-header flex justify-between items-center mb-12">
                    <h2 class="Montserrat-Bold text-[40px]">BLOG</h2>
                    <div class="flex gap-4">
                        <a href="#" class="blog-tab active Montserrat-Bold">Tin tức nổi bật</a>
                        <a href="#" class="blog-tab Montserrat-Bold">J-Magazine</a>
                    </div>
                </div>
                <div class="blog-grid">
                    <div class="blog-card" data-aos="fade-up" data-aos-duration="500">
                        <div class="blog-image">
                            <img src="assets/images/blog/blog1.jpg" alt="J-Magazine 1">
                        </div>
                        <div class="blog-content">
                            <div class="blog-date Montserrat-Regular">11/04/2025</div>
                            <h3 class="blog-title Montserrat-Bold">J&T Express đẩy mạnh phát triển bền vững trên mạng lưới toàn cầu</h3>
                            <p class="blog-excerpt Montserrat-Regular">J&T Global Express Limited vừa công bố Báo cáo Phát triển bền vững đầu tiên, khẳng định cam kết phát triển bền vững.</p>
                        </div>
                    </div>
                    <div class="blog-card" data-aos="fade-up" data-aos-duration="700">
                        <div class="blog-image">
                            <img src="assets/images/blog/blog2.jpg" alt="J-Magazine 2">
                        </div>
                        <div class="blog-content">
                            <div class="blog-date Montserrat-Regular">04/04/2025</div>
                            <h3 class="blog-title Montserrat-Bold">Gửi hàng qua mã QR: Giải pháp vận chuyển thông minh từ J&T Express</h3>
                            <p class="blog-excerpt Montserrat-Regular">Nhằm giúp khách hàng có thể gửi hàng nhanh chóng và tiện lợi hơn, J&T Express phát triển tính năng gửi hàng qua mã QR.</p>
                        </div>
                    </div>
                    <div class="blog-card" data-aos="fade-up" data-aos-duration="900">
                        <div class="blog-image">
                            <img src="assets/images/blog/blog3.jpg" alt="J-Magazine 3">
                        </div>
                        <div class="blog-content">
                            <div class="blog-date Montserrat-Regular">22/05/2023</div>
                            <h3 class="blog-title Montserrat-Bold">Địa phương hóa - Chiến lược mũi nhọn của J&T Express</h3>
                            <p class="blog-excerpt Montserrat-Regular">J&T Express mang yếu tố quốc tế hóa nhập vào từng địa phương, tạo nên bản sắc riêng ở mỗi nơi mà hành trình đi qua.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Partners Section -->
        <section class="testimonials py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-5 Montserrat-Bold">Đối tác nói về chúng tôi</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <p>"Cửa hàng của tôi luôn ưu tiên lựa chọn J&T Fast để đảm bảo giao nhận nhanh chóng và chất lượng hàng hóa."</p>
                            </div>
                            <div class="testimonial-author">
                                <h6>Hoàng Xuân</h6>
                                <p>Chủ tiệm nước hoa tại Hà Nội</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <p>"Tôi đã từng hợp tác với nhiều đơn vị chuyển phát nhưng cuối cùng quyết định đồng hành cùng J&T Express. Phải nói rằng, hệ thống bưu cục đồng nhất về chất lượng khắp 63 tỉnh thành là điểm làm tôi hài lòng nhất."</p>
                            </div>
                            <div class="testimonial-author">
                                <h6>Võ Ngọc Trâm</h6>
                                <p>Chủ shop quần áo tại TP.HCM</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <p>"Gửi hàng mẫu cà phê đi nước ngoài không phải là chuyện dễ để cân đối thu chi, tối ưu chi phí cho công ty. May là công ty chúng tôi tìm được J&T International. Dịch vụ vượt mong đợi với giá cả phải chăng, lại còn hay có ưu đãi."</p>
                            </div>
                            <div class="testimonial-author">
                                <h6>Trần Minh Trí</h6>
                                <p>Giám đốc công ty cà phê tại Buôn Ma Thuột</p>
                            </div>
                        </div>
                    </div>
                    <!-- Add more testimonials -->
                </div>
            </div>
        </section>
    </main>
    
    <!-- Partner Slider -->
    <section class="partner-slider py-8 bg-white">
        <div class="container">
            <div class="partner-carousel">
                <div class="overflow-hidden">
                    <img src="https://jtexpress.vn/storage/app/uploads/public/629/9bd/146/6299bd1466138263975620.png" alt="J&T Express Partner" class="object-scale-down w-[120px] h-[100px] md:h-[100px] md:w-[220px]">
                </div>
                <div class="overflow-hidden">
                    <img src="https://jtexpress.vn/storage/app/uploads/public/629/9c1/536/6299c1536126c561166062.png" alt="J&T Express Partner" class="object-scale-down w-[120px] h-[100px] md:h-[100px] md:w-[220px]">
                </div>
                <div class="overflow-hidden">
                    <img src="https://jtexpress.vn/storage/app/uploads/public/629/9c3/412/6299c34126414957820349.png" alt="J&T Express Partner" class="object-scale-down w-[120px] h-[100px] md:h-[100px] md:w-[220px]">
                </div>
                <div class="overflow-hidden">
                    <img src="https://jtexpress.vn/storage/app/uploads/public/629/9c2/482/6299c2482b64c523421235.png" alt="J&T Express Partner" class="object-scale-down w-[120px] h-[100px] md:h-[100px] md:w-[220px]">
                </div>
                <div class="overflow-hidden">
                    <img src="https://jtexpress.vn/storage/app/uploads/public/629/9e4/4a9/6299e44a9aae4450398133.png" alt="J&T Express Partner" class="object-scale-down w-[120px] h-[100px] md:h-[100px] md:w-[220px]">
                </div>
                <div class="overflow-hidden">
                    <img src="https://jtexpress.vn/storage/app/uploads/public/627/1fb/b53/6271fbb5318e6298080325.png" alt="J&T Express Partner" class="object-scale-down w-[120px] h-[100px] md:h-[100px] md:w-[220px]">
                </div>
                <div class="overflow-hidden">
                    <img src="https://jtexpress.vn/storage/app/uploads/public/627/1fb/d18/6271fbd18abfc963904367.png" alt="J&T Express Partner" class="object-scale-down w-[120px] h-[100px] md:h-[100px] md:w-[220px]">
                </div>
                <div class="overflow-hidden">
                    <img src="https://jtexpress.vn/storage/app/uploads/public/627/1fb/e5e/6271fbe5ea59d010174455.png" alt="J&T Express Partner" class="object-scale-down w-[120px] h-[100px] md:h-[100px] md:w-[220px]">
                </div>
                <div class="overflow-hidden">
                    <img src="https://jtexpress.vn/storage/app/uploads/public/629/9c6/0a6/6299c60a6662d497515747.png" alt="J&T Express Partner" class="object-scale-down w-[120px] h-[100px] md:h-[100px] md:w-[220px]">
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <a class="navbar-brand" href="#">
                        <img src="assets/images/logo.png" alt="J&T Express" height="1000">
                    </a>
                    <h5 class="mt-3"> CÔNG TY TNHH MỘT THÀNH VIÊN CHUYỂN PHÁT NHANH THUẬN PHONG</h5>
                    <p style="font-size: 12px;"> Giấy CNĐKKD: 0313617136 - Ngày cấp: 13/01/2016, đăng ký thay đổi lần 06 ngày 25/09/2018</p>
                    <p style="font-size: 12px;">Cơ Quan Cấp: Phòng Đăng ký kinh doanh - Sở kế hoạch và đầu tư TP Hồ Chí Minh</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>TRUY CẬP NHANH</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Trung tâm chăm sóc khách hàng</a></li>
                        <li><a href="#" class="text-white">Liên hệ với chúng tôi</a></li>
                        <li><a href="#" class="text-white">Chính sách khiếu nại</a></li>
                        <li><a href="#" class="text-white">Điều khoản chung</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>THÔNG TIN CÔNG TY</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Câu chuyện thương hiệu</a></li>
                        <li><a href="#" class="text-white">Tầm nhìn và giá trị cốt lõi</a></li>
                        <li><a href="#" class="text-white">Lịch sử phát triển</a></li>
                        <li><a href="#" class="text-white">Mạng lưới và Quy mô</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>THÔNG TIN LIÊN HỆ</h5>
                    <p>Email: cskh@jtexpress.vn</p>
                    <p>Hotline: 1900 1088</p>
                    <p>10 Mai Chí Thọ, P. Thủ Thiêm, Thành phố Thủ Đức, TP. HCM</p>
                    <div class="app-download mt-3">
                        <h6>TẢI ỨNG DỤNG</h6>
                        <div class="app-buttons">
                            <a href="#" class="me-2"><img src="assets/images/appstore.png" alt="App Store" height="40"></a>
                            <a href="#"><img src="assets/images/ggplay.png" alt="Google Play" height="40"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <p class="mb-0">Copyright © 2025 J&T Express. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Slick Slider JS -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script>
        AOS.init();

        // Initialize partner carousel
        $(document).ready(function(){
            $('.partner-carousel').slick({
                slidesToShow: 5,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 2000,
                arrows: false,
                dots: false,
                infinite: true,
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 4
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 2
                        }
                    }
                ]
            });
        });

        // Blog tabs switching
        document.addEventListener('DOMContentLoaded', function() {
            const blogTabs = document.querySelectorAll('.blog-tab');
            const newsContent = `
                <div class="blog-card" data-aos="fade-up" data-aos-duration="500">
                    <div class="blog-image">
                        <img src="assets/images/blog/magazine1.jpg" alt="Địa phương hóa">
                    </div>
                    <div class="blog-content">
                        <div class="blog-date Montserrat-Regular">22/05/2023</div>
                        <h3 class="blog-title Montserrat-Bold">Địa phương hóa - Chiến lược mũi nhọn của J&T Express</h3>
                        <p class="blog-excerpt Montserrat-Regular">J&T Express mang yếu tố quốc tế hóa nhập vào từng địa phương, tạo nên bản sắc riêng ở mỗi nơi mà hành trình đi qua.</p>
                    </div>
                </div>
                <div class="blog-card" data-aos="fade-up" data-aos-duration="700">
                    <div class="blog-image">
                        <img src="assets/images/blog/magazine2.jpg" alt="Trung tâm trung chuyển">
                    </div>
                    <div class="blog-content">
                        <div class="blog-date Montserrat-Regular">13/07/2022</div>
                        <h3 class="blog-title Montserrat-Bold">Ra mắt Trung tâm trung chuyển lớn nhất của J&T Express</h3>
                        <p class="blog-excerpt Montserrat-Regular">Với diện tích 60.000m2, xử lý lên đến 2 triệu hàng hóa/ngày, Trung tâm trung chuyển mới khánh thành của J&T Express.</p>
                    </div>
                </div>
                <div class="blog-card" data-aos="fade-up" data-aos-duration="900">
                    <div class="blog-image">
                        <img src="assets/images/blog/magazine3.png" alt="Mẹ bỉm sữa">
                    </div>
                    <div class="blog-content">
                        <div class="blog-date Montserrat-Regular">20/06/2022</div>
                        <h3 class="blog-title Montserrat-Bold">Mẹ bỉm sữa và cơ hội khẳng định bản thân</h3>
                        <p class="blog-excerpt Montserrat-Regular">Lựa chọn cân bằng cuộc sống chưa bao giờ là dễ, đặc biệt là với mẹ bỉm sữa như chị Kim Kiều.</p>
                    </div>
                </div>
            `;

            const magazineContent = `
                <div class="blog-card" data-aos="fade-up" data-aos-duration="500">
                    <div class="blog-image">
                        <img src="assets/images/blog/blog1.jpg" alt="J-Magazine 1">
                    </div>
                    <div class="blog-content">
                        <div class="blog-date Montserrat-Regular">11/04/2025</div>
                        <h3 class="blog-title Montserrat-Bold">J&T Express đẩy mạnh phát triển bền vững trên mạng lưới toàn cầu</h3>
                        <p class="blog-excerpt Montserrat-Regular">J&T Global Express Limited vừa công bố Báo cáo Phát triển bền vững đầu tiên, khẳng định cam kết phát triển bền vững.</p>
                    </div>
                </div>
                <div class="blog-card" data-aos="fade-up" data-aos-duration="700">
                    <div class="blog-image">
                        <img src="assets/images/blog/blog2.jpg" alt="J-Magazine 2">
                    </div>
                    <div class="blog-content">
                        <div class="blog-date Montserrat-Regular">04/04/2025</div>
                        <h3 class="blog-title Montserrat-Bold">Gửi hàng qua mã QR: Giải pháp vận chuyển thông minh từ J&T Express</h3>
                        <p class="blog-excerpt Montserrat-Regular">Nhằm giúp khách hàng có thể gửi hàng nhanh chóng và tiện lợi hơn, J&T Express phát triển tính năng gửi hàng qua mã QR.</p>
                    </div>
                </div>
                <div class="blog-card" data-aos="fade-up" data-aos-duration="900">
                    <div class="blog-image">
                        <img src="assets/images/blog/blog3.jpg" alt="J-Magazine 3">
                    </div>
                    <div class="blog-content">
                        <div class="blog-date Montserrat-Regular">22/05/2023</div>
                        <h3 class="blog-title Montserrat-Bold">Địa phương hóa - Chiến lược mũi nhọn của J&T Express</h3>
                        <p class="blog-excerpt Montserrat-Regular">J&T Express mang yếu tố quốc tế hóa nhập vào từng địa phương, tạo nên bản sắc riêng ở mỗi nơi mà hành trình đi qua.</p>
                    </div>
                </div>
            `;

            const blogGrid = document.querySelector('.blog-grid');

            blogTabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Remove active class from all tabs
                    blogTabs.forEach(t => t.classList.remove('active'));
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Update content based on active tab
                    if (this.textContent === 'Tin tức nổi bật') {
                        blogGrid.innerHTML = magazineContent;
                    } else {
                        blogGrid.innerHTML = newsContent;
                    }
                    // Reinitialize AOS for new content
                    AOS.refresh();
                });
            });
        });
    </script>
</body>
</html> 