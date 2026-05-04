<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - Về chúng tôi</title>
</head>

<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">VỀ CHÚNG TÔI</h2>
    <div class="h-line bg-dark"></div>
    <p class="text-center mt-3">
      Sinh viên 522 - Trường Đại học Phương Đông - Khoa Công nghệ Thông tin <br>
      Nhóm thực hiện: Đặng Đình Nam Anh - Nguyễn Kỳ Anh - Nguyễn Thị Ngọc Mai <br>
    </p>
  </div>

  <div class="container">
    <div class="row justify-content-between align-items-center">
      <div class="col-lg-6 col-md-5 mb-4 order-lg-1 order-md-1 order-2">
        <h3 class="mb-3">Lời cảm ơn</h3>
        <p>
          Chúng em xin được bày tỏ lòng biết ơn đến các thầy, cô trường Đại học Phương Đông khoa Công Nghệ Thông Tin đã giúp đỡ, hỗ trợ nhiệt tình trong suốt quá trình học. <br><br>
          Đặc biệt gửi lời cảm ơn đến cô Nguyễn Thị Thanh đã trực tiếp giúp đỡ, hỗ trợ, hướng dẫn em hoàn thành khóa luận này.
        </p>
      </div>
      <div class="col-lg-5 col-md-5 mb-4 order-lg-2 order-md-2 order-1">
        <img src="images/about/about1.png" class="w-100">
      </div>
    </div>
  </div>

  <div class="container mt-5">
    <div class="row">
      <div class="col-lg-3 col-md-6 mb-4 px-4">
        <div class="bg-white p-4 text-center stat-card">
          <img src="images/about/hotel.svg" width="70px">
          <h4 class="mt-3">100+ PHÒNG</h4>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4 px-4">
        <div class="bg-white p-4 text-center stat-card">
          <img src="images/about/customers.svg" width="70px">
          <h4 class="mt-3">200+ KHÁCH HÀNG</h4>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4 px-4">
        <div class="bg-white p-4 text-center stat-card">
          <img src="images/about/rating.svg" width="70px">
          <h4 class="mt-3">150+ ĐÁNH GIÁ</h4>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4 px-4">
        <div class="bg-white p-4 text-center stat-card">
          <img src="images/about/staff.svg" width="70px">
          <h4 class="mt-3">50+ NHÂN SỰ</h4>
        </div>
      </div>
    </div>
  </div>

  <h3 class="my-5 fw-bold h-font text-center">NGƯỜI DÙNG</h3>

  <div class="container px-4">
    <div class="swiper mySwiper">
      <div class="swiper-wrapper mb-5">
        <?php
        $about_r = selectAll('team_details');
        $path = ABOUT_IMG_PATH;
        while ($row = mysqli_fetch_assoc($about_r)) {
          echo <<<data
              <div class="swiper-slide bg-white text-center overflow-hidden team-card">
                <img src="$path$row[picture]" class="w-100">
                <h5 class="mt-2">$row[name]</h5>
              </div>
            data;
        }

        ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>


  <?php require('inc/footer.php'); ?>

  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

  <script>
    var swiper = new Swiper(".mySwiper", {
      spaceBetween: 40,
      pagination: {
        el: ".swiper-pagination",
      },
      breakpoints: {
        320: {
          slidesPerView: 1,
        },
        640: {
          slidesPerView: 1,
        },
        768: {
          slidesPerView: 3,
        },
        1024: {
          slidesPerView: 3,
        },
      }
    });
  </script>

  <script>
    window.addEventListener("scroll", function() {
      let navbar = document.querySelector(".custom-navbar");

      if (window.scrollY > 80) {
        navbar.classList.add("scrolled");
      } else {
        navbar.classList.remove("scrolled");
      }
    });
  </script>
</body>

</html>