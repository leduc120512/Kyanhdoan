<?php

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

session_start();

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    echo 0;
    exit;
}

if (isset($_POST['withdraw_refund'])) {
    $frm_data = filteration($_POST);
    $booking_id = (int)$frm_data['booking_id'];

    $booking_res = select(
        "SELECT `booking_id`, `booking_status`, `refund`, `payment_method`, `payment_status`, `deposit_amt` FROM `booking_order` WHERE `booking_id`=? AND `user_id`=? LIMIT 1",
        [$booking_id, $_SESSION['uId']],
        'ii'
    );

    if (mysqli_num_rows($booking_res) == 0) {
        echo 0;
        exit;
    }

    $booking = mysqli_fetch_assoc($booking_res);

    if ($booking['booking_status'] != 'cancelled' || (int)$booking['refund'] != 0) {
        echo 0;
        exit;
    }

    if ($booking['payment_method'] != 'online' || (int)$booking['deposit_amt'] <= 0) {
        echo 2;
        exit;
    }

    if ($booking['payment_status'] != 'chờ hoàn') {
        echo 2;
        exit;
    }

    $res = update(
        "UPDATE `booking_order` SET `refund`=1, `payment_status`='đã hoàn', `trans_status`='refunded' WHERE `booking_id`=? AND `user_id`=?",
        [$booking_id, $_SESSION['uId']],
        'ii'
    );

    echo $res ? 1 : 0;
}
