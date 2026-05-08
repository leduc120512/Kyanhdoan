<?php
require('inc/links.php');
require('inc/header.php');

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('index.php');
}

$user_id = (int)$_SESSION['uId'];
$ref_res = select("SELECT * FROM `referral_users` WHERE `user_id`=? ORDER BY `id` DESC LIMIT 1", [$user_id], 'i');
$ref_row = null;
$commission_base_label = 'Tổng tiền';

if (mysqli_num_rows($ref_res) > 0) {
    $ref_row = mysqli_fetch_assoc($ref_res);

    if (isset($ref_row['commission_base'])) {
        if ($ref_row['commission_base'] == 'deposit') {
            $commission_base_label = 'Tiền cọc';
        } else if ($ref_row['commission_base'] == 'remaining') {
            $commission_base_label = 'Còn lại';
        }
    }
}

function generate_ref_code($user_id, $con)
{
    $tries = 0;
    do {
        $seed = $user_id . '-' . random_int(1000, 9999) . '-' . time();
        $code = 'REF' . strtoupper(substr(md5($seed), 0, 8));
        $chk = mysqli_query($con, "SELECT `id` FROM `referral_users` WHERE `referral_code`='$code' LIMIT 1");
        $tries++;
    } while (mysqli_num_rows($chk) > 0 && $tries < 5);

    return $code;
}

if (isset($_POST['apply_referral'])) {
    if (!$ref_row || $ref_row['status'] == 'rejected') {
        $new_code = generate_ref_code($user_id, $con);
        $default_commission_pct = 5.00;
        if (isset($settings_r['referral_commission_default'])) {
            $default_commission_pct = (float)$settings_r['referral_commission_default'];
        }
        if ($default_commission_pct < 0) {
            $default_commission_pct = 0;
        }
        if ($default_commission_pct > 100) {
            $default_commission_pct = 100;
        }

        if ($ref_row) {
            update(
                "UPDATE `referral_users` SET `referral_code`=?, `status`='pending', `approved_at`=NULL WHERE `id`=?",
                [$new_code, $ref_row['id']],
                'si'
            );
        } else {
            insert(
                "INSERT INTO `referral_users` (`user_id`, `referral_code`, `commission_pct`, `status`) VALUES (?,?,?,?)",
                [$user_id, $new_code, $default_commission_pct, 'pending'],
                'isds'
            );
        }

        redirect('referral.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings_r['site_title'] ?> - Hoa hồng giới thiệu</title>
</head>

<body class="bg-light">

    <div class="container">
        <div class="row">
            <div class="col-12 my-5 px-4">
                <h2 class="fw-bold h-font">Hoa hồng giới thiệu</h2>
                <div class="breadcrumb-mini">
                    <a href="index.php" class="text-secondary text-decoration-none">Trang chủ</a>
                    <span class="text-secondary"> > </span>
                    <a href="#" class="text-secondary text-decoration-none">Hoa hồng</a>
                </div>
            </div>

            <div class="col-lg-5 px-4 mb-4">
                <div class="bg-white p-4 profile-card">
                    <h5 class="fw-bold">Đăng ký làm người giới thiệu</h5>
                    <p class="text-muted small mb-4">
                        Sau khi đăng ký, admin sẽ xét duyệt. Khi được duyệt, bạn sẽ nhận mã giới thiệu để nhận hoa hồng.
                    </p>

                    <?php if (!$ref_row) { ?>
                        <form method="POST">
                            <button type="submit" name="apply_referral" class="btn custom-bg text-white">Gửi đăng ký</button>
                        </form>
                    <?php } else if ($ref_row['status'] == 'pending') { ?>
                        <div class="alert alert-warning mb-0">
                            Yêu cầu đang chờ xét duyệt.
                        </div>
                    <?php } else if ($ref_row['status'] == 'approved') { ?>
                        <div class="alert alert-success">
                            Bạn đã được duyệt! Mã giới thiệu của bạn là:
                        </div>
                        <div class="d-flex align-items-center justify-content-between bg-light p-3 rounded">
                            <span class="fw-bold text-primary"><?php echo $ref_row['referral_code'] ?></span>
                            <div class="text-end">
                                <div class="badge bg-info text-dark mb-1"><?php echo $ref_row['commission_pct'] ?>%</div>
                                <div class="small text-muted">Theo <?php echo $commission_base_label ?></div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-danger">
                            Yêu cầu bị từ chối. Bạn có thể gửi lại đăng ký.
                        </div>
                        <form method="POST">
                            <button type="submit" name="apply_referral" class="btn custom-bg text-white">Gửi lại đăng ký</button>
                        </form>
                    <?php } ?>
                </div>
            </div>

            <div class="col-lg-7 px-4 mb-4">
                <div class="bg-white p-4 profile-card">
                    <h5 class="fw-bold mb-3">Thống kê hoa hồng</h5>
                    <?php
                    if ($ref_row && $ref_row['status'] == 'approved') {
                        $ref_id = (int)$ref_row['id'];
                        $stats = mysqli_fetch_assoc(select("SELECT
                  COUNT(*) AS total_refs,
                  COALESCE(SUM(commission_amt),0) AS total_commission,
                  COALESCE(SUM(CASE WHEN commission_status='paid' THEN commission_amt ELSE 0 END),0) AS paid_commission
                FROM booking_order WHERE referral_id=?", [$ref_id], 'i'));

                        $pending_commission = $stats['total_commission'] - $stats['paid_commission'];

                        // Kiểm tra đã có yêu cầu rút đang pending chưa
                        $has_pending_withdrawal = false;
                        $pw_res = select("SELECT id FROM withdrawal_requests WHERE user_id=? AND type='commission' AND status='pending' LIMIT 1", [$user_id], 'i');
                        if (mysqli_num_rows($pw_res) > 0) $has_pending_withdrawal = true;
                    ?>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <div class="small text-muted">Lượt đặt phòng</div>
                                    <div class="fw-bold fs-5"><?php echo $stats['total_refs'] ?></div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <div class="small text-muted">Hoa hồng chờ</div>
                                    <div class="fw-bold fs-5"><?php echo number_format($pending_commission) ?> VND</div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <div class="small text-muted">Đã thanh toán</div>
                                    <div class="fw-bold fs-5"><?php echo number_format($stats['paid_commission']) ?> VND</div>
                                </div>
                            </div>
                        </div>

                        <?php if ($pending_commission > 0) { ?>
                        <div class="mb-3">
                            <button class="btn btn-success" onclick="open_withdraw_modal(<?php echo $ref_id ?>, <?php echo $pending_commission ?>, 'commission')">
                                <i class="bi bi-cash-coin me-1"></i> Rút hoa hồng (<?php echo number_format($pending_commission) ?> VND)
                            </button>
                        </div>
                        <?php } elseif ($has_pending_withdrawal) { ?>
                        <div class="alert alert-warning small mb-3">
                            Yêu cầu rút hoa hồng đang chờ admin xử lý.
                        </div>
                        <?php } ?>

                        <?php
                        $list_res = select("SELECT order_id, total_amt, commission_amt, commission_status, datentime
                  FROM booking_order WHERE referral_id=? ORDER BY datentime DESC LIMIT 10", [$ref_id], 'i');
                        ?>
                        <div class="table-responsive">
                            <table class="table table-sm mt-3">
                                <thead>
                                    <tr>
                                        <th>Mã đặt phòng</th>
                                        <th>Tổng tiền</th>
                                        <th>Hoa hồng</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = mysqli_fetch_assoc($list_res)) {
                                        $status = $row['commission_status'] == 'paid' ? 'Đã trả' : 'Chờ xử lý';
                                        echo "<tr>
                          <td>$row[order_id]</td>
                          <td>" . number_format($row['total_amt']) . " VND</td>
                          <td>" . number_format($row['commission_amt']) . " VND</td>
                          <td>$status</td>
                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <?php
                        // Lịch sử yêu cầu rút hoa hồng
                        $wr_res = select("SELECT * FROM withdrawal_requests WHERE user_id=? AND type='commission' ORDER BY created_at DESC LIMIT 10", [$user_id], 'i');
                        if (mysqli_num_rows($wr_res) > 0) {
                        ?>
                        <h6 class="fw-bold mt-4 mb-2">Lịch sử yêu cầu rút</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Số tiền</th>
                                        <th>Ngân hàng</th>
                                        <th>Số TK</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày gửi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($wr = mysqli_fetch_assoc($wr_res)) {
                                        $wr_amount = number_format($wr['amount']);
                                        $wr_date   = date('d-m-Y H:i', strtotime($wr['created_at']));
                                        if ($wr['status'] == 'approved') {
                                            $wr_badge = "<span class='badge bg-success'>Đã duyệt</span>";
                                        } else if ($wr['status'] == 'rejected') {
                                            $wr_badge = "<span class='badge bg-danger'>Từ chối</span>";
                                            if ($wr['admin_note']) $wr_badge .= "<div class='small text-muted'>{$wr['admin_note']}</div>";
                                        } else {
                                            $wr_badge = "<span class='badge bg-warning text-dark'>Chờ duyệt</span>";
                                        }
                                        echo "<tr>
                                            <td><b>{$wr_amount} VND</b></td>
                                            <td>{$wr['bank_name']}</td>
                                            <td>{$wr['bank_account']}<br><small class='text-muted'>{$wr['account_name']}</small></td>
                                            <td>{$wr_badge}</td>
                                            <td>{$wr_date}</td>
                                        </tr>";
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } ?>
                    <?php } else { ?>
                        <p class="text-muted mb-0">Bạn chưa có dữ liệu hoa hồng.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <?php require('inc/footer.php'); ?>

    <!-- MODAL nhập thông tin ngân hàng -->
    <div class="modal fade" id="withdrawModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="withdraw-form">
                    <div class="modal-header">
                        <h5 class="modal-title">Thông tin rút hoa hồng</h5>
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

    <script>
        window.addEventListener("scroll", function() {
            let navbar = document.querySelector(".custom-navbar");
            if (window.scrollY > 80) navbar.classList.add("scrolled");
            else navbar.classList.remove("scrolled");
        });

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
                    alert('error', 'Đã có yêu cầu rút đang xử lý.');
                } else {
                    alert('error', 'Không thể gửi yêu cầu, thử lại sau.');
                }
            };
            xhr.send(data);
        });
    </script>
</body>

</html>