<?php
require('inc/links.php');
require('inc/header.php');

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('index.php');
}

$user_id = (int)$_SESSION['uId'];

// Đơn cọc đủ điều kiện rút (chưa có yêu cầu rút pending/approved)
$pending_res = select(
    "SELECT bo.*, bd.room_name FROM `booking_order` bo
    INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
    WHERE bo.user_id=? AND bo.booking_status='cancelled'
    AND bo.payment_method='online' AND bo.deposit_amt>0 AND bo.refund=0
    AND bo.payment_status='chờ hoàn'
    AND bo.booking_id NOT IN (
        SELECT ref_id FROM withdrawal_requests
        WHERE user_id=? AND type='refund' AND status IN ('pending','approved')
    )
    ORDER BY bo.booking_id DESC",
    [$user_id, $user_id],
    'ii'
);

// Yêu cầu rút cọc đã gửi
$sent_res = select(
    "SELECT wr.*, bd.room_name FROM `withdrawal_requests` wr
    INNER JOIN `booking_details` bd ON wr.ref_id = bd.booking_id
    WHERE wr.user_id=? AND wr.type='refund'
    ORDER BY wr.created_at DESC LIMIT 20",
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
                        Sau khi gửi yêu cầu, admin sẽ xem xét và chuyển khoản cho bạn.
                        Vui lòng nhập đúng thông tin ngân hàng để tránh thất lạc.
                    </p>
                </div>
            </div>

            <!-- Đơn chờ rút -->
            <div class="col-12 px-4 mb-5">
                <div class="bg-white p-3 p-md-4 booking-card">
                    <h5 class="fw-bold mb-3">Đơn chờ hoàn tiền</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Mã đặt phòng</th>
                                    <th>Phòng</th>
                                    <th>Tiền cọc</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($pending_res) == 0) {
                                    echo "<tr><td colspan='4' class='text-center text-muted'>Không có đơn nào chờ hoàn.</td></tr>";
                                } else {
                                    while ($row = mysqli_fetch_assoc($pending_res)) {
                                        $amount = number_format($row['deposit_amt']);
                                        echo "<tr>
                                            <td>{$row['order_id']}</td>
                                            <td>{$row['room_name']}</td>
                                            <td><b>{$amount} VND</b></td>
                                            <td>
                                                <button class='btn btn-primary btn-sm'
                                                    onclick='open_withdraw_modal({$row['booking_id']}, {$row['deposit_amt']}, \"refund\")'>
                                                    Rút tiền
                                                </button>
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

            <!-- Lịch sử yêu cầu rút cọc -->
            <div class="col-12 px-4 mb-5">
                <div class="bg-white p-3 p-md-4 booking-card">
                    <h5 class="fw-bold mb-3">Lịch sử yêu cầu rút cọc</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Phòng</th>
                                    <th>Số tiền</th>
                                    <th>Ngân hàng</th>
                                    <th>Số tài khoản</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày gửi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($sent_res) == 0) {
                                    echo "<tr><td colspan='6' class='text-center text-muted'>Chưa có yêu cầu nào.</td></tr>";
                                } else {
                                    while ($row = mysqli_fetch_assoc($sent_res)) {
                                        $amount = number_format($row['amount']);
                                        $created = date('d-m-Y H:i', strtotime($row['created_at']));
                                        if ($row['status'] == 'approved') {
                                            $badge = "<span class='badge bg-success'>Đã duyệt</span>";
                                        } else if ($row['status'] == 'rejected') {
                                            $badge = "<span class='badge bg-danger'>Từ chối</span>";
                                            if ($row['admin_note']) {
                                                $badge .= "<div class='small text-muted'>{$row['admin_note']}</div>";
                                            }
                                        } else {
                                            $badge = "<span class='badge bg-warning text-dark'>Chờ duyệt</span>";
                                        }
                                        echo "<tr>
                                            <td>{$row['room_name']}</td>
                                            <td><b>{$amount} VND</b></td>
                                            <td>{$row['bank_name']}</td>
                                            <td>{$row['bank_account']}<br><small class='text-muted'>{$row['account_name']}</small></td>
                                            <td>{$badge}</td>
                                            <td>{$created}</td>
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

    <!-- MODAL nhập thông tin ngân hàng -->
    <div class="modal fade" id="withdrawModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="withdraw-form">
                    <div class="modal-header">
                        <h5 class="modal-title">Thông tin rút tiền</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info small mb-3">
                            Số tiền: <b id="withdraw-amount-display"></b> VND
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên ngân hàng <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" class="form-control shadow-none" placeholder="VD: Vietcombank, MB Bank..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Số tài khoản <span class="text-danger">*</span></label>
                            <input type="text" name="bank_account" class="form-control shadow-none" placeholder="Nhập số tài khoản" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên chủ tài khoản <span class="text-danger">*</span></label>
                            <input type="text" name="account_name" class="form-control shadow-none" placeholder="Nhập tên chủ tài khoản" required>
                        </div>
                        <input type="hidden" name="ref_id">
                        <input type="hidden" name="amount">
                        <input type="hidden" name="type">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require('inc/footer.php'); ?>

    <script>
        let withdrawModal = new bootstrap.Modal(document.getElementById('withdrawModal'));
        let withdrawForm  = document.getElementById('withdraw-form');

        function open_withdraw_modal(ref_id, amount, type) {
            withdrawForm.reset();
            withdrawForm.elements['ref_id'].value  = ref_id;
            withdrawForm.elements['amount'].value  = amount;
            withdrawForm.elements['type'].value    = type;
            document.getElementById('withdraw-amount-display').textContent =
                new Intl.NumberFormat('vi-VN').format(amount);
            withdrawModal.show();
        }

        withdrawForm.addEventListener('submit', function(e) {
            e.preventDefault();
            let data = new FormData(this);
            data.append('submit_withdrawal', '1');

            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'ajax/withdrawal.php', true);
            xhr.onload = function() {
                if (this.responseText == 1) {
                    withdrawModal.hide();
                    alert('success', 'Yêu cầu đã được gửi! Admin sẽ xử lý sớm.');
                    setTimeout(() => location.reload(), 800);
                } else if (this.responseText == 2) {
                    alert('error', 'Đơn này đã có yêu cầu rút đang xử lý.');
                } else {
                    alert('error', 'Không thể gửi yêu cầu, thử lại sau.');
                }
            };
            xhr.send(data);
        });

        window.addEventListener('scroll', function() {
            let navbar = document.querySelector('.custom-navbar');
            if (window.scrollY > 80) navbar.classList.add('scrolled');
            else navbar.classList.remove('scrolled');
        });
    </script>

</body>
</html>
