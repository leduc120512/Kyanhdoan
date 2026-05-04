<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - Tiện ích</title>
</head>

<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">TIỆN ÍCH</h2>
    <div class="h-line bg-dark"></div>
    <p class="text-center mt-3">
      Khách sạn cung cấp đầy đủ tiện nghi hiện đại như Wi-Fi tốc độ cao, máy lạnh, truyền hình, và máy nước nóng. <br>
      Quý khách có thể thư giãn tại spa, tận hưởng không gian ban công thoáng mát, hoặc sử dụng khu bếp tiện nghi và ghế sofa êm ái. <br>
      Chúng tôi cam kết mang đến trải nghiệm nghỉ dưỡng thoải mái và trọn vẹn.
    </p>
  </div>

  <div class="container">
    <div class="row">
      <?php
      $res = selectAll('facilities');
      $path = FACILITIES_IMG_PATH;

      while ($row = mysqli_fetch_assoc($res)) {
        echo <<<data
            <div class="col-lg-4 col-md-6 mb-5 px-4">
              <div class="bg-white p-4 facility-card">
                <div class="d-flex align-items-center mb-2">
                  <img src="$path$row[icon]" width="40px">
                  <h5 class="m-0 ms-3">$row[name]</h5>
                </div>
                <p>$row[description]</p>
              </div>
            </div>
          data;
      }
      ?>
    </div>
  </div>


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