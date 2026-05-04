<?php
require('inc/links.php');
require('inc/header.php');

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('index.php');
}

$user_id = (int)$_SESSION['uId'];

$pending_res = select(
    "SELECT bo.*, bd.room_name FROM `booking_order` bo
    INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
    WHERE bo.user_id=? AND bo.booking_status='cancelled'
    AND bo.payment_method='online' AND bo.deposit_amt>0 AND bo.refund=0
    AND bo.payment_status='chờ hoàn'
    ORDER BY bo.booking_id DESC",
    [$user_id],
    'i'
);

$history_res = select(
    "SELECT bo.*, bd.room_name FROM `booking_order` bo
    INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
    WHERE bo.user_id=? AND bo.booking_status='cancelled'
    AND bo.payment_method='online' AND bo.deposit_amt>0 AND bo.refund=1
    ORDER BY bo.booking_id DESC LIMIT 10",
    [$user_id],
    'i'
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings_r['site_title'] ?> - Rút tiền cọc</title>
</head>

<body class="bg-light">

    <div class="container">
        <div class="row">

            <div class="col-12 my-5 px-4">
                <h2 class="fw-bold h-font">Rút tiền cọc</h2>
                <div class="breadcrumb-mini">
                    <a href="index.php" class="text-secondary text-decoration-none">Trang chủ</a>
                    <span class="text-secondary"> > </span>
                    <a href="#" class="text-secondary text-decoration-none">Rút tiền cọc</a>
                </div>
            </div>

            <div class="col-12 px-4 mb-4">
                <div class="bg-white p-3 p-md-4 booking-card">
                    <h5 class="fw-bold mb-2">Lưu ý</h5>
                    <p class="text-muted mb-0">
                        Hoàn tiền được xử lý tự động trong hệ thống (không kết nối ngân hàng thật).
                        Sau khi rút, trạng thái sẽ chuyển sang <b>Đã hoàn</b>.
                    </p>
                </div>
            </div>

            <div class="col-12 px-4 mb-5">
                <div class="bg-white p-3 p-md-4 booking-card">
                    <h5 class="fw-bold mb-3">Đơn chờ hoàn</h5>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Mã đặt phòng</th>
                                    <th>Phòng</th>
                                    <th>Tiền cọc</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($pending_res) == 0) {
                                    echo "<tr><td colspan='5' class='text-center text-muted'>Không có đơn nào chờ hoàn.</td></tr>";
                                } else {
                                    while ($row = mysqli_fetch_assoc($pending_res)) {
                                        $amount = number_format($row['deposit_amt']);
                                        $status = $row['payment_status'] ? $row['payment_status'] : 'chờ hoàn';

                                        echo "<tr>
                      <td>$row[order_id]</td>
                      <td>$row[room_name]</td>
                      <td><b>{$amount} VND</b></td>
                      <td><span class='badge bg-warning text-dark'>{$status}</span></td>
                      <td>
                        <button class='btn btn-primary btn-sm' onclick='withdraw_refund($row[booking_id])'>Rút tiền</button>
                      </td>
                    </tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 px-4 mb-5">
                <div class="bg-white p-3 p-md-4 booking-card">
                    <h5 class="fw-bold mb-3">Lịch sử đã hoàn</h5>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Mã đặt phòng</th>
                                    <th>Phòng</th>
                                    <th>Tiền cọc</th>
                                    <th>Ngày hủy</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($history_res) == 0) {
                                    echo "<tr><td colspan='4' class='text-center text-muted'>Chưa có lịch sử hoàn tiền.</td></tr>";
                                } else {
                                    while ($row = mysqli_fetch_assoc($history_res)) {
                                        $amount = number_format($row['deposit_amt']);
                                        $cancelled_at = $row['cancelled_at'] ? date('d-m-Y H:i', strtotime($row['cancelled_at'])) : '-';

                                        echo "<tr>
                      <td>$row[order_id]</td>
                      <td>$row[room_name]</td>
                      <td><b>{$amount} VND</b></td>
                      <td>$cancelled_at</td>
                    </tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php require('inc/footer.php'); ?>

    <script>
        function withdraw_refund(id) {
            if (!confirm('Xác nhận rút tiền cọc?')) {
                return;
            }

            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'ajax/refund_withdraw.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (this.responseText == 1) {
                    alert('success', 'Rút tiền thành công!');
                    setTimeout(() => {
                        window.location.href = 'refunds.php';
                    }, 600);
                } else if (this.responseText == 2) {
                    alert('error', 'Đơn chưa đủ điều kiện hoàn tiền.');
                } else {
                    alert('error', 'Không thể rút tiền lúc này.');
                }
            };

            xhr.send('withdraw_refund=1&booking_id=' + id);
        }
    </script>

    <script>
        window.addEventListener("scroll", function() {
            let navbar = document.querySelector(".custom-navbar");

            if (window.scrollY > 80) {
                navbar.classList.add("scrolled");
            } else {
                navbar.classList.remove("scrolled");
            }
        });
    </script>
</body>

</html>