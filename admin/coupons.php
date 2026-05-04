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
    <title>Quản lý mã giảm giá</title>
    <?php require('inc/links.php'); ?>
</head>

<body class="bg-light">

    <?php require('inc/header.php'); ?>

    <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
                <h3 class="mb-4">Mã giảm giá</h3>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <form id="coupon-form" autocomplete="off">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Mã</label>
                                    <input type="text" name="code" class="form-control shadow-none" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label fw-bold">Loại</label>
                                    <select name="discount_type" class="form-select shadow-none">
                                        <option value="percent">%</option>
                                        <option value="fixed">VND</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label fw-bold">Giá trị</label>
                                    <input type="number" min="0" name="discount_value" class="form-control shadow-none" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label fw-bold">Giảm tối đa</label>
                                    <input type="number" min="0" name="max_discount" class="form-control shadow-none" placeholder="Tùy chọn">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Giá trị tối thiểu</label>
                                    <input type="number" min="0" name="min_booking" class="form-control shadow-none" value="0">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Ngày bắt đầu</label>
                                    <input type="date" name="start_date" class="form-control shadow-none">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Ngày kết thúc</label>
                                    <input type="date" name="end_date" class="form-control shadow-none">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Giới hạn lượt</label>
                                    <input type="number" min="0" name="usage_limit" class="form-control shadow-none" placeholder="Tùy chọn">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Kích hoạt</label>
                                    <select name="is_active" class="form-select shadow-none">
                                        <option value="1" selected>Có</option>
                                        <option value="0">Không</option>
                                    </select>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn custom-bg text-white">Thêm mã</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover border text-center">
                        <thead class="bg-dark text-light">
                            <tr>
                                <th>#</th>
                                <th>Mã</th>
                                <th>Loại</th>
                                <th>Giá trị</th>
                                <th>Giới hạn</th>
                                <th>Đã dùng</th>
                                <th>Hiệu lực</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="coupon-data"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php require('inc/scripts.php'); ?>
    <script src="scripts/coupons.js"></script>
</body>

</html>