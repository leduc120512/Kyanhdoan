<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

// Lấy danh sách chờ duyệt
if (isset($_POST['get_pending'])) {
    $res = mysqli_query($con,
        "SELECT wr.*, u.name AS user_name, u.email, u.phonenum
         FROM withdrawal_requests wr
         JOIN user_cred u ON wr.user_id = u.id
         WHERE wr.status = 'pending'
         ORDER BY wr.created_at ASC"
    );

    $html = '';
    $count = 0;
    $i = 1;

    while ($row = mysqli_fetch_assoc($res)) {
        $amount  = number_format($row['amount']);
        $created = date('d-m-Y H:i', strtotime($row['created_at']));
        $type_label = $row['type'] == 'refund'
            ? "<span class='badge bg-primary'>Rút cọc</span>"
            : "<span class='badge bg-success'>Hoa hồng</span>";

        $html .= "<tr>
            <td>{$i}</td>
            <td>
                <b>{$row['user_name']}</b><br>
                <small class='text-muted'>{$row['email']}</small><br>
                <small class='text-muted'>{$row['phonenum']}</small>
            </td>
            <td>{$type_label}</td>
            <td><b>{$amount} VND</b></td>
            <td>{$row['bank_name']}</td>
            <td>{$row['bank_account']}</td>
            <td>{$row['account_name']}</td>
            <td>{$created}</td>
            <td>
                <button onclick='approve_withdrawal({$row['id']})' class='btn btn-success btn-sm me-1 shadow-none'>
                    <i class='bi bi-check-lg'></i> Duyệt
                </button>
                <button onclick='open_reject({$row['id']})' class='btn btn-danger btn-sm shadow-none'>
                    <i class='bi bi-x-lg'></i> Từ chối
                </button>
            </td>
        </tr>";
        $i++;
        $count++;
    }

    if ($count == 0) {
        $html = "<tr><td colspan='9' class='text-center text-muted'>Không có yêu cầu nào.</td></tr>";
    }

    echo json_encode(['html' => $html, 'count' => $count]);
    exit;
}

// Lấy danh sách đã xử lý
if (isset($_POST['get_done'])) {
    $res = mysqli_query($con,
        "SELECT wr.*, u.name AS user_name
         FROM withdrawal_requests wr
         JOIN user_cred u ON wr.user_id = u.id
         WHERE wr.status IN ('approved','rejected')
         ORDER BY wr.updated_at DESC LIMIT 50"
    );

    $html = '';
    $i = 1;

    while ($row = mysqli_fetch_assoc($res)) {
        $amount  = number_format($row['amount']);
        $updated = $row['updated_at'] ? date('d-m-Y H:i', strtotime($row['updated_at'])) : '-';
        $type_label = $row['type'] == 'refund'
            ? "<span class='badge bg-primary'>Rút cọc</span>"
            : "<span class='badge bg-success'>Hoa hồng</span>";

        if ($row['status'] == 'approved') {
            $status_badge = "<span class='badge bg-success'>Đã duyệt</span>";
        } else {
            $status_badge = "<span class='badge bg-danger'>Từ chối</span>";
        }

        $note = htmlspecialchars($row['admin_note'] ?? '');

        $html .= "<tr>
            <td>{$i}</td>
            <td><b>{$row['user_name']}</b></td>
            <td>{$type_label}</td>
            <td><b>{$amount} VND</b></td>
            <td>{$row['bank_name']}<br><small>{$row['bank_account']}</small></td>
            <td>{$row['account_name']}</td>
            <td>{$status_badge}</td>
            <td><small class='text-muted'>{$note}</small></td>
            <td>{$updated}</td>
        </tr>";
        $i++;
    }

    if ($i == 1) {
        $html = "<tr><td colspan='9' class='text-center text-muted'>Chưa có dữ liệu.</td></tr>";
    }

    echo $html;
    exit;
}

// Duyệt yêu cầu
if (isset($_POST['approve_withdrawal'])) {
    $id = (int)$_POST['id'];

    // Lấy thông tin yêu cầu
    $wr_res = select("SELECT * FROM withdrawal_requests WHERE id=? AND status='pending' LIMIT 1", [$id], 'i');
    if (mysqli_num_rows($wr_res) == 0) { echo 0; exit; }
    $wr = mysqli_fetch_assoc($wr_res);

    // Cập nhật trạng thái yêu cầu
    $upd = update(
        "UPDATE withdrawal_requests SET status='approved', updated_at=NOW() WHERE id=?",
        [$id], 'i'
    );

    if (!$upd) { echo 0; exit; }

    // Nếu là rút cọc: cập nhật booking_order
    if ($wr['type'] == 'refund') {
        update(
            "UPDATE booking_order SET refund=1, payment_status='đã hoàn', trans_status='refunded' WHERE booking_id=?",
            [$wr['ref_id']], 'i'
        );
    }

    // Nếu là hoa hồng: cập nhật commission_status
    if ($wr['type'] == 'commission') {
        update(
            "UPDATE booking_order SET commission_status='paid' WHERE referral_id=? AND commission_status='pending'",
            [$wr['ref_id']], 'i'
        );
    }

    echo 1;
    exit;
}

// Từ chối yêu cầu
if (isset($_POST['reject_withdrawal'])) {
    $id   = (int)$_POST['id'];
    $note = trim(htmlspecialchars($_POST['note'] ?? ''));

    $res = update(
        "UPDATE withdrawal_requests SET status='rejected', admin_note=?, updated_at=NOW() WHERE id=? AND status='pending'",
        [$note, $id], 'si'
    );

    echo $res ? 1 : 0;
    exit;
}
