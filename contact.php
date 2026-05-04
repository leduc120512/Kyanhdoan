<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - Liên hệ</title>
</head>

<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">PHẢN HỒI</h2>
    <div class="h-line bg-dark"></div>
    <p class="text-center mt-3">
      Chúng tôi luôn sẵn sàng hỗ trợ bạn! <br>
      Liên hệ ngay qua hotline, email, hoặc biểu mẫu trực tuyến để được tư vấn và giải đáp thắc mắc. <br>
      Đội ngũ của chúng tôi sẽ phản hồi nhanh chóng, đảm bảo mang đến sự hài lòng cho quý khách.
    </p>
    <p class="text-center text-muted small">
      Lưu ý: Yêu cầu hủy sau 24h kể từ lúc đặt phòng vui lòng gửi qua trang liên hệ và sẽ mất tiền cọc.
    </p>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-lg-6 col-md-6 mb-5 px-4">

        <div class="bg-white p-4 contact-card">
          <iframe class="w-100 rounded mb-4" height="320px" src="<?php echo $contact_r['iframe'] ?>" loading="lazy"></iframe>

          <h5>Địa chỉ</h5>
          <a href="<?php echo $contact_r['gmap'] ?>" target="_blank" class="d-inline-block text-decoration-none text-dark mb-2">
            <i class="bi bi-geo-alt-fill"></i> <?php echo $contact_r['address'] ?>
          </a>

          <h5 class="mt-4">Tổng đài viên</h5>
          <a href="tel: +<?php echo $contact_r['pn1'] ?>" class="d-inline-block mb-2 text-decoration-none text-dark">
            <i class="bi bi-telephone-fill"></i> +<?php echo $contact_r['pn1'] ?>
          </a>
          <br>


          <h5 class="mt-4">Email</h5>
          <a href="mailto: <?php echo $contact_r['email'] ?>" class="d-inline-block text-decoration-none text-dark">
            <i class="bi bi-envelope-fill"></i> <?php echo $contact_r['email'] ?>
          </a>

          <h5 class="mt-4">Theo dõi chúng tôi</h5>
          <?php
          if ($contact_r['tw'] != '') {
            echo <<<data
                <a href="$contact_r[tw]" class="d-inline-block text-dark fs-5 me-2">
                  <i class="bi bi-twitter me-1"></i>
                </a>
              data;
          }
          ?>

          <a href="<?php echo $contact_r['fb'] ?>" class="d-inline-block text-dark fs-5 me-2">
            <i class="bi bi-facebook me-1"></i>
          </a>
          <a href="<?php echo $contact_r['insta'] ?>" class="d-inline-block text-dark fs-5">
            <i class="bi bi-instagram me-1"></i>
          </a>
        </div>
      </div>
      <div class="col-lg-6 col-md-6 px-4">
        <div class="bg-white p-4 contact-card">
          <form method="POST" class="contact-form">
            <h5 class="fw-bold h-font">Để lại lời nhắn</h5>
            <div class="mt-3">
              <label class="form-label">Tên</label>
              <input name="name" required type="text" class="form-control shadow-none">
            </div>
            <div class="mt-3">
              <label class="form-label">Email</label>
              <input name="email" required type="email" class="form-control shadow-none">
            </div>
            <div class="mt-3">
              <label class="form-label">Tiêu đề</label>
              <input name="subject" required type="text" class="form-control shadow-none">
            </div>
            <div class="mt-3">
              <label class="form-label">Nội dung</label>
              <textarea name="message" required class="form-control shadow-none" rows="5"></textarea>
            </div>
            <button type="submit" name="send" class="btn text-white custom-bg mt-3">Gửi</button>
          </form>
        </div>
      </div>
    </div>
  </div>


  <?php

  if (isset($_POST['send'])) {
    $frm_data = filteration($_POST);

    $q = "INSERT INTO `user_queries`(`name`, `email`, `subject`, `message`) VALUES (?,?,?,?)";
    $values = [$frm_data['name'], $frm_data['email'], $frm_data['subject'], $frm_data['message']];

    $res = insert($q, $values, 'ssss');
    if ($res == 1) {
      alert('success', 'Email đã được gửi đi!');
    } else {
      alert('error', 'Hệ thống đang được bảo trì! Hãy thử lại sau ít phút.');
    }
  }
  ?>

  <?php require('inc/footer.php'); ?>
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