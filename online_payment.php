<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/admin/inc/db_config.php';
require_once __DIR__ . '/admin/inc/essentials.php';

if (!isset($_SESSION['online_payment'])) {
    redirect('rooms.php');
}

$payment = $_SESSION['online_payment'];
$order_id = $payment['order_id'];
$amount   = $payment['amount'];
$booking_id = $payment['booking_id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once 'inc/links.php'; ?>
    <title>Thanh toán online</title>
</head>

<body>
    <?php require_once 'inc/header.php'; ?>

    <div class="container my-5">
        <div class="card p-4 booking-card">
            <h4>Thanh toán online</h4>
            <p>Mã đặt phòng: <strong><?php echo $order_id ?></strong></p>
            <p>Số tiền cọc: <strong><?php echo format_money($amount) ?></strong></p>

            <div class="text-center my-4">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php
                                                                                        echo urlencode("PAY|$order_id|$amount");
                                                                                        ?>" />
            </div>

            <div class="text-center">
                <a href="bookings.php" class="btn btn-dark">Quay lại đặt phòng</a>
            </div>
        </div>
    </div>

    <?php require_once 'inc/footer.php'; ?>
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