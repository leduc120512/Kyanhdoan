<?php

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

session_start();

if (isset($_GET['fetch_rooms'])) {
  // CHECK AVAILABILITY
  $chk_avail = json_decode($_GET['chk_avail'], true);

  if ($chk_avail['checkin'] != '' && $chk_avail['checkout'] != '') {
    $today_date = new DateTime(date("Y-m-d"));
    $checkin_date = new DateTime($chk_avail['checkin']);
    $checkout_date = new DateTime($chk_avail['checkout']);

    if ($checkin_date == $checkout_date) {
      echo "<h3 class='text-center text-danger'>Invalid Dates!</h3>";
      exit;
    } else if ($checkout_date < $checkin_date) {
      echo "<h3 class='text-center text-danger'>Invalid Dates!</h3>";
      exit;
    } else if ($checkin_date < $today_date) {
      echo "<h3 class='text-center text-danger'>Invalid Dates!</h3>";
      exit;
    }
  }

  // GUESTS
  $guests = json_decode($_GET['guests'], true);
  $adults = ($guests['adults'] != '') ? $guests['adults'] : 0;
  $children = ($guests['children'] != '') ? $guests['children'] : 0;

  // PRICE FILTER
  $price = json_decode($_GET['price'], true);
  $min_price = ($price['min_price'] != '') ? $price['min_price'] : 0;
  $max_price = ($price['max_price'] != '') ? $price['max_price'] : 999999999;

  // FACILITIES
  $facility_list = json_decode($_GET['facility_list'], true);

  $count_rooms = 0;
  $output = "";

  // SETTINGS
  $settings_q = "SELECT * FROM `settings` WHERE `sr_no`=1";
  $settings_r = mysqli_fetch_assoc(mysqli_query($con, $settings_q));

  // MAIN QUERY (đã thêm lọc giá)
  $room_res = select(
    "SELECT * FROM `rooms` 
     WHERE `adult`>=? 
     AND `children`>=? 
     AND `price` BETWEEN ? AND ?
     AND `status`=? 
     AND `removed`=?",
    [$adults, $children, $min_price, $max_price, 1, 0],
    'iiiiii'
  );

  while ($room_data = mysqli_fetch_assoc($room_res)) {
    // CHECK AVAILABILITY
    if ($chk_avail['checkin'] != '' && $chk_avail['checkout'] != '') {
      $tb_query = "SELECT COUNT(*) AS total_bookings FROM booking_order
        WHERE booking_status=? AND room_id=?
        AND check_out > ? AND check_in < ?";

      $values = ['booked', $room_data['id'], $chk_avail['checkin'], $chk_avail['checkout']];
      $tb_fetch = mysqli_fetch_assoc(select($tb_query, $values, 'siss'));

      if (($room_data['quantity'] - $tb_fetch['total_bookings']) == 0) {
        continue;
      }
    }

    // FACILITIES FILTER
    $fac_count = 0;

    $fac_q = mysqli_query($con, "SELECT f.name, f.id FROM facilities f 
      INNER JOIN room_facilities rfac ON f.id = rfac.facilities_id 
      WHERE rfac.room_id = '$room_data[id]'");

    $facilities_data = "";

    while ($fac_row = mysqli_fetch_assoc($fac_q)) {
      if (in_array($fac_row['id'], $facility_list['facilities'])) {
        $fac_count++;
      }

      $facilities_data .= "
        <span class='tag-chip me-1 mb-1'>
          $fac_row[name]
        </span>";
    }

    if (count($facility_list['facilities']) != $fac_count) {
      continue;
    }

    // FEATURES
    $fea_q = mysqli_query($con, "SELECT f.name FROM features f 
      INNER JOIN room_features rfea ON f.id = rfea.features_id 
      WHERE rfea.room_id = '$room_data[id]'");

    $features_data = "";

    while ($fea_row = mysqli_fetch_assoc($fea_q)) {
      $features_data .= "
        <span class='tag-chip me-1 mb-1'>
          $fea_row[name]
        </span>";
    }

    // IMAGE
    $room_thumb = ROOMS_IMG_PATH . "thumbnail.jpg";

    $thumb_q = mysqli_query($con, "SELECT * FROM room_images 
      WHERE room_id='$room_data[id]' AND thumb='1'");

    if (mysqli_num_rows($thumb_q) > 0) {
      $thumb_res = mysqli_fetch_assoc($thumb_q);
      $room_thumb = ROOMS_IMG_PATH . $thumb_res['image'];
    }

    // BOOK BUTTON
    $book_btn = "";

    if (!$settings_r['shutdown']) {
      $login = 0;

      if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
        $login = 1;
      }

      $book_btn = "<button onclick='checkLoginToBook($login,$room_data[id])' class='btn btn-sm w-100 text-white custom-bg mb-2'>Đặt ngay</button>";
    }

    // OUTPUT CARD
    $output .= "
    <div class='room-list-card mb-4'>
      <div class='room-list-grid'>
        <div class='room-list-media'>
          <img src='$room_thumb' class='img-fluid' alt='$room_data[name]'>
        </div>

        <div class='room-list-body'>
          <div>
            <h5 class='room-list-title'>$room_data[name]</h5>
            <div class='room-list-sub'>
              <i class='bi bi-people'></i>
              $room_data[adult] NL | $room_data[children] TE
            </div>
          </div>

          <div class='room-section'>
            <span class='section-title'>Không gian</span>
            <div class='tags'>
              $features_data
            </div>
          </div>

          <div class='room-section'>
            <span class='section-title'>Tiện ích</span>
            <div class='tags'>
              $facilities_data
            </div>
          </div>
        </div>

        <div class='room-list-actions text-center'>
          <div>
            <div class='room-list-price'>" . number_format($room_data['price']) . " VND</div>
            <div class='room-list-sub'>/ đêm</div>
          </div>
          $book_btn
          <a href='room_details.php?id=$room_data[id]' class='btn btn-sm btn-outline-dark'>Chi tiết</a>
        </div>
      </div>
    </div>
    ";

    $count_rooms++;
  }

  if ($count_rooms > 0) {
    echo $output;
  } else {
    echo "<h3 class='text-center text-danger'>Không có phòng phù hợp!</h3>";
  }
}
