<?php

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

session_start();

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    echo 0;
    exit;
}

if (isset($_POST['withdraw_commission'])) {
    $ref_id  = (int)$_POST['ref_id'];
    $user_id = (int)$_SESSION['uId'];

    // Kiểm tra ref_id thuộc về user này
    $ref_res = select(
        "SELECT `id` FROM `referral_users` WHERE `id`=? AND `user_id`=? AND `status`='approved' LIMIT 1",
        [$ref_id, $user_id],
        'ii'
    );

    if (mysqli_num_rows($ref_res) == 0) {
        echo 0;
        exit;
    }

    // Kiểm tra có hoa hồng chờ không
    $pending_res = select(
        "SELECT COALESCE(SUM(commission_amt), 0) AS pending FROM `booking_order`
         WHERE `referral_id`=? AND `commission_status`='pending'",
        [$ref_id],
        'i'
    );
    $pending_row = mysqli_fetch_assoc($pending_res);
    $pending_amt = (float)$pending_row['pending'];

    if ($pending_amt <= 0) {
        echo 2;
        exit;
    }

    // Cập nhật tất cả hoa hồng pending -> paid
    $upd = update(
        "UPDATE `booking_order` SET `commission_status`='paid'
         WHERE `referral_id`=? AND `commission_status`='pending'",
        [$ref_id],
        'i'
    );

    echo $upd ? 1 : 0;
}
