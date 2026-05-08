<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

// LẤY DANH SÁCH ĐƠN
if (isset($_POST['get_bookings'])) {
    $q = "SELECT bo.*, r.name AS room_name, u.name AS user_name, u.phonenum
          FROM booking_order bo
          JOIN rooms r ON bo.room_id = r.id
          JOIN user_cred u ON bo.user_id = u.id
          ORDER BY bo.booking_id DESC";

    $res = mysqli_query($con, $q);

    $data = "";
    $i = 1;

    while ($row = mysqli_fetch_assoc($res)) {
        // Status
        $status = "<span class='badge bg-warning'>Chờ xác nhận cọc</span>";

        if ($row['payment_status'] == "đã cọc") {
            $status = "<span class='badge bg-success'>Đã cọc</span>";
        } else if ($row['payment_status'] == "đã thanh toán" || $row['payment_status'] == "Success") {
            $status = "<span class='badge bg-success'>Đã thanh toán</span>";
        } else if ($row['payment_status'] == "chờ hoàn") {
            $status = "<span class='badge bg-warning text-dark'>Chờ hoàn</span>";
        } else if ($row['payment_status'] == "đã hoàn") {
            $status = "<span class='badge bg-success'>Đã hoàn</span>";
        } else if ($row['payment_status'] == "đã hủy") {
            $status = "<span class='badge bg-danger'>Đã hủy</span>";
        } else if ($row['payment_status'] == "cọc tại quầy") {
            $status = "<span class='badge bg-info text-dark'>Cọc tại quầy</span>";
        }

        // Trạng thái xác nhận chi tiết
        $status_detail = "";
        $deposit_is_confirmed = ($row['deposit_confirmed'] == 1 || $row['payment_status'] == 'cọc tại quầy');

        // Xác nhận cọc
        if ($deposit_is_confirmed) {
            $status_detail .= "<span class='badge bg-info me-1'>✓ Cọc xác nhận</span>";
        } else if ($row['payment_method'] == "online") {
            $status_detail .= "<span class='badge bg-warning me-1'>⏳ Cọc chưa xác nhận</span>";
        }

        // Xác nhận thanh toán hết
        if ($row['full_payment_confirmed'] == 1) {
            $status_detail .= "<span class='badge bg-info me-1'>✓ Thanh toán xác nhận</span>";
        } else if ($row['payment_status'] == "đã thanh toán") {
            $status_detail .= "<span class='badge bg-warning me-1'>⏳ Thanh toán chưa xác nhận</span>";
        }

        // Xác nhận khách đến
        if ($row['guest_arrival_confirmed'] == 1) {
            $status_detail .= "<span class='badge bg-success me-1'>✓ Khách xác nhận đến</span>";
        }

        // Button
        $btn = "";

        // Nếu khách đã hủy — không cho thao tác gì
        if ($row['booking_status'] == 'cancelled') {
            $btn = "<span class='badge bg-danger px-3 py-2'>🚫 Khách đã hủy</span>";
        } else {
            // Thanh toán QR (online): cần xác nhận cọc trước
            if ($row['payment_method'] == "online" && !$deposit_is_confirmed) {
                $btn .= "<button onclick='confirm_deposit($row[booking_id])' class='btn btn-primary btn-sm shadow-none me-1'>
                    Xác nhận cọc
                    </button>";
            }

            // Đã cọc thì cho xác nhận thanh toán hết
            if ($deposit_is_confirmed && $row['full_payment_confirmed'] == 0) {
                $btn .= "<button onclick='confirm_full_payment($row[booking_id])' class='btn btn-success btn-sm shadow-none me-1'>
                    Xác nhận thanh toán
                    </button>";
            }

            // Nếu đã thanh toán hết nhưng chưa xác nhận khách đến
            if ($row['full_payment_confirmed'] == 1 && $row['guest_arrival_confirmed'] == 0) {
                $btn .= "<button onclick='confirm_guest_arrival($row[booking_id])' class='btn btn-info btn-sm shadow-none me-1'>
                    Xác nhận khách đến
                    </button>";
            }
        }

        $deposit_amt = (int)$row['deposit_amt'];
        $total_amt = (int)$row['total_amt'];
        $remaining_amt = (int)$row['remaining_amt'];

        if ($total_amt == 0 && $row['trans_amt']) {
            $total_amt = (int)$row['trans_amt'];
        }

        $deposit_paid = $deposit_amt ?: (int)$row['trans_amt'];
        $deposit_pct = 0;
        if ($total_amt > 0 && $deposit_paid > 0) {
            $deposit_pct = round(($deposit_paid / $total_amt) * 100, 1);
        }

        $data .= "
            <tr>
                <td>$i</td>
                <td>$row[user_name]</td>
                <td>$row[phonenum]</td>
                <td>$row[room_name]</td>
                <td>$row[check_in]</td>
                <td>$row[check_out]</td>
                <td>Cọc: " . number_format($deposit_paid) . " VND ($deposit_pct%)<br>Tổng: " . number_format($total_amt) . " VND</td>
                <td>" . number_format($remaining_amt) . " VND</td>
                <td>$status<br>$status_detail</td>
                <td>$btn</td>
            </tr>
        ";

        $i++;
    }

    echo $data;
    exit;
}



// ADMIN XÁC NHẬN CỌC
if (isset($_POST['confirm_deposit'])) {
    $id = $_POST['id'];

    $q = "UPDATE booking_order 
                        SET deposit_confirmed=1, deposit_confirmed_at=NOW(),
                                payment_status=CASE
                                    WHEN payment_method='online' AND payment_status='đang chờ xử lý' THEN 'đã cọc'
                                    ELSE payment_status
                                END
            WHERE booking_id=?";

    if (update($q, [$id], 'i')) {
        echo 1;
    } else {
        echo 0;
    }
}

// ADMIN XÁC NHẬN THANH TOÁN HẾT
if (isset($_POST['confirm_full_payment'])) {
    $id = $_POST['id'];

    $q = "UPDATE booking_order 
            SET full_payment_confirmed=1, full_payment_confirmed_at=NOW() 
            WHERE booking_id=?";

    if (update($q, [$id], 'i')) {
        echo 1;
    } else {
        echo 0;
    }
}

// ADMIN XÁC NHẬN KHÁCH ĐẾN
if (isset($_POST['confirm_guest_arrival'])) {
    $id = $_POST['id'];

    $q = "UPDATE booking_order 
            SET guest_arrival_confirmed=1, guest_arrival_confirmed_at=NOW(), booking_status='booked'
            WHERE booking_id=?";

    if (update($q, [$id], 'i')) {
        echo 1;
    } else {
        echo 0;
    }
}

// ADMIN XÁC NHẬN THANH TOÁN (OLD - GIỮ LẠI CHO TƯƠNG THÍCH)
if (isset($_POST['confirm_booking'])) {
    $id = $_POST['id'];

    $q = "UPDATE booking_order 
            SET payment_status='đã cọc' 
            WHERE booking_id=?";

    if (update($q, [$id], 'i')) {
        echo 1;
    } else {
        echo 0;
    }
}
