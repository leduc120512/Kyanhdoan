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
    <title>Trang quản lý - Yêu cầu rút tiền</title>
    <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

    <?php require('inc/header.php'); ?>

    <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
                <h3 class="mb-1">Yêu cầu rút tiền</h3>
                <p class="text-muted mb-4">Xem xét và xác nhận chuyển khoản cho khách hàng.</p>

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" id="withdrawTab">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-pending">
                            Chờ duyệt <span class="badge bg-danger ms-1" id="pending-count"></span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-done">
                            Đã xử lý
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Chờ duyệt -->
                    <div class="tab-pane fade show active" id="tab-pending">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="bg-dark text-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Khách hàng</th>
                                                <th>Loại</th>
                                                <th>Số tiền</th>
                                                <th>Ngân hàng</th>
                                                <th>Số tài khoản</th>
                                                <th>Tên chủ TK</th>
                                                <th>Ngày gửi</th>
                                                <th>Xử lý</th>
                                            </tr>
                                        </thead>
                                        <tbody id="pending-data">
                                            <tr><td colspan="9" class="text-center text-muted">Đang tải...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Đã xử lý -->
                    <div class="tab-pane fade" id="tab-done">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="bg-dark text-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Khách hàng</th>
                                                <th>Loại</th>
                                                <th>Số tiền</th>
                                                <th>Ngân hàng</th>
                                                <th>Số tài khoản</th>
                                                <th>Trạng thái</th>
                                                <th>Ghi chú</th>
                                                <th>Ngày xử lý</th>
                                            </tr>
                                        </thead>
                                        <tbody id="done-data">
                                            <tr><td colspan="9" class="text-center text-muted">Đang tải...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal từ chối -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Từ chối yêu cầu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Lý do từ chối (tùy chọn)</label>
                    <input type="text" id="reject-note" class="form-control shadow-none" placeholder="Nhập lý do...">
                    <input type="hidden" id="reject-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" onclick="confirm_reject()">Xác nhận từ chối</button>
                </div>
            </div>
        </div>
    </div>

    <?php require('inc/scripts.php'); ?>
    <script src="scripts/withdrawals.js"></script>
</body>
</html>
