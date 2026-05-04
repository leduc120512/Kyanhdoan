<nav id="nav-bar" class="custom-navbar text-white py-2 sticky-top">

  <div class="container-fluid">

    <!-- TOP BAR -->
    <div class="d-flex justify-content-between align-items-center top-bar">

      <!-- LOGO -->
      <div class="logo h-font">
        <?php echo $settings_r['site_title'] ?>
      </div>

      <!-- RIGHT -->
      <div class="d-flex align-items-center gap-3">

        <span class="currency-pill">VND</span>

        <?php
        if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
          $path = USERS_IMG_PATH;
          echo <<<data
          <div class="btn-group">
            <button class="btn user-btn dropdown-toggle" data-bs-toggle="dropdown">
              <img src="$path$_SESSION[uPic]" width="25" class="rounded-circle me-1">
              $_SESSION[uName]
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="profile.php">Hồ sơ</a></li>
              <li><a class="dropdown-item" href="bookings.php">Lịch sử</a></li>
              <li><a class="dropdown-item" href="refunds.php">Rút tiền cọc</a></li>
              <li><a class="dropdown-item" href="referral.php">Hoa hồng</a></li>
              <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
            </ul>
          </div>
          data;
        } else {
          echo <<<data
          <button class="btn nav-btn" data-bs-toggle="modal" data-bs-target="#registerModal">Đăng ký</button>
          <button class="btn nav-btn" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập</button>
          data;
        }
        ?>

      </div>
    </div>

    <!-- MENU (giống Booking) -->
    <div class="nav-menu mt-3">

      <a href="index.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
        <i class="bi bi-house-door"></i> Trang chủ
      </a>

      <a href="rooms.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'rooms.php' ? 'active' : '' ?>">
        <i class="bi bi-building"></i> Danh Sách Phòng
      </a>


      <a href="contact.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>">
        <i class="bi bi-envelope"></i> Phản Hồi
      </a>

      <a href="about.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>">
        <i class="bi bi-info-circle"></i> Hoạt Động
      </a>

    </div>

  </div>
</nav>

<div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="login-form">
        <div class="modal-header">
          <h5 class="modal-title d-flex align-items-center">
            <i class="bi bi-person-circle fs-3 me-2"></i> Đăng nhập
          </h5>
          <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Email / Số điện thoại</label>
            <input type="text" name="email_mob" required class="form-control shadow-none">
          </div>
          <div class="mb-4">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="pass" required class="form-control shadow-none">
          </div>
          <div class="d-flex align-items-center justify-content-between mb-2">
            <button type="submit" class="btn btn-dark shadow-none">Tiếp tục</button>
            <button type="button" class="btn text-secondary text-decoration-none shadow-none p-0" data-bs-toggle="modal" data-bs-target="#forgotModal" data-bs-dismiss="modal">
              Bạn quên mật khẩu?
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="registerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="register-form">
        <div class="modal-header">
          <h5 class="modal-title d-flex align-items-center">
            <i class="bi bi-person-lines-fill fs-3 me-2"></i> Đăng ký
          </h5>
          <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Tên</label>
                <input name="name" type="text" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Số điện thoại</label>
                <input name="phonenum" type="number" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Ảnh đại diện</label>
                <input name="profile" type="file" accept=".jpg, .jpeg, .png, .webp" class="form-control shadow-none" required>
              </div>
              <div class="col-md-12 mb-3">
                <label class="form-label">Địa chỉ</label>
                <textarea name="address" class="form-control shadow-none" rows="1" required></textarea>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Mã định danh</label>
                <input name="pincode" type="number" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Sinh nhật</label>
                <input name="dob" type="date" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Mật khẩu</label>
                <input name="pass" type="password" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Xác nhận lại mật khẩu</label>
                <input name="cpass" type="password" class="form-control shadow-none" required>
              </div>
            </div>
          </div>
          <div class="text-center my-1">
            <button type="submit" class="btn btn-dark shadow-none">Đăng ký</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="forgotModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="forgot-form">
        <div class="modal-header">
          <h5 class="modal-title d-flex align-items-center">
            <i class="bi bi-person-circle fs-3 me-2"></i> Quên mật khẩu
          </h5>
        </div>
        <div class="modal-body">
          <span class="badge rounded-pill bg-light text-dark mb-3 text-wrap lh-base">
            Ghi chú: Liên kết sẽ được gửi tới địa chỉ email của bạn để tạo lại mật khẩu!
          </span>
          <div class="mb-4">
            <label class="form-label">Email</label>
            <input type="email" name="email" required class="form-control shadow-none">
          </div>
          <div class="mb-2 text-end">
            <button type="button" class="btn shadow-none p-0 me-2" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">
              Huỷ
            </button>
            <button type="submit" class="btn btn-dark shadow-none">Gửi</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>