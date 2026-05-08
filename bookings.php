<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - Lịch sử đặt phòng</title>
</head>

<body class="bg-light">

  <?php
  require('inc/header.php');

  if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('index.php');
  }
  ?>

  <div class="container">
    <div class="row">

      <div class="col-12 my-5 px-4">
        <h2 class="fw-bold h-font">Lịch sử đặt phòng</h2>
        <div class="breadcrumb-mini">
          <a href="index.php" class="text-secondary text-decoration-none">Trang chủ</a>
          <span class="text-secondary"> > </span>
          <a href="#" class="text-secondary text-decoration-none">Lịch sử đặt phòng</a>
        </div>
      </div>

      <?php
      $query = "SELECT bo.*, bd.* FROM `booking_order` bo
INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
WHERE ((bo.booking_status='booked') 
OR (bo.booking_status='cancelled')
OR (bo.booking_status='payment failed')
OR (bo.booking_status='pending'))
AND (bo.user_id=?)
ORDER BY bo.booking_id DESC";

      $result = select($query, [$_SESSION['uId']], 'i');

      while ($data = mysqli_fetch_assoc($result)) {

        // ===== DATE =====
        $date = date("d-m-Y", strtotime($data['datentime']));
        $checkin = date("d-m-Y", strtotime($data['check_in']));
        $checkout = date("d-m-Y", strtotime($data['check_out']));

        // ===== DATA =====
        $total_amt = (int)$data['total_amt'];
        $deposit_amt = (int)$data['deposit_amt'];
        $remaining_amt = (int)$data['remaining_amt'];
        $discount_amt = (int)$data['discount_amt'];
        $coupon_code = $data['coupon_code'];
        $payment_status = $data['payment_status'];
        $payment_method = $data['payment_method'];
        $refund = (int)$data['refund'];

        // ===== FORMAT =====
        $formatted_price = number_format($data['price']);
        $formatted_total = number_format($total_amt ?: (int)$data['total_pay']);
        $formatted_deposit = number_format($deposit_amt ?: 0);
        $formatted_remaining = number_format($remaining_amt ?: 0);

        // ===== TIME =====
        $booked_at = new DateTime($data['datentime']);
        $now = new DateTime();
        $hours_diff = ($now->getTimestamp() - $booked_at->getTimestamp()) / 3600;
        $within_24h = $hours_diff <= 24;

        // ===== STATUS =====
        $status_bg = "";
        $btn = "";
        $note = "";
        $refund_badge = "";

        // Cho phép đánh giá khi admin đã xác nhận thanh toán hoặc khách đã đến
        $can_review = ($data['full_payment_confirmed'] == 1 || $data['arrival'] == 1);

        if ($data['booking_status'] == 'booked' || ($data['booking_status'] == 'pending' && $can_review)) {
          $status_bg = ($data['booking_status'] == 'booked') ? "bg-success" : "bg-warning";

          if ($can_review) {
            if ($data['rate_review'] == 0) {
              $btn .= "<button type='button' onclick='review_room({$data['booking_id']},{$data['room_id']})' data-bs-toggle='modal' data-bs-target='#reviewModal' class='btn btn-dark btn-sm shadow-none ms-2'>Rate & Review</button>";
            }
          } else {
            if ($within_24h) {
              $btn = "<button onclick='cancel_booking({$data['booking_id']})' type='button' class='btn btn-danger btn-sm shadow-none'>Cancel</button>";
            } else {
              $note = "<div class='small text-muted mt-2'>Sau 24h vui lòng liên hệ để hủy, sẽ mất cọc.</div>";
            }
          }
        } else if ($data['booking_status'] == 'cancelled') {
          $status_bg = "bg-danger";

          if ($refund == 1) {
            $refund_badge = "<span class='badge bg-success'>Đã hoàn</span>";
          } else if ($payment_method == 'online' && $deposit_amt > 0 && $payment_status == 'chờ hoàn') {
            $refund_badge = "<span class='badge bg-warning text-dark'>Chờ hoàn</span>";
            $note = "<div class='small text-muted mt-2'>Rút tiền cọc tại mục <a href='refunds.php'>Rút tiền cọc</a>.</div>";
          }
        } else {
          $status_bg = "bg-warning";
        }

        // ===== COUPON =====
        $coupon_html = "";
        if ($discount_amt > 0 && $coupon_code) {
          $discount_format = number_format($discount_amt);
          $coupon_html = "<div class='small text-muted'>Mã giảm giá: {$coupon_code} (-{$discount_format} VND)</div>";
        }

        // ===== PAYMENT =====
        $payment_html = "";
        if ($payment_status) {
          $payment_html = "<span class='badge bg-info text-dark'>{$payment_status}</span>";
        }

        // ===== HTML =====
        echo <<<HTML
  <div class='col-md-4 px-4 mb-4'>
    <div class='bg-white p-3 booking-card'>
      <h5 class='fw-bold'>{$data['room_name']}</h5>
      <p>{$formatted_price} VND / đêm</p>

      <p>
        <b>Check in:</b> {$checkin} <br>
        <b>Check out:</b> {$checkout}
      </p>

      <p>
        <b>Tổng tiền:</b> {$formatted_total} VND <br>
        <b>Tiền cọc:</b> {$formatted_deposit} VND <br>
        <b>Còn lại:</b> {$formatted_remaining} VND <br>
        <b>Order ID:</b> {$data['order_id']} <br>
        <b>Date:</b> {$date}
      </p>

      {$coupon_html}

      <p>
        <span class='badge {$status_bg}'>{$data['booking_status']}</span>
        {$payment_html}
        {$refund_badge}
      </p>

      {$btn}
      {$note}
    </div>
  </div>
HTML;
      }
      ?>

    </div>
  </div>

  <!-- REVIEW MODAL -->
  <div class="modal fade" id="reviewModal" data-bs-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="review-form">
          <div class="modal-header">
            <h5 class="modal-title">Rate & Review</h5>
            <button type="reset" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <select class="form-select mb-3" name="rating">
              <option value="5">Excellent</option>
              <option value="4">Good</option>
              <option value="3">Ok</option>
              <option value="2">Poor</option>
              <option value="1">Bad</option>
            </select>

            <textarea name="review" rows="3" required class="form-control mb-3"></textarea>

            <input type="hidden" name="booking_id">
            <input type="hidden" name="room_id">
            <input type="hidden" name="review_form" value="1">

            <div class="text-end">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php
  if (isset($_GET['cancel_status'])) {
    alert('success', 'Huỷ đặt phòng!');
  } else if (isset($_GET['review_status'])) {
    alert('success', 'Cảm ơn bạn đã đánh giá!');
  }
  ?>

  <?php require('inc/footer.php'); ?>

  <script>
    function cancel_booking(id) {
      if (confirm('Bạn có chắc muốn huỷ?')) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/cancel_booking.php", true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
          if (this.responseText == 1) {
            location.href = "bookings.php?cancel_status=true";
          } else if (this.responseText == 2) {
            alert('error', 'Đã quá 24h nên không thể hủy online. Vui lòng liên hệ.');
          } else {
            alert('error', 'Không thể hủy đơn này.');
          }
        }

        xhr.send("cancel_booking&id=" + id);
      }
    }

    let review_form = document.getElementById('review-form');

    function review_room(bid, rid) {
      review_form.elements['booking_id'].value = bid;
      review_form.elements['room_id'].value = rid;
    }

    review_form.addEventListener('submit', function(e) {
      e.preventDefault();

      let data = new FormData(this);

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/review_room.php", true);

      xhr.onload = function() {
        if (this.responseText == 1) {
          location.href = "bookings.php?review_status=true";
        }
      }

      xhr.send(data);
    });
  </script>

</body>

</html>