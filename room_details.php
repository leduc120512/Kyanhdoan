<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - Chi tiết phòng</title>

  <style>
    :root {
      --box-border: #d9dde3;
      --box-dark: #111827;
      --box-muted: #6b7280;
      --box-soft: #f3f4f6;
      --box-bg: #ffffff;
    }

    body.bg-light {
      background: #f1f3f6 !important;
      color: #111827;
    }

    .room-page-wrap {
      padding-bottom: 60px;
    }

    .room-title-block {
      background: #fff;
      border: 1px solid var(--box-border);
      border-left: 6px solid #111827;
      padding: 22px 24px;
      box-shadow: 0 12px 28px rgba(17, 24, 39, .08);
    }

    .room-title-block h2 {
      margin: 0 0 10px;
      font-size: 32px;
      line-height: 1.15;
      letter-spacing: -.4px;
      text-transform: uppercase;
      color: #111827;
    }

    .breadcrumb-mini {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 8px;
      font-size: 14px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .4px;
    }

    .breadcrumb-mini a,
    .breadcrumb-mini span {
      color: #6b7280 !important;
    }

    .room-gallery-box,
    .detail-card,
    .description-box,
    .reviews-box {
      background: #fff;
      border: 1px solid var(--box-border);
      border-radius: 0 !important;
      box-shadow: 0 14px 34px rgba(17, 24, 39, .09);
    }

    .room-gallery-box {
      padding: 10px;
    }

    #roomCarousel,
    #roomCarousel .carousel-inner,
    #roomCarousel .carousel-item,
    #roomCarousel img {
      border-radius: 0 !important;
    }

    #roomCarousel img {
      height: 520px;
      object-fit: cover;
      border: 1px solid #e5e7eb;
    }

    .carousel-control-prev,
    .carousel-control-next {
      width: 54px;
      opacity: 1;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
      background-color: rgba(17, 24, 39, .9);
      background-size: 54%;
      width: 42px;
      height: 42px;
      border-radius: 0;
      border: 1px solid rgba(255, 255, 255, .75);
    }

    .detail-card {
      border: 1px solid #cfd5dd !important;
      overflow: hidden;
    }

    .detail-card .card-body {
      padding: 26px;
    }

    .detail-card h4 {
      font-size: 30px;
      font-weight: 900;
      color: #111827;
      margin-bottom: 16px;
      padding-bottom: 16px;
      border-bottom: 2px solid #111827;
    }

    .detail-card h6 {
      font-size: 13px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .8px;
      color: #111827;
      margin-bottom: 10px !important;
    }

    .tags {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .tag-chip {
      display: inline-flex;
      align-items: center;
      min-height: 34px;
      padding: 7px 12px;
      background: #f3f4f6;
      border: 1px solid #d1d5db;
      border-radius: 0;
      color: #1f2937;
      font-size: 13px;
      font-weight: 700;
      line-height: 1.2;
    }

    .detail-card .custom-bg,
    .detail-card .btn {
      border-radius: 0 !important;
      min-height: 48px;
      font-size: 15px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .7px;
      border: 1px solid #111827 !important;
      background: #111827 !important;
      transition: .18s ease-in-out;
    }

    .detail-card .custom-bg:hover,
    .detail-card .btn:hover {
      background: #fff !important;
      color: #111827 !important;
      transform: translateY(-1px);
    }

    .description-box,
    .reviews-box {
      padding: 26px;
    }

    .section-title {
      display: inline-block;
      margin-bottom: 16px;
      padding-bottom: 8px;
      border-bottom: 3px solid #111827;
      font-size: 20px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .6px;
      color: #111827;
    }

    .description-box p {
      margin: 0;
      color: #374151;
      font-size: 15px;
      line-height: 1.8;
    }

    .review-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 16px;
    }

    .review-card,
    .review-empty {
      background: #fff;
      border: 1px solid #d1d5db;
      border-radius: 0;
      padding: 18px;
      box-shadow: 0 8px 20px rgba(17, 24, 39, .06);
    }

    .review-card:hover {
      border-color: #111827;
    }

    .review-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 14px;
      padding-bottom: 12px;
      border-bottom: 1px solid #e5e7eb;
    }

    .review-user {
      display: flex;
      align-items: center;
      gap: 11px;
      min-width: 0;
    }

    .review-avatar {
      width: 42px;
      height: 42px;
      object-fit: cover;
      border-radius: 0;
      border: 1px solid #111827;
      background: #f3f4f6;
    }

    .review-name {
      max-width: 150px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      font-weight: 900;
      color: #111827;
      line-height: 1.2;
    }

    .review-subtitle {
      color: var(--box-muted);
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .3px;
    }

    .review-stars {
      white-space: nowrap;
      color: #f59e0b;
      font-size: 13px;
      line-height: 1;
    }

    .review-text {
      margin: 0;
      color: #374151;
      font-size: 14px;
      line-height: 1.65;
    }

    .review-empty {
      grid-column: 1 / -1;
      color: #6b7280;
      font-weight: 700;
      text-align: center;
      padding: 30px;
    }

    @media (max-width: 991px) {
      #roomCarousel img {
        height: 420px;
      }

      .review-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 575px) {
      .room-title-block {
        padding: 18px;
      }

      .room-title-block h2 {
        font-size: 24px;
      }

      #roomCarousel img {
        height: 280px;
      }

      .detail-card .card-body,
      .description-box,
      .reviews-box {
        padding: 18px;
      }

      .detail-card h4 {
        font-size: 24px;
      }

      .review-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <?php
  if (!isset($_GET['id'])) {
    redirect('rooms.php');
  }

  $data = filteration($_GET);

  $room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? AND `removed`=?", [$data['id'], 1, 0], 'iii');

  if (mysqli_num_rows($room_res) == 0) {
    redirect('rooms.php');
  }

  $room_data = mysqli_fetch_assoc($room_res);
  ?>

  <div class="container room-page-wrap">
    <div class="row">

      <div class="col-12 my-5 mb-4 px-4">
        <div class="room-title-block">
          <h2 class="fw-bold"><?php echo $room_data['name'] ?></h2>
          <div class="breadcrumb-mini">
            <a href="index.php" class="text-secondary text-decoration-none">Trang chủ</a>
            <span class="text-secondary">/</span>
            <a href="rooms.php" class="text-secondary text-decoration-none">Danh sách phòng</a>
          </div>
        </div>
      </div>

      <div class="col-lg-7 col-md-12 px-4">
        <div class="room-gallery-box">
          <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              <?php

              $room_img = ROOMS_IMG_PATH . "thumbnail.jpg";
              $img_q = mysqli_query($con, "SELECT * FROM `room_images` 
                WHERE `room_id`='$room_data[id]'");

              if (mysqli_num_rows($img_q) > 0) {
                $active_class = 'active';

                while ($img_res = mysqli_fetch_assoc($img_q)) {
                  echo "
                    <div class='carousel-item $active_class'>
                      <img src='" . ROOMS_IMG_PATH . $img_res['image'] . "' class='d-block w-100 rounded'>
                    </div>
                  ";
                  $active_class = '';
                }
              } else {
                echo "<div class='carousel-item active'>
                  <img src='$room_img' class='d-block w-100'>
                </div>";
              }

              ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Lùi</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Tiến</span>
            </button>
          </div>
        </div>

      </div>

      <div class="col-lg-5 col-md-12 px-4">
        <div class="card mb-4 detail-card">
          <div class="card-body">
            <?php

            $room_price = number_format($room_data['price']);
            echo <<<price
                <h4>$room_price VND / đêm</h4>
              price;

            $rating_q = "SELECT AVG(rating) AS `avg_rating` FROM `rating_review`
                WHERE `room_id`='$room_data[id]' ORDER BY `sr_no` DESC LIMIT 20";

            $rating_res = mysqli_query($con, $rating_q);
            $rating_fetch = mysqli_fetch_assoc($rating_res);

            $rating_data = "";

            if ($rating_fetch['avg_rating'] != NULL) {
              for ($i = 0; $i < $rating_fetch['avg_rating']; $i++) {
                $rating_data .= "<i class='bi bi-star-fill text-warning'></i> ";
              }
            }

            echo <<<rating
                <div class="mb-3">
                  $rating_data
                </div>
              rating;

            $fea_q = mysqli_query($con, "SELECT f.name FROM `features` f 
                INNER JOIN `room_features` rfea ON f.id = rfea.features_id 
                WHERE rfea.room_id = '$room_data[id]'");

            $features_data = "";
            while ($fea_row = mysqli_fetch_assoc($fea_q)) {
              $features_data .= "<span class='tag-chip text-wrap me-1 mb-1'>
                    $fea_row[name]
                  </span>";
            }

            echo <<<features
                <div class="mb-3">
                  <h6 class="mb-1">Không gian</h6>
                  <div class="tags">
                    $features_data
                  </div>
                </div>
              features;

            $fac_q = mysqli_query($con, "SELECT f.name FROM `facilities` f 
                INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id 
                WHERE rfac.room_id = '$room_data[id]'");

            $facilities_data = "";
            while ($fac_row = mysqli_fetch_assoc($fac_q)) {
              $facilities_data .= "<span class='tag-chip text-wrap me-1 mb-1'>
                    $fac_row[name]
                  </span>";
            }

            echo <<<facilities
                <div class="mb-3">
                  <h6 class="mb-1">Tiện ích</h6>
                  <div class="tags">
                    $facilities_data
                  </div>
                </div>
              facilities;

            echo <<<guests
                <div class="mb-3">
                  <h6 class="mb-1">Số khách</h6>
                  <div class="tags">
                    <span class="tag-chip text-wrap">
                      $room_data[adult] Người lớn
                    </span>
                    <span class="tag-chip text-wrap">
                      $room_data[children] Trẻ em
                    </span>
                  </div>
                </div>
              guests;

            echo <<<area
                <div class="mb-3">
                  <h6 class="mb-1">Diện tích</h6>
                  <div class="tags">
                    <span class='tag-chip text-wrap me-1 mb-1'>
                      $room_data[area] m2
                    </span>
                  </div>
                </div>
              area;

            if (!$settings_r['shutdown']) {
              $login = 0;
              if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
                $login = 1;
              }
              echo <<<book
                  <button onclick='checkLoginToBook($login,$room_data[id])' class="btn w-100 text-white custom-bg shadow-none mb-1">Đặt ngay</button>
                book;
            }

            ?>
          </div>
        </div>
      </div>

      <div class="col-12 mt-4 px-4">
        <div class="description-box mb-5">
          <h5 class="section-title">Mô tả</h5>
          <p>
            <?php echo $room_data['description'] ?>
          </p>
        </div>

        <div class="reviews-box">
          <h5 class="section-title">Trải nghiệm khách hàng</h5>
          <div class="review-grid">

            <?php
            $review_q = "SELECT rr.*,uc.name AS uname, uc.profile, r.name AS rname FROM `rating_review` rr
                INNER JOIN `user_cred` uc ON rr.user_id = uc.id
                INNER JOIN `rooms` r ON rr.room_id = r.id
                WHERE rr.room_id = '$room_data[id]'
                ORDER BY `sr_no` DESC LIMIT 15";

            $review_res = mysqli_query($con, $review_q);
            $img_path = USERS_IMG_PATH;

            if (mysqli_num_rows($review_res) == 0) {
              echo "<div class='review-empty'>Chưa có đánh giá. Hãy là người đầu tiên chia sẻ trải nghiệm.</div>";
            } else {
              while ($row = mysqli_fetch_assoc($review_res)) {
                $stars = "<i class='bi bi-star-fill'></i>";
                for ($i = 1; $i < $row['rating']; $i++) {
                  $stars .= " <i class='bi bi-star-fill'></i>";
                }

                echo <<<reviews
                    <div class="review-card">
                      <div class="review-header">
                        <div class="review-user">
                          <img src="$img_path$row[profile]" class="review-avatar" loading="lazy" width="42" height="42">
                          <div>
                            <div class="review-name">$row[uname]</div>
                            <div class="review-subtitle">Đã lưu trú</div>
                          </div>
                        </div>
                        <div class="review-stars">$stars</div>
                      </div>
                      <p class="review-text">$row[review]</p>
                    </div>
                  reviews;
              }
            }
            ?>

          </div>
        </div>
      </div>

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