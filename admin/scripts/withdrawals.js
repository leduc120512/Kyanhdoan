let rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));

function load_pending() {
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/withdrawals.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        let res = JSON.parse(this.responseText);
        document.getElementById('pending-data').innerHTML = res.html;
        let badge = document.getElementById('pending-count');
        badge.textContent = res.count > 0 ? res.count : '';
        badge.style.display = res.count > 0 ? '' : 'none';
    };
    xhr.send('get_pending=1');
}

function load_done() {
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/withdrawals.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        document.getElementById('done-data').innerHTML = this.responseText;
    };
    xhr.send('get_done=1');
}

function approve_withdrawal(id) {
    if (!confirm('Xác nhận đã chuyển khoản và duyệt yêu cầu này?')) return;

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/withdrawals.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.responseText == 1) {
            alert('success', 'Đã duyệt yêu cầu rút tiền!');
            load_pending();
        } else {
            alert('error', 'Không thể duyệt, thử lại.');
        }
    };
    xhr.send('approve_withdrawal=1&id=' + id);
}

function open_reject(id) {
    document.getElementById('reject-id').value = id;
    document.getElementById('reject-note').value = '';
    rejectModal.show();
}

function confirm_reject() {
    let id   = document.getElementById('reject-id').value;
    let note = document.getElementById('reject-note').value;

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/withdrawals.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        rejectModal.hide();
        if (this.responseText == 1) {
            alert('success', 'Đã từ chối yêu cầu.');
            load_pending();
        } else {
            alert('error', 'Không thể từ chối, thử lại.');
        }
    };
    xhr.send('reject_withdrawal=1&id=' + id + '&note=' + encodeURIComponent(note));
}

// Load khi tab chuyển
document.querySelectorAll('#withdrawTab button').forEach(btn => {
    btn.addEventListener('shown.bs.tab', function(e) {
        if (e.target.dataset.bsTarget == '#tab-done') load_done();
    });
});

// Load lần đầu
load_pending();
