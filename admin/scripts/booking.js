window.onload = function(){
    get_bookings();
}

function get_bookings(){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/booking.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function(){
        document.getElementById('booking-data').innerHTML = this.responseText;
    }

    xhr.send("get_bookings=1");
}

function confirm_payment(id){
    if(confirm("Xác nhận khách đã thanh toán?")){
        
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/booking.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function(){
            if(this.responseText == "1"){
                alert("Xác nhận thành công!");
                get_bookings();
            } else {
                alert("Xác nhận thất bại!");
            }
        }

        xhr.send("confirm_payment=1&id=" + id);
    }
}
