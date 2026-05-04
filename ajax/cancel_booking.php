<?php

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');


session_start();


if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
  redirect('index.php');
}

if (isset($_POST['cancel_booking'])) {
  $frm_data = filteration($_POST);

  $booking_res = select(
    "SELECT `booking_status`, `arrival`, `datentime`, `payment_method`, `payment_status`, `deposit_amt` FROM `booking_order` WHERE `booking_id`=? AND `user_id`=? LIMIT 1",
    [$frm_data['id'], $_SESSION['uId']],
    'ii'
  );

  if (mysqli_num_rows($booking_res) == 0) {
    echo 0;
    exit;
  }

  $booking = mysqli_fetch_assoc($booking_res);

  if ($booking['booking_status'] != 'booked' || $booking['arrival'] == 1) {
    echo 0;
    exit;
  }

  $booked_time = new DateTime($booking['datentime']);
  $now = new DateTime();
  $hours_diff = ($now->getTimestamp() - $booked_time->getTimestamp()) / 3600;

  if ($hours_diff > 24) {
    echo 2;
    exit;
  }

  $refund_status = 0;
  $payment_status = 'đã hủy';
  $can_refund = ($booking['payment_method'] == 'online'
    && (int)$booking['deposit_amt'] > 0
    && in_array($booking['payment_status'], ['đã cọc', 'đã thanh toán', 'Success', 'paid'], true));

  if ($can_refund) {
    $payment_status = 'chờ hoàn';
  }

  $query = "UPDATE `booking_order` SET `booking_status`=?, `refund`=?, `cancelled_at`=?, `cancel_fee`=?, `payment_status`=?
      WHERE `booking_id`=? AND `user_id`=?";

  $values = ['cancelled', $refund_status, date('Y-m-d H:i:s'), 0, $payment_status, $frm_data['id'], $_SESSION['uId']];

  $result = update($query, $values, 'sisssii');

  echo $result;
}
