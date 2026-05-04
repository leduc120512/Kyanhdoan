<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - Danh sách phòng</title>
</head>

<body class="bg-light">

  <?php
  require('inc/header.php');

  $checkin_default = "";
  $checkout_default = "";
  $adult_default = "";
  $children_default = "";
  $min_price_default = "";
  $max_price_default = "";

  if (isset($_GET['check_availability'])) {
    $frm_data = filteration($_GET);

    $checkin_default = $frm_data['checkin'];
    $checkout_default = $frm_data['checkout'];
    $adult_default = $frm_data['adult'];
    $children_default = $frm_data['children'];
    $min_price_default = $frm_data['min_price'];
    $max_price_default = $frm_data['max_price'];
  }
  ?>

  <div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">DANH SÁCH PHÒNG</h2>
    <div class="h-line bg-dark"></div>
  </div>

  <div class="container-fluid">
    <div class="row">

      <!-- FILTER -->
      <div class="col-lg-3 col-md-12 mb-lg-0 mb-4 ps-4">
        <nav class="navbar navbar-expand-lg navbar-light filter-panel">
          <div class="container-fluid flex-lg-column align-items-stretch">
            <h4 class="mt-2">Bộ lọc</h4>

            <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterDropdown">
              <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse flex-column align-items-stretch mt-2" id="filterDropdown">

              <!-- Check availability -->
              <div class="filter-card p-3 mb-3">
                <h5 class="d-flex justify-content-between">
                  <span>Kiểm tra phòng trống</span>
                  <button id="chk_avail_btn" onclick="chk_avail_clear()" class="btn btn-sm text-secondary d-none">Reset</button>
                </h5>

                <label>Nhận phòng</label>
                <input type="date" id="checkin" value="<?php echo $checkin_default ?>" class="form-control mb-3" onchange="chk_avail_filter()">

                <label>Trả phòng</label>
                <input type="date" id="checkout" value="<?php echo $checkout_default ?>" class="form-control" onchange="chk_avail_filter()">
              </div>

              <!-- Facilities -->
              <div class="filter-card p-3 mb-3">
                <h5 class="d-flex justify-content-between">
                  <span>Tiện ích</span>
                  <button id="facilities_btn" onclick="facilities_clear()" class="btn btn-sm text-secondary d-none">Reset</button>
                </h5>

                <?php
                $facilities_q = selectAll('facilities');
                while ($row = mysqli_fetch_assoc($facilities_q)) {
                  echo "
                  <div>
                    <input type='checkbox' onclick='fetch_rooms()' value='$row[id]' name='facilities'>
                    $row[name]
                  </div>";
                }
                ?>
              </div>

              <!-- Guests -->
              <div class="filter-card p-3 mb-3">
                <h5 class="d-flex justify-content-between">
                  <span>Số khách</span>
                  <button id="guests_btn" onclick="guests_clear()" class="btn btn-sm text-secondary d-none">Reset</button>
                </h5>

                <input type="number" id="adults" placeholder="Người lớn" value="<?php echo $adult_default ?>" oninput="guests_filter()" class="form-control mb-2">
                <input type="number" id="children" placeholder="Trẻ em" value="<?php echo $children_default ?>" oninput="guests_filter()" class="form-control">
              </div>

              <!-- PRICE FILTER -->
              <div class="filter-card p-3 mb-3">
                <h5 class="d-flex justify-content-between">
                  <span>Giá phòng</span>
                  <button id="price_btn" onclick="price_clear()" class="btn btn-sm text-secondary d-none">Reset</button>
                </h5>

                <input type="number" id="min_price" placeholder="Giá thấp nhất" value="<?php echo $min_price_default ?>" oninput="price_filter()" class="form-control mb-2">
                <input type="number" id="max_price" placeholder="Giá cao nhất" value="<?php echo $max_price_default ?>" oninput="price_filter()" class="form-control">
              </div>

            </div>
          </div>
        </nav>
      </div>

      <!-- ROOM DATA -->
      <div class="col-lg-9 col-md-12 px-4" id="rooms-data"></div>

    </div>
  </div>

  <script>
    let rooms_data = document.getElementById('rooms-data');

    let checkin = document.getElementById('checkin');
    let checkout = document.getElementById('checkout');
    let chk_avail_btn = document.getElementById('chk_avail_btn');

    let adults = document.getElementById('adults');
    let children = document.getElementById('children');
    let guests_btn = document.getElementById('guests_btn');

    let facilities_btn = document.getElementById('facilities_btn');

    let min_price = document.getElementById('min_price');
    let max_price = document.getElementById('max_price');
    let price_btn = document.getElementById('price_btn');

    function fetch_rooms() {
      let chk_avail = JSON.stringify({
        checkin: checkin.value,
        checkout: checkout.value
      });

      let guests = JSON.stringify({
        adults: adults.value,
        children: children.value
      });

      let price = JSON.stringify({
        min_price: min_price.value,
        max_price: max_price.value
      });

      let facility_list = {
        "facilities": []
      };

      document.querySelectorAll('[name="facilities"]:checked').forEach(el => {
        facility_list.facilities.push(el.value);
      });

      facility_list = JSON.stringify(facility_list);

      let xhr = new XMLHttpRequest();
      xhr.open("GET", "ajax/rooms.php?fetch_rooms&chk_avail=" + chk_avail + "&guests=" + guests + "&facility_list=" + facility_list + "&price=" + price, true);

      xhr.onload = function() {
        rooms_data.innerHTML = this.responseText;
      }

      xhr.send();
    }

    function chk_avail_filter() {
      if (checkin.value && checkout.value) {
        fetch_rooms();
        chk_avail_btn.classList.remove('d-none');
      }
    }

    function chk_avail_clear() {
      checkin.value = '';
      checkout.value = '';
      chk_avail_btn.classList.add('d-none');
      fetch_rooms();
    }

    function guests_filter() {
      fetch_rooms();
      guests_btn.classList.remove('d-none');
    }

    function guests_clear() {
      adults.value = '';
      children.value = '';
      guests_btn.classList.add('d-none');
      fetch_rooms();
    }

    function facilities_clear() {
      document.querySelectorAll('[name="facilities"]').forEach(el => el.checked = false);
      facilities_btn.classList.add('d-none');
      fetch_rooms();
    }

    function price_filter() {
      fetch_rooms();
      price_btn.classList.remove('d-none');
    }

    function price_clear() {
      min_price.value = '';
      max_price.value = '';
      price_btn.classList.add('d-none');
      fetch_rooms();
    }

    window.onload = fetch_rooms;
  </script>

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