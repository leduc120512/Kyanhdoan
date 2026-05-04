let coupon_form = document.getElementById("coupon-form");

function get_coupons() {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "ajax/coupons.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onload = function () {
    document.getElementById("coupon-data").innerHTML = this.responseText;
  };

  xhr.send("get_coupons=1");
}

coupon_form.addEventListener("submit", function (e) {
  e.preventDefault();

  let data = new FormData(coupon_form);
  data.append("add_coupon", "1");

  let xhr = new XMLHttpRequest();
  xhr.open("POST", "ajax/coupons.php", true);

  xhr.onload = function () {
    if (this.responseText == 1) {
      alert("success", "Da them ma giam gia!");
      coupon_form.reset();
      get_coupons();
    } else if (this.responseText == "exists") {
      alert("error", "Ma giam gia da ton tai!");
    } else {
      alert("error", "Khong the them ma giam gia.");
    }
  };

  xhr.send(data);
});

function toggle_coupon(id, value) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "ajax/coupons.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onload = function () {
    if (this.responseText == 1) {
      get_coupons();
    } else {
      alert("error", "Khong the cap nhat.");
    }
  };

  xhr.send("toggle_coupon=1&id=" + id + "&value=" + value);
}

function delete_coupon(id) {
  if (!confirm("Xoa ma giam gia nay?")) {
    return;
  }

  let xhr = new XMLHttpRequest();
  xhr.open("POST", "ajax/coupons.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onload = function () {
    if (this.responseText == 1) {
      alert("success", "Da xoa ma giam gia.");
      get_coupons();
    } else {
      alert("error", "Khong the xoa.");
    }
  };

  xhr.send("delete_coupon=1&id=" + id);
}

window.onload = function () {
  get_coupons();
};
