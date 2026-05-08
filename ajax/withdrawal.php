<?php

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

session_start();

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    echo 0;
    exit;
}

$user_id = (int)$_SESSION['uId'];

if (isset($_POST['submit_withdrawal'])) {
    $type        = isset($_POST['type']) ? $_POST['type'] : '';
    $ref_id      = (int)($_POST['ref_id'] ?? 0);
    $amount      = (float)($_POST['amount'] ?? 0);
    $bank_name   = trim(htmlspecialchars($_POST['bank_name'] ?? ''));
    $bank_account = trim(htmlspecialchars($_POST['bank_account'] ?? ''));
    $account_name = trim(htmlspecialchars($_POST['account_name'] ?? ''));

    // Validate
    if (!in_array($type, ['refund', 'commission']) || $ref_id <= 0 || $amount <= 0
        || $bank_name == '' || $bank_account == '' || $account_name == '') {
        echo 0;
        exit;
    }

    if ($type == 'refund') {
        // Kiểm tra booking thuộc user, đủ điều kiện
        $chk = select(
            "SELECT booking_id FROM booking_order
             WHERE booking_id=? AND user_id=? AND booking_status='cancelled'
             AND payment_method='online' AND deposit_amt>0 AND refund=0
             AND payment_status='chờ hoàn' LIMIT 1",
            [$ref_id, $user_id], 'ii'
        );
        if (mysqli_num_rows($chk) == 0) { echo 0; exit; }

        // Kiểm tra chưa có yêu cầu pending/approved
        $dup = select(
            "SELECT id FROM withdrawal_requests
             WHERE user_id=? AND type='refund' AND ref_id=? AND status IN ('pending','approved') LIMIT 1",
            [$user_id, $ref_id], 'ii'
        );
        if (mysqli_num_rows($dup) > 0) { echo 2; exit; }

    } else {
        // commission: kiểm tra referral_users thuộc user và approved
        $chk = select(
            "SELECT id FROM referral_users WHERE id=? AND user_id=? AND status='approved' LIMIT 1",
            [$ref_id, $user_id], 'ii'
        );
        if (mysqli_num_rows($chk) == 0) { echo 0; exit; }

        // Kiểm tra chưa có yêu cầu pending
        $dup = select(
            "SELECT id FROM withdrawal_requests
             WHERE user_id=? AND type='commission' AND status='pending' LIMIT 1",
            [$user_id], 'i'
        );
        if (mysqli_num_rows($dup) > 0) { echo 2; exit; }
    }

    $res = insert(
        "INSERT INTO withdrawal_requests (user_id, type, ref_id, amount, bank_name, bank_account, account_name)
         VALUES (?,?,?,?,?,?,?)",
        [$user_id, $type, $ref_id, $amount, $bank_name, $bank_account, $account_name],
        'isiisss'
    );

    echo $res ? 1 : 0;
}
