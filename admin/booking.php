<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();
?>

<!DOCTYPE html>
<html>

<head>
  <title>Quản lý đặt phòng</title>
  <?php require('inc/links.php'); ?>
</head>

<body>
  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4">
        <h3 class="mb-4">Đơn đặt phòng</h3>

        <div class="table-responsive">
          <table class="table table-hover border">
            <thead>
              <tr class="bg-dark text-light">
                <th>#</th>
                <th>Họ tên</th>
                <th>SĐT</th>
                <th>Nhận phòng</th>
                <th>Trả phòng</th>
                <th>Phương thức</th>
                <th>Cọc/Tổng</th>
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

  <script src="scripts/booking.js"></script>
</body>

</html>