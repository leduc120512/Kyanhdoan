function get_bookings() {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "ajax/booking_order.php", true);

  xhr.onload = function () {
    document.getElementById("booking-data").innerHTML = this.responseText;
  };

  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.send("get_bookings=1");
}

function confirm_deposit(id) {
  if (confirm("Xác nhận khách hàng đã cọc tiền?")) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/booking_order.php", true);

    xhr.onload = function () {
      if (this.responseText == 1) {
        alert("Xác nhận cọc thành công!");
        get_bookings();
      } else {
        alert("Lỗi! Không thể xác nhận.");
      }
    };

    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("confirm_deposit=1&id=" + id);
  }
}

function confirm_full_payment(id) {
  if (confirm("Xác nhận khách hàng đã thanh toán hết tiền?")) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/booking_order.php", true);

    xhr.onload = function () {
      if (this.responseText == 1) {
        alert("Xác nhận thanh toán thành công!");
        get_bookings();
      } else {
        alert("Lỗi! Không thể xác nhận.");
      }
    };

    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("confirm_full_payment=1&id=" + id);
  }
}

function confirm_guest_arrival(id) {
  if (confirm("Xác nhận khách hàng đã tới nhận phòng?")) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/booking_order.php", true);

    xhr.onload = function () {
      if (this.responseText == 1) {
        alert("Xác nhận khách đến thành công!");
        get_bookings();
      } else {
        alert("Lỗi! Không thể xác nhận.");
      }
    };

    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("confirm_guest_arrival=1&id=" + id);
  }
}

function confirm_booking(id) {
  if (confirm("Xác nhận thanh toán cho đơn này?")) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/booking_order.php", true);

    xhr.onload = function () {
      if (this.responseText == 1) {
        alert("Xác nhận thành công!");
        get_bookings();
      } else {
        alert("Lỗi! Không thể xác nhận.");
      }
    };

    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("confirm_booking=1&id=" + id);
  }
}

window.onload = function () {
  get_bookings();
};
