<?php

require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if (isset($_POST['get_general'])) {
  $q = "SELECT * FROM `settings` WHERE `sr_no` = ?";
  $values = [1];
  $res = select($q, $values, "i");
  $data = mysqli_fetch_assoc($res);
  $json_data = json_encode($data);
  echo $json_data;
}

if (isset($_POST['upd_general'])) {
  $frm_data = filteration($_POST);
  $commission_default = isset($frm_data['referral_commission_default']) ? (float)$frm_data['referral_commission_default'] : 5.00;
  if ($commission_default < 0) {
    $commission_default = 0;
  }
  if ($commission_default > 100) {
    $commission_default = 100;
  }

  $q = "UPDATE `settings` SET `site_title` = ?, `site_about` = ?, `referral_commission_default` = ? WHERE `sr_no` = ?";
  $values = [$frm_data['site_title'], $frm_data['site_about'], $commission_default, 1];
  $res = update($q, $values, 'ssdi');
  echo $res;
}

if (isset($_POST['upd_shutdown'])) {
  $frm_data = $_POST['upd_shutdown'];

  $q = "UPDATE `settings` SET `shutdown` = ? WHERE `sr_no` = ?";
  $values = [$frm_data, 1];
  $res = update($q, $values, 'ii');
  echo $res;
}
