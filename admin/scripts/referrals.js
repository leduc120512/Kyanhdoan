function get_referrals() {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "ajax/referrals.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onload = function () {
    document.getElementById("referral-data").innerHTML = this.responseText;
  };

  xhr.send("get_referrals=1");
}

function approve_referral(id) {
  if (!confirm("Duyet nguoi gioi thieu nay?")) {
    return;
  }

  let baseInput = document.getElementById("base_" + id);
  let pctInput = document.getElementById("pct_" + id);
  let commissionBase = baseInput ? baseInput.value : "total";
  let commissionPct = pctInput ? pctInput.value : "5";

  let xhr = new XMLHttpRequest();
  xhr.open("POST", "ajax/referrals.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onload = function () {
    if (this.responseText == 1) {
      alert("success", "Da duyet thanh cong!");
      get_referrals();
    } else {
      alert("error", "Khong the duyet.");
    }
  };

  xhr.send(
    "approve_referral=1&id=" +
      id +
      "&commission_base=" +
      encodeURIComponent(commissionBase) +
      "&commission_pct=" +
      encodeURIComponent(commissionPct),
  );
}

function reject_referral(id) {
  if (!confirm("Tu choi nguoi gioi thieu nay?")) {
    return;
  }

  let xhr = new XMLHttpRequest();
  xhr.open("POST", "ajax/referrals.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onload = function () {
    if (this.responseText == 1) {
      alert("success", "Da tu choi yeu cau.");
      get_referrals();
    } else {
      alert("error", "Khong the tu choi.");
    }
  };

  xhr.send("reject_referral=1&id=" + id);
}

window.onload = function () {
  get_referrals();
};
