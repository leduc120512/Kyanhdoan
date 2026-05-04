-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost
-- Thời gian đã tạo: Th12 01, 2024 lúc 07:22 AM
-- Phiên bản máy phục vụ: 10.4.28-MariaDB
-- Phiên bản PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `vietchill`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin_cred`
--

CREATE TABLE `admin_cred` (
  `sr_no` int(11) NOT NULL,
  `admin_name` varchar(150) NOT NULL,
  `admin_pass` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin_cred`
--

INSERT INTO `admin_cred` (`sr_no`, `admin_name`, `admin_pass`) VALUES
(1, 'holden', '12345');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_details`
--

CREATE TABLE `booking_details` (
  `sr_no` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `total_pay` int(11) NOT NULL,
  `room_no` varchar(100) DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  `phonenum` varchar(100) NOT NULL,
  `address` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_details`
--

INSERT INTO `booking_details` (`sr_no`, `booking_id`, `room_name`, `price`, `total_pay`, `room_no`, `user_name`, `phonenum`, `address`) VALUES
(1, 1, 'Phòng Cơ Bản 3', 1200000, 2400000, NULL, 'Hung', '123', 'ad'),
(2, 2, 'Phòng Cao Cấp', 2000000, 4000000, 'a2', 'amey', '123', 'ad'),
(3, 3, 'Phòng Tổng Thống', 10000000, 10000000, NULL, 'amey', '123', 'ad'),
(21, 21, 'Phòng Cơ Bản 3', 1200000, 7200000, NULL, 'Hùng', '12345', 'qweqweqweqwe'),
(22, 22, 'Phòng Cao Cấp', 2000000, 4000000, NULL, 'Hùng', '12345', 'qweqweqweqwe');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_order`
--

CREATE TABLE `booking_order` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `arrival` int(11) NOT NULL DEFAULT 0,
  `refund` int(11) DEFAULT NULL,
  `booking_status` varchar(100) NOT NULL DEFAULT 'pending',
  `order_id` varchar(150) NOT NULL,
  `trans_id` varchar(200) DEFAULT NULL,
  `trans_amt` int(11) DEFAULT NULL,
  `trans_status` varchar(100) NOT NULL DEFAULT 'pending',
  `trans_resp_msg` varchar(200) DEFAULT NULL,
  `payment_method` varchar(20) NOT NULL DEFAULT 'offline',
  `payment_status` varchar(30) NOT NULL DEFAULT 'đang chờ xử lý',
  `total_amt` int(11) NOT NULL DEFAULT 0,
  `discount_amt` int(11) NOT NULL DEFAULT 0,
  `coupon_code` varchar(30) DEFAULT NULL,
  `deposit_amt` int(11) NOT NULL DEFAULT 0,
  `remaining_amt` int(11) NOT NULL DEFAULT 0,
  `referral_id` int(11) DEFAULT NULL,
  `commission_pct` decimal(5,2) NOT NULL DEFAULT 0.00,
  `commission_amt` int(11) NOT NULL DEFAULT 0,
  `commission_status` varchar(20) NOT NULL DEFAULT 'pending',
  `cancelled_at` datetime DEFAULT NULL,
  `cancel_fee` int(11) NOT NULL DEFAULT 0,
  `rate_review` int(11) DEFAULT NULL,
  `deposit_confirmed` int(11) NOT NULL DEFAULT 0 COMMENT 'Trạng thái xác nhận cọc: 1=đã xác nhận, 0=chưa xác nhận',
  `deposit_confirmed_at` datetime DEFAULT NULL COMMENT 'Thời gian xác nhận cọc',
  `full_payment_confirmed` int(11) NOT NULL DEFAULT 0 COMMENT 'Trạng thái thanh toán hết: 1=đã xác nhận, 0=chưa xác nhận',
  `full_payment_confirmed_at` datetime DEFAULT NULL COMMENT 'Thời gian xác nhận thanh toán hết',
  `guest_arrival_confirmed` int(11) NOT NULL DEFAULT 0 COMMENT 'Trạng thái khách đến: 1=đã xác nhận, 0=chưa xác nhận',
  `guest_arrival_confirmed_at` datetime DEFAULT NULL COMMENT 'Thời gian xác nhận khách đến',
  `datentime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_order`
--

INSERT INTO `booking_order` (`booking_id`, `user_id`, `room_id`, `check_in`, `check_out`, `arrival`, `refund`, `booking_status`, `order_id`, `trans_id`, `trans_amt`, `trans_status`, `trans_resp_msg`, `rate_review`, `datentime`) VALUES
(1, 2, 3, '2024-12-12', '2024-12-14', 0, NULL, 'pending', 'ORD_21055700', NULL, 0, 'pending', NULL, NULL, '2024-11-30 01:50:12'),
(2, 2, 3, '2024-12-03', '2024-12-04', 1, NULL, 'booked', 'ORD_24215693', '20220720111212800110168128204225279', 600, 'TXN_SUCCESS', 'Txn Success', NULL, '2024-11-30 02:14:44'),
(3, 2, 3, '2024-12-13', '2024-12-17', 0, 1, 'cancelled', 'ORD_26312547', '20220720111212800110168165603901976', 1800, 'TXN_SUCCESS', 'Txn Success', NULL, '2024-11-30 02:19:00'),
(21, 7, 3, '2024-12-01', '2024-12-07', 0, 0, 'cancelled', 'ORD_74731476', NULL, NULL, 'TXN_SUCCESS', NULL, NULL, '2024-12-01 11:25:29'),
(22, 7, 4, '2024-12-29', '2024-12-31', 0, NULL, 'booked', 'ORD_72382450', NULL, NULL, 'TXN_SUCCESS', NULL, NULL, '2024-12-01 11:32:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(30) NOT NULL,
  `discount_type` varchar(10) NOT NULL DEFAULT 'percent',
  `discount_value` int(11) NOT NULL DEFAULT 0,
  `max_discount` int(11) DEFAULT NULL,
  `min_booking` int(11) NOT NULL DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `datentime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `referral_users`
--

CREATE TABLE `referral_users` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `referral_code` varchar(30) NOT NULL,
  `commission_pct` decimal(5,2) NOT NULL DEFAULT 5.00,
  `commission_base` varchar(20) NOT NULL DEFAULT 'total',
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `approved_at` datetime DEFAULT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carousel`
--

CREATE TABLE `carousel` (
  `sr_no` int(11) NOT NULL,
  `image` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `carousel`
--

INSERT INTO `carousel` (`sr_no`, `image`) VALUES
(4, '1.png'),
(5, '2.png'),
(6, '3.png');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contact_details`
--

CREATE TABLE `contact_details` (
  `sr_no` int(11) NOT NULL,
  `address` varchar(50) NOT NULL,
  `gmap` varchar(100) NOT NULL,
  `pn1` bigint(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fb` varchar(100) NOT NULL,
  `insta` varchar(100) NOT NULL,
  `tw` varchar(100) NOT NULL,
  `iframe` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `contact_details`
--

INSERT INTO `contact_details` (`sr_no`, `address`, `gmap`, `pn1`, `email`, `fb`, `insta`, `tw`, `iframe`) VALUES
(1, 'Quận 12, Hồ Chí Minh, Vietnam', 'https://maps.app.goo.gl/5C8P7KsLbAbSYNc46', 914298300, '2311560406@nttu.edu.vn', 'https://www.facebook.com/DaiHocNguyenTatThanh', '', '', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d501725.4184356321!2d106.3655589082179!3d10.755292868615134!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317529292e8d3dd1%3A0xf15f5aad773c112b!2sHo%20Chi%20Minh%20City%2C%20Vietnam!5e0!3m2!1sen!2s!4v1732960441082!5m2!1sen!2s');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `facilities`
--

CREATE TABLE `facilities` (
  `id` int(11) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `facilities`
--

INSERT INTO `facilities` (`id`, `icon`, `name`, `description`) VALUES
(13, 'IMG_43553.svg', 'Wi-Fi', 'Kết nối Internet tốc độ cao, miễn phí trong toàn bộ khách sạn, giúp bạn dễ dàng làm việc hoặc giải trí trực tuyến.'),
(14, 'IMG_49949.svg', 'Máy Lạnh', 'Hệ thống điều hòa không khí hiện đại, mang lại không gian thoải mái và dễ chịu, phù hợp với mọi điều kiện thời tiết.'),
(15, 'IMG_41622.svg', 'Truyền Hình', 'TV màn hình phẳng với đa dạng kênh giải trí trong nước và quốc tế, đáp ứng nhu cầu thư giãn của khách hàng.'),
(17, 'IMG_47816.svg', 'Spa', 'Dịch vụ spa chuyên nghiệp với liệu trình thư giãn, chăm sóc cơ thể, và phục hồi sức khỏe.'),
(18, 'IMG_96423.svg', 'Máy Sưởi', 'Hệ thống sưởi ấm chất lượng cao, giữ không gian ấm áp, đặc biệt phù hợp vào những ngày lạnh giá.'),
(19, 'IMG_27079.svg', 'Máy Nước Nóng', 'Máy nước nóng tiện lợi, cung cấp nước nóng tức thì, đảm bảo sự thoải mái khi sử dụng phòng tắm.');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `features`
--

CREATE TABLE `features` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `features`
--

INSERT INTO `features` (`id`, `name`) VALUES
(13, 'Phòng Ngủ'),
(14, 'Ban Công'),
(15, 'Nhà Bếp'),
(17, 'Ghế Sofa');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rating_review`
--

CREATE TABLE `rating_review` (
  `sr_no` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` varchar(200) NOT NULL,
  `seen` int(11) NOT NULL DEFAULT 0,
  `datentime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `rating_review`
--

INSERT INTO `rating_review` (`sr_no`, `booking_id`, `room_id`, `user_id`, `rating`, `review`, `seen`, `datentime`) VALUES
(4, 21, 5, 2, 5, 'Dịch vụ tuyệt vời, không gian đẳng cấp và được trang bị đầy đủ tiện nghi hiện đại. Rất phù hợp cho những dịp đặc biệt hoặc nghỉ dưỡng cao cấp.', 1, '2022-08-20 00:22:25'),
(5, 22, 4, 5, 3, 'Chất lượng dịch vụ xuất sắc, phòng rộng rãi, đầy đủ tiện nghi. Không gian sang trọng và thoải mái, rất đáng giá cho kỳ nghỉ.', 1, '2022-08-20 00:22:30'),
(6, 1, 3, 6, 4, 'Tương tự như “Phòng Cơ bản”, nhưng một số chi tiết như ánh sáng hoặc nội thất cần được cải thiện để mang lại trải nghiệm tốt hơn.', 1, '2022-08-20 00:22:34'),
(8, 21, 5, 7, 5, 'Nhân viên phục vụ rất chuyên nghiệp, mang lại cảm giác thoải mái và đáng nhớ cho kỳ nghỉ.', 1, '2022-08-20 00:22:25'),
(9, 22, 3, 8, 4, 'Dịch vụ ổn định, phòng sạch sẽ và gọn gàng. Tuy nhiên, tiện nghi chỉ ở mức cơ bản, phù hợp cho những ai cần chỗ ở ngắn hạn.', 1, '2022-08-20 00:22:34'),
(10, 1, 6, 2, 5, 'Phòng đẳng cấp, dịch vụ chu đáo, không gian sang trọng. Tuy nhiên, giá thành hơi cao so với những gì nhận được.', 1, '2022-08-20 00:22:34'),
(12, 1, 3, 7, 5, 'Rất tốt, đỉnh nóc kịch trần, bay phấp pha phấp phới.\r\nHãy gửi voucher discount về cho tôi vì đã để lại bình luận tốt!', 0, '2024-12-01 11:33:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `area` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `adult` int(11) NOT NULL,
  `children` int(11) NOT NULL,
  `description` varchar(350) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `removed` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `area`, `price`, `quantity`, `adult`, `children`, `description`, `status`, `removed`) VALUES
(1, 'Phòng Cơ Bản 1', 34, 800000, 56, 2, 1, 'Phòng đơn giản, phù hợp với những khách hàng cần chỗ nghỉ ngắn hạn. Được trang bị các tiện nghi cơ bản như giường thoải mái, bàn làm việc nhỏ, và Wi-Fi miễn phí.', 1, 1),
(2, 'Phòng Cơ Bản 2', 40, 1000000, 30, 2, 1, 'Nâng cấp nhẹ so với Phòng Cơ Bản 1, mang đến không gian rộng rãi hơn và thêm các tiện ích như TV màn hình phẳng và minibar.', 1, 1),
(3, 'Phòng Cơ Bản 3', 60, 1200000, 20, 4, 2, 'Phòng cơ bản cao cấp hơn với thiết kế hiện đại, ban công nhỏ hoặc cửa sổ lớn có view thành phố, tạo cảm giác thoáng đãng và thư giãn.', 1, 0),
(4, 'Phòng Cao Cấp', 50, 2000000, 15, 2, 1, 'Không gian rộng rãi với thiết kế sang trọng, phù hợp cho các kỳ nghỉ dài ngày. Được trang bị nội thất cao cấp, phòng tắm riêng với bồn tắm, và các tiện ích như máy pha cà phê và két an toàn.', 1, 0),
(5, 'Phòng Sang Trọng', 50, 3500000, 15, 2, 1, 'Thiết kế đẳng cấp với nội thất tinh tế và các tiện nghi hiện đại. Phòng có không gian sống riêng biệt, ban công hoặc cửa sổ lớn với view đẹp, mang lại trải nghiệm thư giãn hoàn hảo.', 1, 0),
(6, 'Phòng Tổng Thống', 120, 10000000, 5, 6, 3, 'Hạng phòng cao cấp nhất, mang đến sự xa hoa với không gian rộng lớn, nội thất tinh xảo và dịch vụ đặc biệt. Bao gồm phòng khách riêng, phòng ngủ lớn, phòng tắm xa hoa và nhiều tiện nghi VIP như quản gia riêng.', 1, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `room_facilities`
--

CREATE TABLE `room_facilities` (
  `sr_no` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `facilities_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `room_facilities`
--

INSERT INTO `room_facilities` (`sr_no`, `room_id`, `facilities_id`) VALUES
(46, 3, 14),
(47, 3, 15),
(48, 3, 18),
(49, 3, 19),
(50, 4, 14),
(51, 4, 18),
(52, 4, 19),
(53, 5, 13),
(54, 5, 14),
(55, 5, 18),
(56, 6, 13),
(57, 6, 14),
(58, 6, 18),
(59, 6, 19);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `room_features`
--

CREATE TABLE `room_features` (
  `sr_no` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `features_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `room_features`
--

INSERT INTO `room_features` (`sr_no`, `room_id`, `features_id`) VALUES
(31, 3, 13),
(32, 3, 14),
(33, 3, 17),
(34, 4, 13),
(35, 4, 14),
(36, 4, 15),
(37, 5, 13),
(38, 5, 14),
(39, 5, 15),
(40, 6, 13),
(41, 6, 14),
(42, 6, 15);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `room_images`
--

CREATE TABLE `room_images` (
  `sr_no` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `image` varchar(150) NOT NULL,
  `thumb` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `room_images`
--

INSERT INTO `room_images` (`sr_no`, `room_id`, `image`, `thumb`) VALUES
(15, 3, 'IMG_39782.png', 0),
(16, 3, 'IMG_65019.png', 1),
(17, 4, 'IMG_44867.png', 0),
(18, 4, 'IMG_78809.png', 1),
(19, 4, 'IMG_11892.png', 0),
(21, 5, 'IMG_17474.png', 0),
(22, 5, 'IMG_42663.png', 1),
(23, 5, 'IMG_70583.png', 0),
(24, 6, 'IMG_67761.png', 0),
(25, 6, 'IMG_69824.png', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `settings`
--

CREATE TABLE `settings` (
  `sr_no` int(11) NOT NULL,
  `site_title` varchar(50) CHARACTER SET utf8 COLLATE utf8_vietnamese_ci NOT NULL,
  `site_about` varchar(250) CHARACTER SET utf8 COLLATE utf8_vietnamese_ci NOT NULL,
  `referral_commission_default` decimal(5,2) NOT NULL DEFAULT 5.00,
  `shutdown` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `settings`
--

INSERT INTO `settings` (`sr_no`, `site_title`, `site_about`, `referral_commission_default`, `shutdown`) VALUES
(1, 'VietChill', 'Trải nghiệm dịch vụ đặt phòng khách sạn trực tuyến nhanh chóng, tiện lợi với đa dạng lựa chọn tại các điểm đến du lịch nổi tiếng trên khắp Việt Nam. Hãy để hành trình của bạn bắt đầu chỉ với vài cú nhấp chuột!', 5.00, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `team_details`
--

CREATE TABLE `team_details` (
  `sr_no` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `picture` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `team_details`
--

INSERT INTO `team_details` (`sr_no`, `name`, `picture`) VALUES
(16, 'Hùng', 'chill-guy1.png'),
(17, 'Trung', 'chill-guy2.png'),
(18, 'Huy', 'chill-guy3.png'),
(19, 'Dũng', 'chill-guy4.png'),
(20, 'Đăng', 'chill-guy5.png'),
(21, 'Đạt', 'chill-guy6.png'),
(22, 'Hưng', 'chill-guy7.png'),
(23, 'Hiếu', 'chill-guy8.png');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_cred`
--

CREATE TABLE `user_cred` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `address` varchar(120) NOT NULL,
  `phonenum` varchar(100) NOT NULL,
  `pincode` int(11) NOT NULL,
  `dob` date NOT NULL,
  `profile` varchar(100) NOT NULL DEFAULT 'chill-guy.png',
  `password` varchar(200) NOT NULL,
  `is_verified` int(11) NOT NULL DEFAULT 0,
  `token` varchar(200) DEFAULT NULL,
  `t_expire` date DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `datentime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_cred`
--

INSERT INTO `user_cred` (`id`, `name`, `email`, `address`, `phonenum`, `pincode`, `dob`, `profile`, `password`, `is_verified`, `token`, `t_expire`, `status`, `datentime`) VALUES
(2, 'Trung', 'trung@gmail.com', 'ad', '123', 123324, '2022-06-12', 'chill-guy2.png', '12345', 1, NULL, NULL, 1, '2024-11-30 16:05:59'),
(5, 'Huy', 'huy@gmail.com', 'asd', '1234', 123, '2005-12-30', 'chill-guy3.png', '12345', 1, '24ffd287a4c2eda5f2b424be2824f997', NULL, 1, '2024-11-30 02:37:19'),
(6, 'Hiếu', 'hieu@gmail.com', 'asd', '1123', 123, '2005-12-22', 'chill-guy6.png', '12345', 1, 'ef6dc7ba39cf4bf844244d3ef927a3e7', NULL, 1, '2024-11-30 02:40:42'),
(7, 'Hùng', 'hung@gmail.com', 'qweqweqweqwe', '12345', 123, '1995-12-28', 'chill-guy1.png', '12345', 0, '5c9f04397ff3e693f7cbfccea1044483', NULL, 1, '2024-11-30 02:42:37'),
(8, 'Đạt', 'dat@gmail.com', 'a', '12', 1, '2005-12-01', 'chill-guy5.png', '12345', 0, '250dd45640f7d810313b27e758a267af', NULL, 1, '2024-11-30 02:55:39'),
(9, 'trung', '123123@asdasdasd', '1123123123', '1231111111111', 123, '2222-02-12', 'chill-guy.png', '123', 0, NULL, NULL, 1, '2024-12-01 11:56:05'),
(11, 'test', 'test@gmail.com', '123123', '1231231455555', 123123, '1111-11-11', 'chill-guy.png', '12345', 0, NULL, NULL, 1, '2024-12-01 13:05:25');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_queries`
--

CREATE TABLE `user_queries` (
  `sr_no` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` varchar(500) NOT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  `seen` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_queries`
--

INSERT INTO `user_queries` (`sr_no`, `name`, `email`, `subject`, `message`, `datentime`, `seen`) VALUES
(11, 'Hung', 'hung@gmail.com', 'Tôi muốn đặt phòng', 'Cần hỗ trợ đặt phòng Tổng Thống.', '2024-11-29 00:00:00', 1),
(13, 'Trung', 'trung@gmail.com', 'Yêu cầu hoàn tiền', 'Cần hỗ trợ hoàn tiền do huỷ đột xuất.', '2024-12-06 10:10:48', 1);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin_cred`
--
ALTER TABLE `admin_cred`
  ADD PRIMARY KEY (`sr_no`);

--
-- Chỉ mục cho bảng `booking_details`
--
ALTER TABLE `booking_details`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Chỉ mục cho bảng `booking_order`
--
ALTER TABLE `booking_order`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `referral_id` (`referral_id`);

--
-- Chỉ mục cho bảng `carousel`
--
ALTER TABLE `carousel`
  ADD PRIMARY KEY (`sr_no`);

--
-- Chỉ mục cho bảng `contact_details`
--
ALTER TABLE `contact_details`
  ADD PRIMARY KEY (`sr_no`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `referral_users`
--
ALTER TABLE `referral_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `referral_code` (`referral_code`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `rating_review`
--
ALTER TABLE `rating_review`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `room_facilities`
--
ALTER TABLE `room_facilities`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `facilities id` (`facilities_id`),
  ADD KEY `room id` (`room_id`);

--
-- Chỉ mục cho bảng `room_features`
--
ALTER TABLE `room_features`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `features id` (`features_id`),
  ADD KEY `rm id` (`room_id`);

--
-- Chỉ mục cho bảng `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `room_id` (`room_id`);

--
-- Chỉ mục cho bảng `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`sr_no`);

--
-- Chỉ mục cho bảng `team_details`
--
ALTER TABLE `team_details`
  ADD PRIMARY KEY (`sr_no`);

--
-- Chỉ mục cho bảng `user_cred`
--
ALTER TABLE `user_cred`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `user_queries`
--
ALTER TABLE `user_queries`
  ADD PRIMARY KEY (`sr_no`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin_cred`
--
ALTER TABLE `admin_cred`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `booking_details`
--
ALTER TABLE `booking_details`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `booking_order`
--
ALTER TABLE `booking_order`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `carousel`
--
ALTER TABLE `carousel`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `contact_details`
--
ALTER TABLE `contact_details`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `features`
--
ALTER TABLE `features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `referral_users`
--
ALTER TABLE `referral_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `rating_review`
--
ALTER TABLE `rating_review`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `room_facilities`
--
ALTER TABLE `room_facilities`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT cho bảng `room_features`
--
ALTER TABLE `room_features`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT cho bảng `room_images`
--
ALTER TABLE `room_images`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `settings`
--
ALTER TABLE `settings`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `team_details`
--
ALTER TABLE `team_details`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `user_cred`
--
ALTER TABLE `user_cred`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `user_queries`
--
ALTER TABLE `user_queries`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `booking_details`
--
ALTER TABLE `booking_details`
  ADD CONSTRAINT `booking_details_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking_order` (`booking_id`);

--
-- Các ràng buộc cho bảng `booking_order`
--
ALTER TABLE `booking_order`
  ADD CONSTRAINT `booking_order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_cred` (`id`),
  ADD CONSTRAINT `booking_order_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Các ràng buộc cho bảng `rating_review`
--
ALTER TABLE `rating_review`
  ADD CONSTRAINT `rating_review_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking_order` (`booking_id`),
  ADD CONSTRAINT `rating_review_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `rating_review_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user_cred` (`id`);

--
-- Các ràng buộc cho bảng `room_facilities`
--
ALTER TABLE `room_facilities`
  ADD CONSTRAINT `facilities id` FOREIGN KEY (`facilities_id`) REFERENCES `facilities` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `room id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON UPDATE NO ACTION;

--
-- Các ràng buộc cho bảng `room_features`
--
ALTER TABLE `room_features`
  ADD CONSTRAINT `features id` FOREIGN KEY (`features_id`) REFERENCES `features` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `rm id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON UPDATE NO ACTION;

--
-- Các ràng buộc cho bảng `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
