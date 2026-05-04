<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - Xác nhận đặt phòng</title>
</head>

<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <?php

  /*
      Check room id from url is present or not
      Shutdown mode is active or not
      User is logged in or not
    */

  if (!isset($_GET['id']) || $settings_r['shutdown'] == true) {
    redirect('rooms.php');
  } else if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('rooms.php');
  }

  // filter and get room and user data

  $data = filteration($_GET);

  $room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? AND `removed`=?", [$data['id'], 1, 0], 'iii');

  if (mysqli_num_rows($room_res) == 0) {
    redirect('rooms.php');
  }

  $room_data = mysqli_fetch_assoc($room_res);

  $_SESSION['room'] = [
    "id" => $room_data['id'],
    "name" => $room_data['name'],
    "price" => $room_data['price'],
    "payment" => null,
    "available" => false,
  ];


  $user_res = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], "i");
  $user_data = mysqli_fetch_assoc($user_res);

  ?>

  <div class="container">
    <div class="row">

      <div class="col-12 my-5 mb-4 px-4">
        <h4 class="mt-4 fw-bold h-font">XÁC NHẬN ĐẶT PHÒNG</h4>
        <div class="breadcrumb-mini">
          <a href="index.php" class="text-secondary text-decoration-none">Trang chủ</a>
          <span class="text-secondary"> > </span>
          <a href="rooms.php" class="text-secondary text-decoration-none">Danh sách phòng</a>
          <span class="text-secondary"> > </span>
          <a href="#" class="text-secondary text-decoration-none">Xác nhận đặt phòng</a>
        </div>
        <?php
        if (isset($_SESSION['coupon_error'])) {
          $msg = $_SESSION['coupon_error'];
          unset($_SESSION['coupon_error']);
          echo "<div class='alert alert-danger mt-3 mb-0'>$msg</div>";
        }
        ?>
      </div>

      <div class="col-lg-7 col-md-12 px-4">
        <?php

        $room_thumb = ROOMS_IMG_PATH . "thumbnail.jpg";
        $thumb_q = mysqli_query($con, "SELECT * FROM `room_images` 
            WHERE `room_id`='$room_data[id]' 
            AND `thumb`='1'");

        if (mysqli_num_rows($thumb_q) > 0) {
          $thumb_res = mysqli_fetch_assoc($thumb_q);
          $room_thumb = ROOMS_IMG_PATH . $thumb_res['image'];
        }

        echo <<<data
            <div class="card p-3 booking-card">
              <img src="$room_thumb" class="img-fluid rounded mb-3">
              <h5>$room_data[name]</h5>
              <h6>$room_data[price] VND / đêm</h6>
            </div>
          data;

        ?>
      </div>

      <div class="col-lg-5 col-md-12 px-4">
        <div class="card mb-4 booking-card">
          <div class="card-body">
            <form action="pay_now.php" method="POST" id="booking_form">
              <h6 class="mb-3">Thông tin chi tiết</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Tên</label>
                  <input name="name" type="text" value="<?php echo $user_data['name'] ?>" class="form-control shadow-none" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Số điện thoại</label>
                  <input name="phonenum" type="number" value="<?php echo $user_data['phonenum'] ?>" class="form-control shadow-none" required>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label">Địa chỉ</label>
                  <textarea name="address" class="form-control shadow-none" rows="1" required><?php echo $user_data['address'] ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Nhận phòng</label>
                  <input name="checkin" onchange="check_availability()" type="date" class="form-control shadow-none" required>
                </div>
                <div class="col-md-6 mb-4">
                  <label class="form-label">Trả phòng</label>
                  <input name="checkout" onchange="check_availability()" type="date" class="form-control shadow-none" required>
                </div>

                <div class="col-md-8 mb-3">
                  <label class="form-label">Mã giảm giá</label>
                  <div class="input-group">
                    <input id="coupon_code" name="coupon_code" type="text" class="form-control shadow-none" placeholder="Nhập mã giảm giá">
                    <button type="button" class="btn btn-outline-dark" onclick="check_availability()">Áp dụng</button>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Mã giới thiệu</label>
                  <input name="referral_code" type="text" class="form-control shadow-none" placeholder="Tùy chọn">
                </div>

                <div class="col-12">
                  <div class="spinner-border text-info mb-3 d-none" id="info_loader" role="status">
                    <span class="visually-hidden">Xin vui lòng chờ...</span>
                  </div>

                  <h6 class="mb-3 text-danger" id="pay_info">Chọn ngày nhận phòng và trả phòng để tính tiền cọc!</h6>
                  <div class="col-md-12 mb-3">
                    <label class="form-label">Phương thức thanh toán</label>
                    <select name="payment_method" class="form-select shadow-none" required>
                      <option value="offline" selected>Thanh toán tiền mặt</option>
                      <option value="online">Thanh toán online</option>
                    </select>

                  </div>

                  <div class="alert alert-info small mb-3">
                    Lưu ý: Cọc tối thiểu 30% tổng tiền phòng. Sau 24h kể từ lúc đặt, yêu cầu hủy qua trang liên hệ sẽ mất cọc.
                  </div>
                  <button name="pay_now" class="btn w-100 text-white custom-bg shadow-none mb-1" disabled>Thanh toán</button>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>

    </div>
  </div>


  <?php require('inc/footer.php'); ?>
  <script>
    let booking_form = document.getElementById('booking_form');
    let info_loader = document.getElementById('info_loader');
    let pay_info = document.getElementById('pay_info');

    function format_money(value) {
      return Number(value).toLocaleString('vi-VN');
    }

    function check_availability() {
      let checkin_val = booking_form.elements['checkin'].value;
      let checkout_val = booking_form.elements['checkout'].value;

      booking_form.elements['pay_now'].setAttribute('disabled', true);

      if (checkin_val != '' && checkout_val != '') {
        pay_info.classList.add('d-none');
        pay_info.classList.replace('text-dark', 'text-danger');
        info_loader.classList.remove('d-none');

        let data = new FormData();

        data.append('check_availability', '');
        data.append('check_in', checkin_val);
        data.append('check_out', checkout_val);
        data.append('coupon_code', booking_form.elements['coupon_code'].value.trim());

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/confirm_booking.php", true);

        xhr.onload = function() {
          let data = JSON.parse(this.responseText);

          if (data.status == 'check_in_out_equal') {
            pay_info.innerText = "You cannot check-out on the same day!";
          } else if (data.status == 'check_out_earlier') {
            pay_info.innerText = "Check-out date is earlier than check-in date!";
          } else if (data.status == 'check_in_earlier') {
            pay_info.innerText = "Check-in date is earlier than today's date!";
          } else if (data.status == 'date_conflict') {
            // Xử lý trường hợp ngày trùng - hiển thị alert cảnh báo
            let availableText = "Phòng còn lại: " + data.available_rooms + "/" + data.total_rooms;
            let confirmMsg = data.message + "\n\n" + availableText +
              "\n\nSố đêm: " + data.days +
              "\nTổng tiền: " + format_money(data.total) + " VND" +
              "\nGiảm giá: " + format_money(data.discount) + " VND" +
              "\nThanh toán cọc (30%): " + format_money(data.deposit) + " VND" +
              "\nCòn lại: " + format_money(data.remaining) + " VND";

            if (confirm(confirmMsg)) {
              // Nếu người dùng xác nhận, gửi lại request với confirm_conflict = 1
              let dataConfirm = new FormData();
              dataConfirm.append('check_availability', '');
              dataConfirm.append('check_in', checkin_val);
              dataConfirm.append('check_out', checkout_val);
              dataConfirm.append('coupon_code', booking_form.elements['coupon_code'].value.trim());
              dataConfirm.append('confirm_conflict', '1');

              let xhrConfirm = new XMLHttpRequest();
              xhrConfirm.open("POST", "ajax/confirm_booking.php", true);

              xhrConfirm.onload = function() {
                let dataConfirm = JSON.parse(this.responseText);
                if (dataConfirm.status == 'available') {
                  let availableRoomsText = "Phòng còn lại: " + dataConfirm.available_rooms + "/" + dataConfirm.total_rooms;
                  pay_info.innerHTML =
                    availableRoomsText + "<br>" +
                    "Số đêm: " + dataConfirm.days +
                    "<br>Tổng tiền: " + format_money(dataConfirm.total) + " VND" +
                    "<br>Giảm giá: " + format_money(dataConfirm.discount) + " VND" +
                    "<br>Thanh toán cọc (30%): " + format_money(dataConfirm.deposit) + " VND" +
                    "<br>Còn lại: " + format_money(dataConfirm.remaining) + " VND";
                  pay_info.classList.replace('text-danger', 'text-dark');
                  booking_form.elements['pay_now'].removeAttribute('disabled');
                }
                pay_info.classList.remove('d-none');
                info_loader.classList.add('d-none');
              }

              xhrConfirm.send(dataConfirm);
            } else {
              pay_info.innerText = "Bạn đã hủy việc đặt phòng. Vui lòng chọn ngày khác.";
              pay_info.classList.remove('d-none');
              info_loader.classList.add('d-none');
            }
            return;
          } else if (data.status == 'unavailable') {
            pay_info.innerText = "Phòng này đã hết phòng cho ngày bạn chọn!";
          } else if (data.status == 'invalid_coupon') {
            pay_info.innerText = data.message || "Mã giảm giá không hợp lệ!";
          } else {
            let availableRoomsText = "Phòng còn lại: " + data.available_rooms + "/" + data.total_rooms;
            pay_info.innerHTML =
              availableRoomsText + "<br>" +
              "Số đêm: " + data.days +
              "<br>Tổng tiền: " + format_money(data.total) + " VND" +
              "<br>Giảm giá: " + format_money(data.discount) + " VND" +
              "<br>Thanh toán cọc (30%): " + format_money(data.deposit) + " VND" +
              "<br>Còn lại: " + format_money(data.remaining) + " VND";
            pay_info.classList.replace('text-danger', 'text-dark');
            booking_form.elements['pay_now'].removeAttribute('disabled');
          }

          pay_info.classList.remove('d-none');
          info_loader.classList.add('d-none');
        }

        xhr.send(data);
      }

    }
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