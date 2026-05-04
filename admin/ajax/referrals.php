<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if (isset($_POST['get_referrals'])) {
    $q = "SELECT ru.*, u.name, u.email, u.phonenum
        FROM referral_users ru
        JOIN user_cred u ON ru.user_id = u.id
        ORDER BY ru.id DESC";

    $res = mysqli_query($con, $q);
    $data = "";
    $i = 1;

    while ($row = mysqli_fetch_assoc($res)) {
        $commission_base = isset($row['commission_base']) ? $row['commission_base'] : 'total';
        $base_label = 'Tổng tiền';

        if ($commission_base == 'deposit') {
            $base_label = 'Tiền cọc';
        } else if ($commission_base == 'remaining') {
            $base_label = 'Còn lại';
        }

        $status_badge = "<span class='badge bg-warning'>Chờ duyệt</span>";

        if ($row['status'] == 'approved') {
            $status_badge = "<span class='badge bg-success'>Đã duyệt</span>";
        } else if ($row['status'] == 'rejected') {
            $status_badge = "<span class='badge bg-danger'>Từ chối</span>";
        }

        $actions = "-";
        $base_cell = "<span class='badge bg-light text-dark'>$base_label</span>";
        $pct_cell = "<span class='badge bg-info text-dark'>{$row['commission_pct']}%</span>";

        if ($row['status'] == 'pending') {
            $base_cell = "<select id='base_$row[id]' class='form-select form-select-sm'>
                                <option value='total' " . ($commission_base == 'total' ? 'selected' : '') . ">Tổng tiền</option>
                                <option value='deposit' " . ($commission_base == 'deposit' ? 'selected' : '') . ">Tiền cọc</option>
                                <option value='remaining' " . ($commission_base == 'remaining' ? 'selected' : '') . ">Còn lại</option>
                            </select>";
            $pct_cell = "<input id='pct_$row[id]' type='number' min='0' max='100' step='0.1' value='$row[commission_pct]' class='form-control form-control-sm'>";
            $actions = "<button onclick='approve_referral($row[id])' class='btn btn-primary btn-sm me-1'>Duyệt</button>
                                    <button onclick='reject_referral($row[id])' class='btn btn-danger btn-sm'>Từ chối</button>";
        }

        $data .= "
      <tr>
        <td>$i</td>
        <td>$row[name]</td>
        <td>$row[email]</td>
        <td>$row[phonenum]</td>
        <td>$row[referral_code]</td>
                <td>$pct_cell</td>
                <td>$base_cell</td>
        <td>$status_badge</td>
        <td>$row[datentime]</td>
        <td>$actions</td>
      </tr>
    ";

        $i++;
    }

    echo $data;
    exit;
}

if (isset($_POST['approve_referral'])) {
    $id = (int)$_POST['id'];
    $commission_pct = null;
    if (isset($_POST['commission_pct']) && $_POST['commission_pct'] !== '') {
        $commission_pct = (float)$_POST['commission_pct'];
    }
    if ($commission_pct === null) {
        $settings_row = mysqli_fetch_assoc(select("SELECT `referral_commission_default` FROM `settings` WHERE `sr_no`=?", [1], 'i'));
        $commission_pct = isset($settings_row['referral_commission_default']) ? (float)$settings_row['referral_commission_default'] : 5.00;
    }
    if ($commission_pct < 0) {
        $commission_pct = 0;
    }
    if ($commission_pct > 100) {
        $commission_pct = 100;
    }

    $commission_base = isset($_POST['commission_base']) ? $_POST['commission_base'] : 'total';
    if (!in_array($commission_base, ['total', 'deposit', 'remaining'], true)) {
        $commission_base = 'total';
    }

    $q = "UPDATE referral_users SET status='approved', approved_at=?, commission_pct=?, commission_base=? WHERE id=?";
    $res = update($q, [date('Y-m-d H:i:s'), $commission_pct, $commission_base, $id], 'sdsi');
    echo $res ? 1 : 0;
    exit;
}

if (isset($_POST['reject_referral'])) {
    $id = (int)$_POST['id'];
    $q = "UPDATE referral_users SET status='rejected' WHERE id=?";
    $res = update($q, [$id], 'i');
    echo $res ? 1 : 0;
    exit;
}
