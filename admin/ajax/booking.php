<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();


// ================================
// 1. LOAD DANH SÁCH BOOKING
// ================================
if (isset($_POST['get_bookings'])) {
    // JOIN booking_order + booking_details
    $q = "
        SELECT 
            bo.booking_id,
            bo.order_id,
            bo.check_in,
            bo.check_out,
            bo.payment_method,
            bo.payment_status,
            bo.trans_amt,
            bo.total_amt,
            bo.deposit_amt,
            bd.user_name,
            bd.phonenum
        FROM booking_order bo
        INNER JOIN booking_details bd
        ON bo.booking_id = bd.booking_id
        ORDER BY bo.booking_id DESC
    ";

    $res = mysqli_query($con, $q);
    $data = "";
    $i = 1;

    while ($row = mysqli_fetch_assoc($res)) {
        // Badge trạng thái thanh toán
        if ($row['payment_status'] == "đang chờ xử lý") {
            $status = "<span class='badge bg-warning'>Chờ xác nhận cọc</span>";
        } else if ($row['payment_status'] == "đã cọc") {
            $status = "<span class='badge bg-success'>Đã cọc</span>";
        } else if ($row['payment_status'] == "cọc tại quầy") {
            $status = "<span class='badge bg-info text-dark'>Cọc tại quầy</span>";
        } else if ($row['payment_status'] == "đã thanh toán" || $row['payment_status'] == "Success") {
            $status = "<span class='badge bg-success'>Đã thanh toán</span>";
        } else if ($row['payment_status'] == "đã hủy") {
            $status = "<span class='badge bg-danger'>Đã hủy</span>";
        } else {
            $status = "<span class='badge bg-secondary'>Chưa xác định</span>";
        }

        // Nút xác nhận (chỉ xuất hiện nếu thanh toán online và đang chờ xử lý)
        $btn = "";
        if ($row['payment_method'] == "online" && $row['payment_status'] == "đang chờ xử lý") {
            $btn = "<button onclick='confirm_payment($row[booking_id])'
                     class='btn btn-primary btn-sm shadow-none'>
                        Xác nhận
                    </button>";
        }

        // Render hàng của bảng
        $total_amt = (int)$row['total_amt'];
        if ($total_amt == 0 && $row['trans_amt']) {
            $total_amt = (int)$row['trans_amt'];
        }

        $data .= "
            <tr>
                <td>$i</td>
                <td>$row[user_name]</td>
                <td>$row[phonenum]</td>
                <td>$row[check_in]</td>
                <td>$row[check_out]</td>
                <td>$row[payment_method]</td>
                <td>Cọc: " . number_format($row['deposit_amt'] ?: $row['trans_amt']) . " VND<br>Tổng: " . number_format($total_amt) . " VND</td>
                <td>$status</td>
                <td>$btn</td>
            </tr>
        ";

        $i++;
    }

    echo $data;
}



// ================================
// 2. XÁC NHẬN THANH TOÁN
// ================================
if (isset($_POST['confirm_payment'])) {
    $id = $_POST['id'];

    $q = "UPDATE booking_order 
          SET payment_status='đã cọc'
          WHERE booking_id=?";

    $res = update($q, [$id], 'i');

    echo $res ? 1 : 0;
}
