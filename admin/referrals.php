<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người giới thiệu</title>
    <?php require('inc/links.php'); ?>
</head>

<body class="bg-light">

    <?php require('inc/header.php'); ?>

    <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
                <h3 class="mb-4">Người giới thiệu</h3>

                <div class="table-responsive">
                    <table class="table table-hover border text-center">
                        <thead class="bg-dark text-light">
                            <tr>
                                <th>#</th>
                                <th>Khách hàng</th>
                                <th>Email</th>
                                <th>SĐT</th>
                                <th>Mã giới thiệu</th>
                                <th>Tỷ lệ</th>
                                <th>Cách tính</th>
                                <th>Trạng thái</th>
                                <th>Ngày đăng ký</th>
                                <th>Xử lý</th>
                            </tr>
                        </thead>
                        <tbody id="referral-data"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php require('inc/scripts.php'); ?>
    <script src="scripts/referrals.js"></script>
</body>

</html>