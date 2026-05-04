<?php

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');



if (isset($_POST['check_availability'])) {
  $frm_data = filteration($_POST);
  $deposit_rate = 0.30;
  $status = "";
  $result = "";

  // check in and out validations


  $today_date = new DateTime(date("Y-m-d"));
  $checkin_date = new DateTime($frm_data['check_in']);
  $checkout_date = new DateTime($frm_data['check_out']);

  if ($checkin_date == $checkout_date) {
    $status = 'check_in_out_equal';
    $result = json_encode(["status" => $status]);
  } else if ($checkout_date < $checkin_date) {
    $status = 'check_out_earlier';
    $result = json_encode(["status" => $status]);
  } else if ($checkin_date < $today_date) {
    $status = 'check_in_earlier';
    $result = json_encode(["status" => $status]);
  }

  // check booking availability if status is blank else return the error

  if ($status != '') {
    echo $result;
  } else {
    session_start();

    // run query to check room is available or not 

    $tb_query = "SELECT COUNT(*) AS `total_bookings` FROM `booking_order`
        WHERE booking_status=? AND room_id=?
        AND check_out > ? AND check_in < ?";

    $values = ['booked', $_SESSION['room']['id'], $frm_data['check_in'], $frm_data['check_out']];
    $tb_fetch = mysqli_fetch_assoc(select($tb_query, $values, 'siss'));

    $rq_result = select("SELECT `quantity` FROM `rooms` WHERE `id`=?", [$_SESSION['room']['id']], 'i');
    $rq_fetch = mysqli_fetch_assoc($rq_result);

    // Tính số phòng còn lại
    $available_rooms = $rq_fetch['quantity'] - $tb_fetch['total_bookings'];
    if ($available_rooms < 0) {
      $available_rooms = 0;
    }

    // Kiểm tra xem người dùng đã đặt trùng ngày chưa
    $user_conflict = false;
    $user_conflict_count = 0;
    if (isset($_SESSION['uId'])) {
      $ub_query = "SELECT COUNT(*) AS `total_user_bookings` FROM `booking_order`
          WHERE user_id=? AND booking_status IN (?, ?)
          AND check_out > ? AND check_in < ?";
      $ub_values = [$_SESSION['uId'], 'booked', 'pending', $frm_data['check_in'], $frm_data['check_out']];
      $ub_fetch = mysqli_fetch_assoc(select($ub_query, $ub_values, 'issss'));
      $user_conflict_count = (int)$ub_fetch['total_user_bookings'];
      if ($user_conflict_count > 0) {
        $user_conflict = true;
      }
    }

    // Kiểm tra xem có ngày trùng không (nếu có ít nhất 1 đặt hoặc người dùng đã đặt)
    $has_conflict = false;
    if ($tb_fetch['total_bookings'] > 0 || $user_conflict) {
      $has_conflict = true;
    }

    // Nếu hết phòng, thì báo lỗi
    if ($available_rooms <= 0) {
      $status = 'unavailable';
      $result = json_encode(['status' => $status]);
      echo $result;
      exit;
    }

    $count_days = date_diff($checkin_date, $checkout_date)->days;
    $total = $_SESSION['room']['price'] * $count_days;
    $discount = 0;
    $coupon_code = isset($frm_data['coupon_code']) ? trim($frm_data['coupon_code']) : '';

    if ($coupon_code !== '') {
      $coupon_res = select("SELECT * FROM `coupons` WHERE `code`=? AND `is_active`=1 LIMIT 1", [$coupon_code], 's');

      if (mysqli_num_rows($coupon_res) == 0) {
        $result = json_encode(["status" => 'invalid_coupon', "message" => "Mã giảm giá không hợp lệ!"]);
        echo $result;
        exit;
      }

      $coupon = mysqli_fetch_assoc($coupon_res);
      $today = date("Y-m-d");

      if (($coupon['start_date'] && $today < $coupon['start_date']) || ($coupon['end_date'] && $today > $coupon['end_date'])) {
        $result = json_encode(["status" => 'invalid_coupon', "message" => "Mã giảm giá đã hết hạn!"]);
        echo $result;
        exit;
      }

      if ($coupon['usage_limit'] !== NULL && $coupon['used_count'] >= $coupon['usage_limit']) {
        $result = json_encode(["status" => 'invalid_coupon', "message" => "Mã giảm giá đã hết lượt sử dụng!"]);
        echo $result;
        exit;
      }

      if ($total < $coupon['min_booking']) {
        $result = json_encode(["status" => 'invalid_coupon', "message" => "Đơn đặt chưa đạt mức tối thiểu để dùng mã!"]);
        echo $result;
        exit;
      }

      if ($coupon['discount_type'] == 'percent') {
        $discount = round($total * ($coupon['discount_value'] / 100));
        if ($coupon['max_discount'] !== NULL && $discount > $coupon['max_discount']) {
          $discount = (int)$coupon['max_discount'];
        }
      } else {
        $discount = (int)$coupon['discount_value'];
      }

      if ($discount > $total) {
        $discount = $total;
      }
    }

    $total_after = $total - $discount;
    $deposit = (int)round($total_after * $deposit_rate);
    $remaining = $total_after - $deposit;

    // Nếu có conflict và chưa xác nhận, chỉ cảnh báo (không chặn)
    if ($has_conflict && (!isset($frm_data['confirm_conflict']) || $frm_data['confirm_conflict'] != '1')) {
      $conflict_message = 'Phòng này đã có người đặt ngày này. Bạn có thể đặt thêm vì khách có thể đặt nhầm hoặc đặt hộ. Bạn vẫn muốn tiếp tục?';
      if ($user_conflict) {
        $conflict_message = 'Phòng này trong tài khoản của bạn đã đặt hoặc bạn đã đặt ngày này. Bạn có muốn đặt thêm không?';
      }
      $status = 'date_conflict';
      $result = json_encode([
        'status' => $status,
        'message' => $conflict_message,
        'has_conflict' => true,
        'user_conflict' => $user_conflict,
        'available_rooms' => $available_rooms,
        'booked_count' => $tb_fetch['total_bookings'],
        'total_rooms' => $rq_fetch['quantity'],
        'days' => $count_days,
        'total' => $total,
        'discount' => $discount,
        'deposit' => $deposit,
        'remaining' => $remaining
      ]);
      echo $result;
      exit;
    }

    $_SESSION['room']['total'] = $total_after;
    $_SESSION['room']['discount'] = $discount;
    $_SESSION['room']['deposit'] = $deposit;
    $_SESSION['room']['remaining'] = $remaining;
    $_SESSION['room']['coupon_code'] = $coupon_code;
    $_SESSION['room']['payment'] = $deposit;
    $_SESSION['room']['available'] = true;

    $result = json_encode([
      "status" => 'available',
      "days" => $count_days,
      "total" => $total,
      "discount" => $discount,
      "deposit" => $deposit,
      "remaining" => $remaining,
      "available_rooms" => $available_rooms,
      "booked_count" => $tb_fetch['total_bookings'],
      "total_rooms" => $rq_fetch['quantity'],
      "has_conflict" => $has_conflict
    ]);
    echo $result;
  }
}
