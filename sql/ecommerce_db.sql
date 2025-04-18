-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th4 18, 2025 lúc 02:16 PM
-- Phiên bản máy phục vụ: 8.4.3
-- Phiên bản PHP: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `ecommerce_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text,
  `avatar` varchar(255) DEFAULT NULL,
  `two_factor_code` varchar(50) DEFAULT NULL,
  `role_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `name`, `created_at`, `phone`, `email`, `address`, `avatar`, `two_factor_code`, `role_id`) VALUES
(1, 'admin', 'admin123', 'Thắng Rai', '2025-04-14 12:16:16', '0914476792', 'badaotulong123@gmail.com', '59 Đường 30 Tháng 4, Phường Tân Thành, Quận Tân Phú, TP.HCM', 'uploads/avatars/admin_1_1744689854.png', '2FA', 1),
(2, 'thangwebso', 'Ser3HeQ8', 'Thắng Raiy', '2025-04-15 06:55:40', '0914476791', 'thangwebso@gmail.com', 'Hồ Chí Minh', 'uploads/avatars/admin_1744700140.jpg', '2FA', 2),
(6, 'tram', 'tram0123', 'Trâm', '2025-04-18 03:52:16', '0397970507', 'nguyenthingoctram09102003@gmail.com', 'Thị trấn', 'uploads/avatars/admin_1744948336.png', '2FA', 1),
(7, 'vanthang', 'Hrb1Cd9d', 'Lê Văn Thắng', '2025-04-18 04:17:33', '0914476791', 'vanthang@webso.vn', 'thôn 3 số nhà 161\r\nXã khuê ngọc điền', 'uploads/avatars/admin_1744949852.jpg', '2FA', 3),
(8, 'loi', 'loi0123', 'Lê Văn Bá Lợi', '2025-04-18 08:27:26', '0914476793', 'levanthang200603@gmail.com', 'thôn 3 số nhà 161\r\nXã khuê ngọc điền', 'uploads/avatars/admin_1744964845.jpg', '2FA', 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin_logins`
--

CREATE TABLE `admin_logins` (
  `id` int NOT NULL,
  `admin_id` int NOT NULL,
  `admin_name` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `login_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `admin_logins`
--

INSERT INTO `admin_logins` (`id`, `admin_id`, `admin_name`, `ip_address`, `login_time`) VALUES
(1, 1, 'Admin User', '::1', '2025-04-15 03:42:28'),
(2, 1, 'Admin User', '::1', '2025-04-15 04:12:58'),
(3, 1, 'Thắng Rai', '::1', '2025-04-15 04:20:21'),
(4, 1, 'Thắng Rai', '::1', '2025-04-15 07:58:01'),
(5, 2, 'Thắng Raiy', '::1', '2025-04-15 07:59:51'),
(6, 2, 'Thắng Raiy', '::1', '2025-04-15 08:00:20'),
(7, 2, 'Thắng Raiy', '::1', '2025-04-15 08:01:04'),
(8, 2, 'Thắng Raiy', '::1', '2025-04-15 08:03:17'),
(9, 2, 'Thắng Raiy', '::1', '2025-04-15 08:04:52'),
(10, 1, 'Thắng Rai', '::1', '2025-04-15 08:06:50'),
(11, 1, 'Thắng Rai', '::1', '2025-04-15 08:06:58'),
(12, 1, 'Thắng Rai', '::1', '2025-04-15 08:09:40'),
(13, 1, 'Thắng Rai', '::1', '2025-04-15 12:27:42'),
(14, 1, 'Thắng Rai', '::1', '2025-04-17 08:51:14'),
(15, 1, 'Thắng Rai', '::1', '2025-04-17 13:31:24'),
(16, 1, 'Thắng Rai', '::1', '2025-04-17 15:01:28'),
(17, 1, 'Thắng Rai', '::1', '2025-04-18 02:51:21'),
(18, 2, 'Thắng Raiy', '::1', '2025-04-18 03:57:47'),
(19, 1, 'Thắng Rai', '::1', '2025-04-18 04:11:43'),
(20, 6, 'Trâm', '::1', '2025-04-18 04:15:06'),
(21, 7, 'Lê Văn Thắng', '::1', '2025-04-18 04:17:51'),
(22, 1, 'Thắng Rai', '::1', '2025-04-18 04:21:05'),
(23, 1, 'Thắng Rai', '::1', '2025-04-18 06:11:15'),
(24, 1, 'Thắng Rai', '::1', '2025-04-18 06:11:33'),
(25, 7, 'Lê Văn Thắng', '::1', '2025-04-18 06:55:16'),
(26, 1, 'Thắng Rai', '::1', '2025-04-18 06:55:46'),
(27, 8, 'Lê Văn Bá Lợi', '::1', '2025-04-18 08:28:39'),
(28, 2, 'Thắng Raiy', '::1', '2025-04-18 08:29:34'),
(29, 2, 'Thắng Raiy', '::1', '2025-04-18 08:41:28'),
(30, 1, 'Thắng Rai', '::1', '2025-04-18 08:42:30'),
(31, 7, 'Lê Văn Thắng', '::1', '2025-04-18 08:54:30'),
(32, 1, 'Thắng Rai', '::1', '2025-04-18 11:53:45');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` int DEFAULT '0',
  `link` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_id`, `link`, `status`, `order`, `created_at`, `updated_at`) VALUES
(11, 'Trang chủ', 0, 'http://localhost/2/public/', 1, 0, '2025-04-15 09:09:34', '2025-04-15 09:13:13'),
(12, 'Giới thiệu', 0, 'http://localhost/2/public/gioithieu.php', 1, 1, '2025-04-15 09:10:17', '2025-04-18 13:46:59'),
(14, 'Sản phẩm', 0, 'http://localhost/2/public/pages/product.php', 1, 4, '2025-04-15 09:45:23', '2025-04-18 07:01:18'),
(15, 'Liên hệ', 0, 'http://localhost/2/public/pages/lienhe.php', 1, 5, '2025-04-18 07:01:51', '2025-04-18 07:02:03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contacts`
--

CREATE TABLE `contacts` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `message`, `created_at`) VALUES
(2, 'NGUYỄN THỊ NGỌC TRÂM', 'nguyenthingoctram09102003@gmail.com', '0397970507', 'cần hỗ trợ', '2025-04-18 08:01:14'),
(3, 'Judah Huff', 'voxogezeh@mailinator.com', '0914476790', 'Eum totam veniam cu', '2025-04-18 08:01:49'),
(4, 'Jermaine Hicks', 'bisoxi@mailinator.com', '0914476791', 'Ex tempora ad fugit', '2025-04-18 08:02:12'),
(5, 'Brett Gallagher', 'seder@mailinator.com', '0914476793', 'Ad cum tempora vero', '2025-04-18 08:02:26'),
(6, 'Vivien Kramer', 'muhufykaki@mailinator.com', '0914476794', 'Consectetur id eius', '2025-04-18 08:02:35'),
(7, 'Shay Boyle', 'tyhefyz@mailinator.com', '0914476795', 'Quis exercitationem', '2025-04-18 08:02:42');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contacts_info`
--

CREATE TABLE `contacts_info` (
  `id` int NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `working_hours` varchar(100) NOT NULL,
  `map_iframe` text,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `contacts_info`
--

INSERT INTO `contacts_info` (`id`, `address`, `phone`, `email`, `working_hours`, `map_iframe`, `updated_at`) VALUES
(1, '161 Thôn 3, Xã Khuê Ngọc Điền, Huyện Krông Bông, Tỉnh Đắk Lắk, Việt Nam', '0914476792', 'vanthang@webso.vn', 'Thứ Hai - Thứ Sáu: 9:00 - 17:00', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3894.832426986445!2d108.30534958539566!3d12.527257892891637!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317193b603af7f3b%3A0x71c85315931a80b0!2zVGjhuq9uZyBSYWk!5e0!3m2!1svi!2s!4v1744961247747!5m2!1svi!2s\" width=\"100%\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '2025-04-18 07:31:03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contact_info`
--

CREATE TABLE `contact_info` (
  `id` int NOT NULL,
  `type` varchar(50) NOT NULL,
  `value` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `bg_color` varchar(20) DEFAULT NULL,
  `text_color` varchar(20) DEFAULT NULL,
  `opacity` float DEFAULT '1',
  `gradient` varchar(255) DEFAULT NULL,
  `font_size` int DEFAULT '16',
  `font_weight` varchar(20) DEFAULT 'normal',
  `order` int DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `contact_info`
--

INSERT INTO `contact_info` (`id`, `type`, `value`, `link`, `icon`, `bg_color`, `text_color`, `opacity`, `gradient`, `font_size`, `font_weight`, `order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'hotline', '0914476792', 'tel:0914476792', '67fe4290d4ef3-hotline-gif.gif', NULL, NULL, 1, NULL, 16, 'normal', 0, 1, '2025-04-15 11:27:12', '2025-04-15 11:27:12'),
(2, 'zalo', '0914476792', 'https://zalo.me/0914476792', '67fe47fa19b5f-zalo-icon.gif', NULL, NULL, 1, NULL, 16, 'normal', 0, 1, '2025-04-15 11:27:43', '2025-04-15 11:50:18'),
(3, 'email', 'thangwebso@gmail.com', 'mailto:thangwebso@gmail.com', '67fe48753d781-giphy.gif', NULL, NULL, 1, NULL, 16, 'normal', 0, 1, '2025-04-15 11:44:34', '2025-04-15 11:52:21');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

CREATE TABLE `coupons` (
  `id` int NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `max_uses` int DEFAULT NULL,
  `used_count` int DEFAULT '0',
  `expiry_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `discount_type`, `discount_value`, `max_uses`, `used_count`, `expiry_date`, `created_at`, `updated_at`) VALUES
(1, 'SALE10', 'percentage', 10.00, 100, 0, '2025-12-31 23:59:59', '2025-04-18 12:43:25', '2025-04-18 12:43:25'),
(2, 'OFF500K', 'fixed', 500000.00, 50, 0, '2025-06-30 23:59:59', '2025-04-18 12:43:25', '2025-04-18 12:43:25');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customers`
--

CREATE TABLE `customers` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `province` varchar(100) NOT NULL,
  `district` varchar(100) NOT NULL,
  `ward` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `password` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `province`, `district`, `ward`, `created_at`, `password`) VALUES
(1, 'Test Customer', 'test@example.com', NULL, NULL, '', '', '', '2025-04-14 12:51:16', '$2y$10$abc123...'),
(2, 'NGUYỄN THỊ NGỌC TRÂM', 'nguyenthingoctram09102003@gmail.com', '0397970507', '9', 'Thành phố Hà Nội', 'Quận Ba Đình', 'Phường Trúc Bạch', '2025-04-17 15:13:02', ''),
(3, 'Lê Văn Thắng', 'thangwebso@gmail.com', '0914476792', 'thôn 3 số nhà 161', 'Tỉnh Đắk Lắk', 'Huyện Krông Pắc', 'Xã Vụ Bổn', '2025-04-17 15:26:33', ''),
(4, 'Lê Văn Thắng 1', 'phimdankhoi@gmail.com', '0914476791', 'thôn 3 số nhà 161', 'Tỉnh Đắk Lắk', 'Huyện Krông Năng', 'Xã Tam Giang', '2025-04-17 15:42:15', ''),
(5, 'Lê Văn Thắng', 'nguyenthingoctram9102003@gmail.com', '0914476790', 'thôn 3 số nhà 161', 'Tỉnh Đắk Lắk', 'Huyện Krông Pắc', 'Xã Ea Hiu', '2025-04-17 15:45:55', ''),
(6, 'NGUYỄN THỊ NGỌC TRÂM', 'badaotulong123@gmail.com', '0397970507', '9', 'Tỉnh Yên Bái', 'Huyện Trạm Tấu', 'Xã Làng Nhì', '2025-04-17 15:51:11', ''),
(7, 'Lê Văn Thắng', 'levanthang200603@gmail.com', '0914476792', 'thôn 3 số nhà 161', 'Tỉnh Đắk Lắk', 'Huyện Ea Kar', 'Xã Cư ELang', '2025-04-18 06:29:15', ''),
(8, 'Hayfa Mercer', 'lemufat@mailinator.com', '0123454213', 'Atque nisi voluptati', 'Tỉnh Kon Tum', 'Thành phố Kon Tum', 'Xã Đăk Năng', '2025-04-18 06:32:59', ''),
(9, 'Yoshio Skinner', 'kusujoja@mailinator.com', '0123456783', 'At sunt modi similiq', 'Tỉnh Ninh Bình', 'Huyện Nho Quan', 'Xã Lạc Vân', '2025-04-18 12:05:39', ''),
(10, 'Joelle Whitley', 'wapyt@mailinator.com', '01234567833', 'Alias officia cum ne', 'Tỉnh Kiên Giang', 'Huyện Giồng Riềng', 'Xã Ngọc Chúc', '2025-04-18 12:08:49', ''),
(11, 'Lê Văn Thắng', 'badaotsulong123@gmail.com', '0914476792', 'thôn 3 số nhà 161', 'Tỉnh Đắk Lắk', 'Thị xã Buôn Hồ', 'Phường Thống Nhất', '2025-04-18 12:11:49', ''),
(12, 'Lê Văn Thắng', 'zufugitu@mailinator.com', '0914476792', 'thôn 3 số nhà 161', 'Tỉnh Hà Nam', 'Huyện Thanh Liêm', 'Xã Thanh Tâm', '2025-04-18 12:14:01', ''),
(13, 'Lê Văn Thắng', 'badaotulonsg123@gmail.com', '0914476792', 'thôn 3 số nhà 161', 'Tỉnh Đắk Lắk', 'Huyện Ea Kar', 'Xã Ea Kmút', '2025-04-18 12:25:03', ''),
(14, 'Lê Văn Thắng', 'vanthang@webso.vn', '0914476792', 'thôn 3 số nhà 161', 'Tỉnh Đắk Lắk', 'Thành phố Buôn Ma Thuột', 'Phường Tân Hòa', '2025-04-18 12:32:59', ''),
(15, 'Lê Văn Thắng', 'badsaotulong123@gmail.com', '0914476792', 'thôn 3 số nhà 161', 'Tỉnh Đắk Lắk', 'Huyện Krông Pắc', 'Xã Hòa Đông', '2025-04-18 12:50:20', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `logos`
--

CREATE TABLE `logos` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `logos`
--

INSERT INTO `logos` (`id`, `title`, `image`, `link`, `status`, `created_at`) VALUES
(1, 'logo', 'uploads/logos/logo_1_1744723326.jpg', NULL, 1, '2025-04-15 08:26:13');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `note` text,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cod',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `total_amount`, `status`, `note`, `payment_method`, `created_at`) VALUES
(1, 1, 1000000.50, 'delivered', NULL, 'cod', '2025-03-01 03:00:00'),
(2, 1, 2000000.75, 'delivered', NULL, 'cod', '2025-04-01 05:00:00'),
(3, 1, 5000000.00, 'delivered', NULL, 'cod', '2025-04-15 01:00:00'),
(4, 1, 7500000.25, 'delivered', NULL, 'cod', '2025-04-14 02:00:00'),
(5, 1, 100000.50, 'pending', NULL, 'cod', '2025-03-01 03:00:00'),
(6, 1, 200.75, 'delivered', NULL, 'cod', '2025-04-01 05:00:00'),
(7, 1, 30.00, 'delivered', NULL, 'cod', '2025-04-10 08:00:00'),
(8, 1, 5321243.00, 'delivered', NULL, 'cod', '2025-04-17 14:49:55'),
(9, 1, 800000.00, 'pending', NULL, 'cod', '2025-04-17 14:52:43'),
(10, 1, 800000.00, 'pending', NULL, 'cod', '2025-04-17 14:53:55'),
(11, 1, 1500000.00, 'pending', NULL, 'cod', '2025-04-17 14:55:49'),
(12, 2, 800000.00, 'cancelled', NULL, 'cod', '2025-04-17 15:13:02'),
(13, 2, 800000.00, 'delivered', 'nhanh', 'cod', '2025-04-17 15:18:38'),
(14, 3, 800000.00, 'delivered', 'giao 24/7', 'cod', '2025-04-17 15:26:33'),
(15, 4, 21312430.00, 'pending', 'cần gấp', 'cod', '2025-04-17 15:42:15'),
(16, 6, 1500000.00, 'pending', 's', 'cod', '2025-04-17 15:51:35'),
(17, 7, 2931243.00, 'delivered', 'cần gấp trong ngày', 'cod', '2025-04-18 06:29:15'),
(18, 8, 88800000.00, 'pending', 'Harum et iste ut eve', 'cod', '2025-04-18 06:32:59'),
(19, 9, 1690000.00, 'pending', 'Alias et lorem conse', 'cod', '2025-04-18 12:05:39'),
(20, 10, 1690000.00, 'pending', 'Eos consectetur vol', 'cod', '2025-04-18 12:08:49'),
(21, 11, 800000.00, 'pending', 'Et sed dolore cumque', 'cod', '2025-04-18 12:11:49'),
(22, 12, 800000.00, 'pending', 'Velit dolorem dolor', 'cod', '2025-04-18 12:14:01'),
(23, 2, 90000.00, 'pending', 'Nostrum anim ea cumq', 'cod', '2025-04-18 12:16:29'),
(24, 2, 800000.00, 'pending', 'fdsf', 'cod', '2025-04-18 12:17:26'),
(25, 6, 800000.00, 'delivered', 's', 'cod', '2025-04-18 12:22:28'),
(26, 14, 800000.00, 'delivered', 'nhanh', 'bank_transfer', '2025-04-18 12:32:59'),
(27, 7, 800000.00, 'delivered', 'nhanh lẹ', 'bank_transfer', '2025-04-18 12:38:31'),
(28, 2, 800000.00, 'delivered', '7h39', 'cod', '2025-04-18 12:39:42'),
(29, 15, 800000.00, 'delivered', 'á', 'cod', '2025-04-18 12:50:20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 8, 6, 1, 800000.00),
(2, 8, 8, 1, 800000.00),
(3, 8, 7, 1, 90000.00),
(4, 8, 5, 1, 1500000.00),
(5, 8, 3, 1, 2131243.00),
(6, 9, 6, 1, 800000.00),
(7, 10, 6, 1, 800000.00),
(8, 11, 5, 1, 1500000.00),
(9, 12, 6, 1, 800000.00),
(10, 13, 6, 1, 800000.00),
(11, 14, 6, 1, 800000.00),
(12, 15, 3, 10, 2131243.00),
(13, 16, 5, 1, 1500000.00),
(14, 17, 6, 1, 800000.00),
(15, 17, 3, 1, 2131243.00),
(16, 18, 8, 111, 800000.00),
(17, 19, 4, 1, 1690000.00),
(18, 20, 4, 1, 1690000.00),
(19, 21, 6, 1, 800000.00),
(20, 22, 6, 1, 800000.00),
(21, 23, 7, 1, 90000.00),
(22, 24, 6, 1, 800000.00),
(23, 25, 6, 1, 800000.00),
(24, 26, 6, 1, 800000.00),
(25, 27, 6, 1, 800000.00),
(26, 28, 6, 1, 800000.00),
(27, 29, 6, 1, 800000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `category_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `stock` int NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text,
  `original_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `current_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `stock`, `image`, `created_at`, `content`, `original_price`, `current_price`, `is_active`) VALUES
(3, 1, 'Mẫu Giày 05', 'Sample description for product.', 50352, 'uploads/products/mau-giay-020-0.png', '2025-04-14 23:41:03', 'This is a sample product description.', 3242421.00, 2131243.00, 1),
(4, 1, 'Mẫu Giày 06', 'Sample description for product.', 30256, 'uploads/products/mau-giay-019-3c-0.jpg', '2025-04-14 23:41:03', 'Another product with great features.', 2000000.00, 1690000.00, 1),
(5, 1, 'Mẫu Giày 04', 'Sample description for product.', 2354, 'uploads/products/mau-giay-015-0.png', '2025-04-15 00:01:16', 'This is a sample product description.', 1900000.00, 1500000.00, 1),
(6, 1, 'Mẫu Giày 03', 'Sample description for product.', 50143, 'uploads/products/mau-giay-015-0.png', '2025-04-15 00:01:29', 'This is a sample product description.fd', 1000000.00, 800000.00, 1),
(7, 0, 'Mẫu Giày 02', 'Sample description for product.', 2435, 'uploads/products/mau-giay-032-0.png', '2025-04-15 00:01:47', 'This is a sample product description.', 100000.00, 90000.00, 1),
(8, 0, 'Mẫu Giày 01', '- Giày Adidas Stan Smith x HER Bounty Sneakers giày thể thao nữ trrắng FW2524\r\n- Hàng Chính Hãng\r\n- Cam kết chính hãng 100% nhập từ ADIDAS US, UK, JP - Fake đền x10.\r\n- GIÁ RẺ hơn các shop khác 15-20% - full box, tem, tag, giấy gói chính hãng.', 214, 'uploads/products/mau-giay-015-0.png', '2025-04-15 00:04:59', 'Sản phẩm Giày thể thao của Shop đã được kiểm tra trước khi đóng gói và có video quay lại quá trình đóng hàng và gửi hàng cho khách nguyên vẹn, không có lỗi. Khách hàng lưu ý khi nhận hàng thì quay clip lại quá trình kiểm hàng để tránh trường hợp nhận sai hàng hoặc hàng lỗi. ', 1000000.00, 800000.00, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'super_admin', 'Toàn quyền, truy cập mọi chức năng'),
(2, 'staff', 'Chỉ được xem và xử lý đơn hàng và khách hàng'),
(3, 'content_manager', 'Quản lý sản phẩm và bài viết');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` text,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`, `updated_at`) VALUES
(1, 'site_status', '1', '2025-04-15 11:22:56'),
(3, 'default_font_size', '16', '2025-04-15 11:10:27'),
(4, 'default_font_weight', 'normal', '2025-04-15 11:10:34'),
(10, 'scroll_top', '1', '2025-04-18 07:31:45'),
(13, 'columns_375', '2', '2025-04-15 13:56:12'),
(14, 'columns_425', '3', '2025-04-15 13:56:12'),
(15, 'columns_768', '4', '2025-04-15 13:47:14'),
(16, 'columns_1200', '5', '2025-04-15 13:47:14'),
(17, 'columns_max', '5', '2025-04-15 14:36:16'),
(88, 'favicon', '6801ef7d9dc18-github.png', '2025-04-18 06:21:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `slides`
--

CREATE TABLE `slides` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text,
  `link` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `slides`
--

INSERT INTO `slides` (`id`, `title`, `image`, `description`, `link`, `status`, `created_at`) VALUES
(1, 'sl1', 'uploads/slides/slide_1744704976.jpg', NULL, NULL, 1, '2025-04-15 08:16:16'),
(2, 'sl2', 'uploads/slides/slide_1744708545.jpg', NULL, NULL, 1, '2025-04-15 09:15:45');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_role` (`role_id`);

--
-- Chỉ mục cho bảng `admin_logins`
--
ALTER TABLE `admin_logins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_ibfk_1` (`parent_id`);

--
-- Chỉ mục cho bảng `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `contacts_info`
--
ALTER TABLE `contacts_info`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `logos`
--
ALTER TABLE `logos`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Chỉ mục cho bảng `slides`
--
ALTER TABLE `slides`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `admin_logins`
--
ALTER TABLE `admin_logins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `contacts_info`
--
ALTER TABLE `contacts_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `logos`
--
ALTER TABLE `logos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT cho bảng `slides`
--
ALTER TABLE `slides`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `fk_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Ràng buộc cho bảng `admin_logins`
--
ALTER TABLE `admin_logins`
  ADD CONSTRAINT `admin_logins_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
