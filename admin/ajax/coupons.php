<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if (isset($_POST['add_coupon'])) {
    $frm = filteration($_POST);

    $code = strtoupper(trim($frm['code']));
    $discount_type = $frm['discount_type'] == 'fixed' ? 'fixed' : 'percent';
    $discount_value = (int)$frm['discount_value'];
    $max_discount = $frm['max_discount'] !== '' ? (int)$frm['max_discount'] : NULL;
    $min_booking = $frm['min_booking'] !== '' ? (int)$frm['min_booking'] : 0;
    $start_date = $frm['start_date'] !== '' ? $frm['start_date'] : NULL;
    $end_date = $frm['end_date'] !== '' ? $frm['end_date'] : NULL;
    $usage_limit = $frm['usage_limit'] !== '' ? (int)$frm['usage_limit'] : NULL;
    $is_active = isset($frm['is_active']) ? (int)$frm['is_active'] : 1;

    $exists = select("SELECT `id` FROM `coupons` WHERE `code`=? LIMIT 1", [$code], 's');
    if (mysqli_num_rows($exists) > 0) {
        echo 'exists';
        exit;
    }

    $q = "INSERT INTO `coupons`
    (`code`, `discount_type`, `discount_value`, `max_discount`, `min_booking`, `start_date`, `end_date`, `usage_limit`, `is_active`)
    VALUES (?,?,?,?,?,?,?,?,?)";

    $res = insert($q, [
        $code,
        $discount_type,
        $discount_value,
        $max_discount,
        $min_booking,
        $start_date,
        $end_date,
        $usage_limit,
        $is_active
    ], 'ssiiissii');

    echo $res ? 1 : 0;
    exit;
}

if (isset($_POST['get_coupons'])) {
    $res = mysqli_query($con, "SELECT * FROM `coupons` ORDER BY `id` DESC");
    $data = "";
    $i = 1;

    while ($row = mysqli_fetch_assoc($res)) {
        $status = $row['is_active'] ? "<span class='badge bg-success'>Đang bật</span>" : "<span class='badge bg-secondary'>Đã tắt</span>";
        $limit = $row['usage_limit'] === NULL ? 'Không giới hạn' : $row['usage_limit'];
        $range = '-';
        if ($row['start_date'] || $row['end_date']) {
            $range = ($row['start_date'] ?: '...') . ' - ' . ($row['end_date'] ?: '...');
        }

        $toggle_btn = $row['is_active'] ? "<button onclick='toggle_coupon($row[id],0)' class='btn btn-sm btn-warning'>Tắt</button>" :
            "<button onclick='toggle_coupon($row[id],1)' class='btn btn-sm btn-success'>Bật</button>";

        $data .= "
      <tr>
        <td>$i</td>
        <td>$row[code]</td>
        <td>$row[discount_type]</td>
        <td>$row[discount_value]</td>
        <td>$limit</td>
        <td>$row[used_count]</td>
        <td>$range</td>
        <td>$status</td>
        <td>
          $toggle_btn
          <button onclick='delete_coupon($row[id])' class='btn btn-sm btn-danger ms-1'>Xóa</button>
        </td>
      </tr>
    ";

        $i++;
    }

    echo $data;
    exit;
}

if (isset($_POST['toggle_coupon'])) {
    $id = (int)$_POST['id'];
    $value = (int)$_POST['value'];

    $res = update("UPDATE `coupons` SET `is_active`=? WHERE `id`=?", [$value, $id], 'ii');
    echo $res ? 1 : 0;
    exit;
}

if (isset($_POST['delete_coupon'])) {
    $id = (int)$_POST['id'];
    $res = delete("DELETE FROM `coupons` WHERE `id`=?", [$id], 'i');
    echo $res ? 1 : 0;
    exit;
}
