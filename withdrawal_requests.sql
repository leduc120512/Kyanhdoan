-- Bảng yêu cầu rút tiền (cọc + hoa hồng)
CREATE TABLE IF NOT EXISTS `withdrawal_requests` (
  `id`            INT(11) NOT NULL AUTO_INCREMENT,
  `user_id`       INT(11) NOT NULL,
  `type`          ENUM('refund','commission') NOT NULL COMMENT 'refund=rút cọc, commission=rút hoa hồng',
  `ref_id`        INT(11) DEFAULT NULL COMMENT 'booking_id nếu type=refund, referral_users.id nếu type=commission',
  `amount`        DECIMAL(15,2) NOT NULL,
  `bank_name`     VARCHAR(100) NOT NULL,
  `bank_account`  VARCHAR(50) NOT NULL,
  `account_name`  VARCHAR(100) NOT NULL,
  `status`        ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_note`    VARCHAR(255) DEFAULT NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type_status` (`type`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
