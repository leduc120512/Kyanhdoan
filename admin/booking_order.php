<?php
require(__DIR__ . '/inc/db_config.php');
require(__DIR__ . '/inc/essentials.php');

adminLogin();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Quản lý đơn đặt phòng</title>
  <?php require('inc/links.php'); ?>
</head>

<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">

        <h3 class="mb-4">Đơn đặt phòng</h3>

        <div class="table-responsive">
          <table class="table table-hover border text-center">
            <thead class="bg-dark text-light">
              <tr>
                <th>#</th>
                <th>Khách hàng</th>
                <th>Điện thoại</th>
                <th>Phòng</th>
                <th>Nhận phòng</th>
                <th>Trả phòng</th>
                <th>Cọc/Tổng</th>
                <th>Còn lại</th>
                <th>Trạng thái</th>
                <th>Xác nhận</th>
              </tr>
            </thead>
            <tbody id="booking-data"></tbody>
          </table>
        </div>

      </div>
    </div>
  </div>

  <script src="scripts/booking_order.js"></script>

</body>

</html>