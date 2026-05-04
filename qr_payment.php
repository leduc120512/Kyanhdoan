<?php
session_start();
require('admin/inc/db_config.php');

if (!isset($_SESSION['last_order_id'])) {
    die("Không tìm thấy mã đơn hàng!");
}

$order_id = $_SESSION['last_order_id'];

$query = "SELECT booking_id, check_in, check_out, trans_amt 
          FROM booking_order 
          WHERE order_id = '$order_id'
          LIMIT 1";

$res = mysqli_query($con, $query);

if (mysqli_num_rows($res) == 0) {
    die("Không tìm thấy thông tin đơn hàng!");
}

$data = mysqli_fetch_assoc($res);

$checkin   = $data['check_in'];
$checkout  = $data['check_out'];
$total_pay = number_format($data['trans_amt']);

if (isset($_POST['done'])) {
    update(
        "UPDATE `booking_order` SET `payment_status`='đã cọc', `trans_status`='paid' WHERE `order_id`=? LIMIT 1",
        [$order_id],
        's'
    );
    unset($_SESSION['last_order_id']);
    echo "<script>
        alert('Cảm ơn bạn! Tiền cọc đã được ghi nhận.');
        window.location='bookings.php';
      </script>";
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Thanh toán QR</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/common.css">

</head>

<body class="qr-page">

    <div class="qr-card">
        <h2>Thanh toán qua QR</h2>
        <p>Vui lòng quét mã QR bên dưới để thanh toán tiền cọc.</p>

        <div class="qr-box">
            <img src="images/qrcodee.jpg" alt="QR Code">
        </div>

        <div class="qr-info">
            <p><b>Mã đặt phòng:</b> <?= $order_id ?></p>
            <p><b>Ngày nhận phòng:</b> <?= $checkin ?></p>
            <p><b>Ngày trả phòng:</b> <?= $checkout ?></p>
            <p><b>Số tiền cọc:</b> <?= $total_pay ?> VNĐ</p>
        </div>

        <form method="POST">
            <button name="done" class="btn custom-bg">Tôi đã thanh toán</button>
        </form>

        <p class="qr-note">Sau khi thanh toán, admin sẽ xác nhận tiền cọc trong vài phút.</p>
    </div>

    <script>
        window.addEventListener("scroll", function() {
            let navbar = document.querySelector(".custom-navbar");

            if (!navbar) {
                return;
            }

            if (window.scrollY > 80) {
                navbar.classList.add("scrolled");
            } else {
                navbar.classList.remove("scrolled");
            }
        });
    </script>
</body>

</html>