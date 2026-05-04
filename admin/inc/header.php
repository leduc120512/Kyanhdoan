<!-- ===== HEADER ===== -->
<div class="container-fluid admin-topbar text-light p-3 d-flex align-items-center justify-content-between sticky-top">
  <div class="d-flex align-items-center gap-2">
    <span class="admin-badge"><i class="bi bi-building"></i></span>
    <h5 class="mb-0 fw-bold h-font admin-topbar__brand">Booking.com</h5>
  </div>
  <a href="logout.php" class="btn btn-light btn-sm d-flex align-items-center gap-2">
    <i class="bi bi-box-arrow-right"></i>
    <span>Đăng xuất</span>
  </a>
</div>

<!-- ===== SIDEBAR ===== -->
<div class="col-lg-2 admin-sidebar border-top border-3 border-secondary" id="dashboard-menu">
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid flex-lg-column align-items-stretch">

      <!-- Title -->
      <h4 class="mt-2 text-light admin-sidebar__title">Trang quản lý</h4>

      <!-- Toggle (Mobile) -->
      <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse"
        data-bs-target="#adminDropdown">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Menu List -->
      <div class="collapse navbar-collapse flex-column align-items-stretch mt-2" id="adminDropdown">
        <ul class="nav nav-pills flex-column">

          <!-- Dashboard -->
          <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="dashboard.php">
              <i class="bi bi-speedometer2"></i>
              <span>Bảng theo dõi</span>
            </a>
          </li>

          <!-- Bookings Group -->
          <li class="nav-item">
            <button
              class="btn admin-menu-btn text-white px-3 w-100 shadow-none text-start d-flex align-items-center justify-content-between"
              type="button" data-bs-toggle="collapse" data-bs-target="#bookingLinks">
              <span class="d-flex align-items-center gap-2">
                <i class="bi bi-calendar2-check"></i>
                <span>Đặt phòng</span>
              </span>
              <span><i class="bi bi-caret-down-fill"></i></span>
            </button>

            <div class="collapse show px-3 small mb-1 admin-subnav" id="bookingLinks">
              <ul class="nav nav-pills flex-column rounded border border-secondary admin-subnav__list">

                <!-- Rooms -->
                <li class="nav-item">
                  <a class="nav-link text-white d-flex align-items-center gap-2" href="rooms.php">
                    <i class="bi bi-door-open"></i>
                    <span>Danh sách phòng</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link text-white d-flex align-items-center gap-2" href="new_bookings.php">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Lượt đặt phòng mới</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link text-white d-flex align-items-center gap-2" href="refund_bookings.php">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>Yêu cầu hoàn tiền</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link text-white d-flex align-items-center gap-2" href="booking_order.php">
                    <i class="bi bi-credit-card"></i>
                    <span>Xác nhận thanh toán</span>
                  </a>
                </li>

              </ul>
            </div>
          </li>

          <!-- Users -->
          <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="users.php">
              <i class="bi bi-people"></i>
              <span>Người dùng</span>
            </a>
          </li>

          <!-- Messages -->
          <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="user_queries.php">
              <i class="bi bi-chat-dots"></i>
              <span>Tin nhắn</span>
            </a>
          </li>

          <!-- Reviews -->
          <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="rate_review.php">
              <i class="bi bi-star"></i>
              <span>Đánh giá</span>
            </a>
          </li>


          <!-- Facilities -->
          <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="features_facilities.php">
              <i class="bi bi-grid-3x3-gap"></i>
              <span>Không gian và Tiện nghi</span>
            </a>
          </li>

          <!-- Carousel -->
          <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="carousel.php">
              <i class="bi bi-images"></i>
              <span>Trình chiếu</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="referrals.php">
              <i class="bi bi-people-fill"></i>
              <span>Người giới thiệu</span>
            </a>
          </li>
          <!-- Settings -->


          <!-- Coupons -->
          <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="coupons.php">
              <i class="bi bi-ticket-perforated"></i>
              <span>Mã giảm giá</span>
            </a>
          </li>

          <!-- Referrals -->
          <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="settings.php">
              <i class="bi bi-gear"></i>
              <span>Cài đặt trang</span>
            </a>
          </li>

        </ul>
      </div>

    </div>
  </nav>
</div>