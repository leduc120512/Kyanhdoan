<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

session_start();

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
  redirect('index.php');
}

if (!isset($_SESSION['uId']) || !is_numeric($_SESSION['uId'])) {
  session_unset();
  session_destroy();
  redirect('index.php');
}

if (isset($_POST['pay_now'])) {
  $ORDER_ID = 'ORD_' . $_SESSION['uId'] . random_int(11111, 9999999);
  $CUST_ID = (int)$_SESSION['uId'];
  $room_id = isset($_SESSION['room']['id']) ? (int)$_SESSION['room']['id'] : 0;
  $deposit_rate = 0.30;

  $frm = filteration($_POST);
  $checkin = $frm['checkin'];
  $checkout = $frm['checkout'];

  $checkin_date = new DateTime($checkin);
  $checkout_date = new DateTime($checkout);
  $today_date = new DateTime(date("Y-m-d"));

  if ($checkin_date == $checkout_date || $checkout_date < $checkin_date || $checkin_date < $today_date) {
    redirect('rooms.php');
  }

  if ($room_id <= 0) {
    redirect('rooms.php');
  }

  $user_check = select("SELECT `id` FROM `user_cred` WHERE `id`=? LIMIT 1", [$CUST_ID], 'i');
  if (mysqli_num_rows($user_check) == 0) {
    session_unset();
    session_destroy();
    redirect('index.php');
  }

  // check booking availability
  $tb_query = "SELECT COUNT(*) AS `total_bookings` FROM `booking_order`
      WHERE booking_status=? AND room_id=?
      AND check_out > ? AND check_in < ?";

  $values = ['booked', $room_id, $checkin, $checkout];
  $tb_fetch = mysqli_fetch_assoc(select($tb_query, $values, 'siss'));

  $rq_result = select("SELECT `quantity` FROM `rooms` WHERE `id`=?", [$room_id], 'i');
  $rq_fetch = mysqli_fetch_assoc($rq_result);

  if (($rq_fetch['quantity'] - $tb_fetch['total_bookings']) == 0) {
    redirect('rooms.php');
  }

  $count_days = date_diff($checkin_date, $checkout_date)->days;
  $total = $_SESSION['room']['price'] * $count_days;

  // apply coupon
  $coupon_code = isset($frm['coupon_code']) ? trim($frm['coupon_code']) : '';
  $discount = 0;

  if ($coupon_code !== '') {
    $coupon_res = select("SELECT * FROM `coupons` WHERE `code`=? AND `is_active`=1 LIMIT 1", [$coupon_code], 's');

    if (mysqli_num_rows($coupon_res) == 0) {
      $_SESSION['coupon_error'] = 'Mã giảm giá không hợp lệ!';
      redirect('confirm_booking.php?id=' . $_SESSION['room']['id']);
    }

    $coupon = mysqli_fetch_assoc($coupon_res);
    $today = date("Y-m-d");

    if (($coupon['start_date'] && $today < $coupon['start_date']) || ($coupon['end_date'] && $today > $coupon['end_date'])) {
      $_SESSION['coupon_error'] = 'Mã giảm giá đã hết hạn!';
      redirect('confirm_booking.php?id=' . $_SESSION['room']['id']);
    }

    if ($coupon['usage_limit'] !== NULL && $coupon['used_count'] >= $coupon['usage_limit']) {
      $_SESSION['coupon_error'] = 'Mã giảm giá đã hết lượt sử dụng!';
      redirect('confirm_booking.php?id=' . $_SESSION['room']['id']);
    }

    if ($total < $coupon['min_booking']) {
      $_SESSION['coupon_error'] = 'Đơn đặt chưa đạt mức tối thiểu để dùng mã!';
      redirect('confirm_booking.php?id=' . $_SESSION['room']['id']);
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

  $payment_method = $frm['payment_method'];   // offline | online
  $payment_status = ($payment_method == 'online')
    ? 'đang chờ xử lý'         // Admin phải xác nhận cọc
    : 'cọc tại quầy';          // Cọc tiền mặt

  // referral
  $referral_code = isset($frm['referral_code']) ? trim($frm['referral_code']) : '';
  $referral_id = 0;
  $commission_pct = 0;
  $commission_amt = 0;
  $commission_base = 'total';
  $commission_base_amt = $total_after;

  if ($referral_code !== '') {
    $ref_res = select("SELECT * FROM `referral_users` WHERE `referral_code`=? AND `status`='approved' LIMIT 1", [$referral_code], 's');
    if (mysqli_num_rows($ref_res) > 0) {
      $ref_row = mysqli_fetch_assoc($ref_res);
      $referral_id = (int)$ref_row['id'];
      $commission_pct = (float)$ref_row['commission_pct'];
      $commission_base = isset($ref_row['commission_base']) ? $ref_row['commission_base'] : 'total';

      if ($commission_base === 'deposit') {
        $commission_base_amt = $deposit;
      } else if ($commission_base === 'remaining') {
        $commission_base_amt = $remaining;
      } else {
        $commission_base = 'total';
        $commission_base_amt = $total_after;
      }

      $commission_amt = (int)round($commission_base_amt * ($commission_pct / 100));
    }
  }

  // INSERT booking_order
  $q1 = "INSERT INTO booking_order
    (`user_id`, `room_id`, `check_in`, `check_out`, `order_id`, `booking_status`,
     `arrival`, `refund`, `trans_amt`, `payment_method`, `payment_status`,
     `total_amt`, `discount_amt`, `coupon_code`, `deposit_amt`, `remaining_amt`,
     `referral_id`, `commission_pct`, `commission_amt`, `commission_status`)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

  insert($q1, [
    $CUST_ID,
    $room_id,
    $checkin,
    $checkout,
    $ORDER_ID,
    'booked',
    0,
    0,
    $deposit,
    $payment_method,
    $payment_status,
    $total_after,
    $discount,
    $coupon_code,
    $deposit,
    $remaining,
    $referral_id,
    $commission_pct,
    $commission_amt,
    'pending'
  ], 'iissssiiissiisiiidis');

  $booking_id = mysqli_insert_id($con);

  // INSERT booking_details
  $q2 = "INSERT INTO booking_details
    (`booking_id`, `room_name`, `price`, `total_pay`, `user_name`, `phonenum`, `address`)
    VALUES (?,?,?,?,?,?,?)";

  insert($q2, [
    $booking_id,
    $_SESSION['room']['name'],
    $_SESSION['room']['price'],
    $total_after,
    $frm['name'],
    $frm['phonenum'],
    $frm['address']
  ], 'issssss');

  if ($coupon_code !== '') {
    update("UPDATE `coupons` SET `used_count`=`used_count`+1 WHERE `code`=?", [$coupon_code], 's');
  }

  // Nếu là thanh toán online → chuyển sang trang QR
  if ($payment_method == "online") {
    $_SESSION['last_order_id'] = $ORDER_ID;
    redirect("qr_payment.php");
    exit;
  }
}

redirect('bookings.php');
