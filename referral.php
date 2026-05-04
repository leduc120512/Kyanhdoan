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
                    <?php } else { ?>
                        <p class="text-muted mb-0">Bạn chưa có dữ liệu hoa hồng.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <?php require('inc/footer.php'); ?>

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