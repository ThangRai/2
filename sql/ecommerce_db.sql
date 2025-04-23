-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th4 23, 2025 lúc 01:22 PM
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
(32, 1, 'Thắng Rai', '::1', '2025-04-18 11:53:45'),
(33, 1, 'Thắng Rai', '::1', '2025-04-21 03:48:30'),
(34, 1, 'Thắng Rai', '::1', '2025-04-21 03:48:42'),
(35, 1, 'Thắng Rai', '::1', '2025-04-21 03:48:54'),
(36, 1, 'Thắng Rai', '::1', '2025-04-21 12:26:24'),
(37, 1, 'Thắng Rai', '::1', '2025-04-22 06:18:59'),
(38, 8, 'Lê Văn Bá Lợi', '::1', '2025-04-22 06:19:12'),
(39, 1, 'Thắng Rai', '::1', '2025-04-22 06:19:53'),
(40, 8, 'Lê Văn Bá Lợi', '::1', '2025-04-22 07:29:48'),
(41, 1, 'Thắng Rai', '::1', '2025-04-23 03:10:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `blogs`
--

CREATE TABLE `blogs` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `content` longtext NOT NULL,
  `views` int DEFAULT '0',
  `thumbnail` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT '1',
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` varchar(160) DEFAULT NULL,
  `seo_keywords` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `slug`, `description`, `content`, `views`, `thumbnail`, `is_published`, `seo_title`, `seo_description`, `seo_keywords`, `created_at`, `updated_at`) VALUES
(3, 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 3', 'a', 'Mẫu giày này lấy cảm hứng từ các phong cách thể thao huyền thoại trong quá khứ và đưa đến tương lai. Giày mang phong cách hàng ngày với thân giày bằng da mượt mà.', '<p>Mẫu giày này lấy cảm hứng từ các phong cách thể thao huyền thoại trong quá khứ và đưa đến tương lai. Giày mang phong cách hàng ngày với thân giày bằng da mượt mà.</p><figure class=\"image\"><img src=\"http://localhost/2/admin/uploads/ckeditor/6808b58b618b7_ChatGPT Image 14_51_19 22 thg 4, 2025.png\"></figure>', 4, 'Uploads/thumbnails/1745399738_bl1.jpg', 1, 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 3', 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 3', 'dsd', '2025-04-23 09:15:38', '2025-04-23 11:28:18'),
(4, 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 2', 'nhap-mau-noi-dung-tin-tuc-va-khuyen-mai-cho-website-mau-2', 'Lorem Ipsum chỉ đơn giản là một đoạn văn bản giả, được dùng vào việc trình bày và dàn trang phục vụ cho in ấn. Lorem Ipsum đã được sử dụng như một văn bản chuẩn', '<p>Lorem Ipsum chỉ đơn giản là một đoạn văn bản giả, được dùng vào việc trình bày và dàn trang phục vụ cho in ấn. Lorem Ipsum đã được sử dụng như một văn bản chuẩn</p><figure class=\"image\"><img src=\"http://localhost/2/admin/uploads/ckeditor/6808b312ddb1b_nhap-mau-noi-dung-tin-tuc-va-khuyen-mai-cho-website-mau-2-0.jpg\"></figure>', 2, 'Uploads/thumbnails/1745400605_nhap-mau-noi-dung-tin-tuc-va-khuyen-mai-cho-website-mau-2-0.jpg', 1, 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 2', 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 2', 'giày, giày đẹp,', '2025-04-23 09:30:05', '2025-04-23 11:12:08'),
(5, 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 1', 'nhap-mau-noi-dung-tin-tuc-va-khuyen-mai-cho-website-mau-1', 'Hơn 300+ mẫu giày Adidas, Nike có tại shop từ STANSMITH, SUPERSTAR, ULTRABOOST, ZX 2k đến ADIDAS 4D ...', '<h2><strong>Thông tin sản phẩm:</strong></h2><p>- Giày Adidas Stan Smith x HER Bounty Sneakers giày thể thao nữ trrắng FW2524</p><h3><strong>- Hàng Chính Hãng</strong></h3><p>- Cam kết chính hãng 100% nhập từ ADIDAS US, UK, JP - Fake đền x10.</p><p>- GIÁ RẺ hơn các shop khác 15-20% - full box, tem, tag, giấy gói chính hãng.</p><h3><strong>- Miễn phí đổi size, đổi mẫu trong vòng 3 ngày.</strong></h3><h3><strong>- NHIỀU MẪU:</strong></h3><p>* Hơn 300+ mẫu giày Adidas, Nike có tại shop từ STANSMITH, SUPERSTAR, ULTRABOOST, ZX 2k đến ADIDAS 4D ...</p><p>+ Nhiệm vụ của Web Số sẽ cài đặt và tối ưu quảng cáo Google cho quý khách trong quá trình hoạt động của quảng cáo. + Theo dõi quảng cáo, tối ưu mẫu quảng cáo khi cần thiết. + Thay đổi mục tiêu quảng cáo và từ khóa cho phù hợp với dịch vụ của quý khách. Chi phí web Số sẽ nhận 15% dựa vào số tiền khách hàng nạp vào tài khoản quảng cáo. VD: Quý khách nạp 5tr vào tk quảng cáo, thì cần chuyển khoản 5.750.000 và Web Số sẽ nhận 750.000 phí quản lý quảng cáo và chi phí nạp tiền vào Google. % Chi phí có thể thay đổi tùy vào khách hàng chạy ngân sách nhiều hay ít.</p><figure class=\"media\"><div data-oembed-url=\"https://www.youtube.com/watch?v=ECxVfrwwTp0&amp;list=RDSK7GEHzTmAA&amp;index=8\"><div style=\"position: relative; padding-bottom: 100%; height: 0; padding-bottom: 56.2493%;\"><iframe src=\"https://www.youtube.com/embed/ECxVfrwwTp0\" style=\"position: absolute; width: 100%; height: 100%; top: 0; left: 0;\" frameborder=\"0\" allow=\"autoplay; encrypted-media\" allowfullscreen=\"\"></iframe></div></div></figure>', 16, 'Uploads/thumbnails/1745400662_nhap-mau-noi-dung-tin-tuc-website-mau-1-0.jpg', 1, 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 1', 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 1', 'giày, giày đẹp,', '2025-04-23 09:31:02', '2025-04-23 11:28:20');

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
(15, 'Liên hệ', 0, 'http://localhost/2/public/pages/lienhe.php', 1, 6, '2025-04-18 07:01:51', '2025-04-23 09:51:59'),
(16, 'Blog', 0, 'http://localhost/2/public/pages/blog.php', 1, 5, '2025-04-23 09:51:53', '2025-04-23 09:51:53');

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
(1, '161 Thôn 3, Xã Khuê Ngọc Điền, Huyện Krông Bông, Tỉnh Đắk Lắk, Việt Nam', '0914476792', 'vanthang@webso.vn', 'Thứ Hai - Thứ Sáu: 9:00 - 17:00', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3894.832426986445!2d108.30534958539566!3d12.527257892891637!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317193b603af7f3b%3A0x71c85315931a80b0!2zVGjhuq9uZyBSYWk!5e0!3m2!1svi!2s!4v1744961247747!5m2!1svi!2s\" width=\"100%\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '2025-04-21 08:54:59');

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
(15, 'Lê Văn Thắng', 'badsaotulong123@gmail.com', '0914476792', 'thôn 3 số nhà 161', 'Tỉnh Đắk Lắk', 'Huyện Krông Pắc', 'Xã Hòa Đông', '2025-04-18 12:50:20', ''),
(16, 'Jeanette Nguyen', 'camypujor@mailinator.com', '0914476799', 'Anim corporis qui eu', 'Tỉnh Hậu Giang', 'Thành phố Vị Thanh', 'Phường IV', '2025-04-21 08:07:08', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer_reviews`
--

CREATE TABLE `customer_reviews` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `rating` int NOT NULL,
  `description` text NOT NULL,
  `content` text NOT NULL,
  `is_visible` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `avatar` varchar(255) DEFAULT NULL
) ;

--
-- Đang đổ dữ liệu cho bảng `customer_reviews`
--

INSERT INTO `customer_reviews` (`id`, `name`, `rating`, `description`, `content`, `is_visible`, `created_at`, `avatar`) VALUES
(1, 'Lê Văn Thắng', 5, 'Hồ chí minh', 'Tôi rất hài lòng về tính thẩm mỹ của giao diện cũng như những chức năng của website mang lại, từ khi có website chúng tôi tiết kiệm được chi phí thuê nhân công. Vì thế, chắc chắc tôi sẽ giới thiệu công ty của các bạn với những người bạn của tôi khi họ có nhu cầu thiết kế web.', 1, '2025-04-23 03:26:07', '68085f724aa4a_yk1.jpg'),
(3, 'NGUYỄN THỊ NGỌC TRÂM', 5, 'Hà nội', 'Rất tuyệt vời! Thật khó tin là chúng tôi có thể lập được một website bán hàng trực tuyến lại đơn giản và nhanh chóng đến vậy. Từ khi có website số lượng đơn hàng tăng lên, công việc kinh doanh của công ty tôi trở nên thuận lợi hơn rất nhiều. Lượng khách hàng biết đến và mua hàng của chúng...', 1, '2025-04-23 03:38:25', '680860be94ef5_khach-hang-4.jpg'),
(4, 'Minh Blbeu', 5, 'Đà Nẵng', 'Sau khi nhận được website do Web Số thiết kế tôi không hề sử dụng thêm bất cứ dịch vụ nào của công ty, nhưng các nhân viên ở đây rất nhiệt tình, giúp tôi nhanh chóng khắc phục các vấn đề mà web mắc phải.Tôi rất hài lòng về dịch vụ của công ty', 1, '2025-04-23 03:39:09', '680860dde643f_Image-User-4.jpg');

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
(20, 10, 1690000.00, 'delivered', 'Eos consectetur vol', 'cod', '2025-04-18 12:08:49'),
(21, 11, 800000.00, 'pending', 'Et sed dolore cumque', 'cod', '2025-04-18 12:11:49'),
(22, 12, 800000.00, 'pending', 'Velit dolorem dolor', 'cod', '2025-04-18 12:14:01'),
(23, 2, 90000.00, 'pending', 'Nostrum anim ea cumq', 'cod', '2025-04-18 12:16:29'),
(24, 2, 800000.00, 'pending', 'fdsf', 'cod', '2025-04-18 12:17:26'),
(25, 6, 800000.00, 'delivered', 's', 'cod', '2025-04-18 12:22:28'),
(26, 14, 800000.00, 'delivered', 'nhanh', 'bank_transfer', '2025-04-18 12:32:59'),
(27, 7, 800000.00, 'delivered', 'nhanh lẹ', 'bank_transfer', '2025-04-18 12:38:31'),
(28, 2, 800000.00, 'delivered', '7h39', 'cod', '2025-04-18 12:39:42'),
(29, 15, 800000.00, 'delivered', 'á', 'cod', '2025-04-18 12:50:20'),
(30, 16, 18287458.00, 'delivered', 'Laborum nihil rerum', 'bank_transfer', '2025-04-21 08:07:08');

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
(27, 29, 6, 1, 800000.00),
(28, 30, 6, 5, 800000.00),
(29, 30, 3, 6, 2131243.00),
(30, 30, 5, 1, 1500000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `partners`
--

CREATE TABLE `partners` (
  `id` int NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `link` varchar(255) DEFAULT NULL,
  `is_visible` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `partners`
--

INSERT INTO `partners` (`id`, `logo`, `name`, `description`, `link`, `is_visible`, `created_at`, `updated_at`) VALUES
(12, '1745405176_dt1.png', '1', '', '', 1, '2025-04-23 10:46:16', '2025-04-23 10:46:16'),
(13, '1745405192_dt2.png', '2', '', '', 1, '2025-04-23 10:46:32', '2025-04-23 10:46:32'),
(14, '1745405202_dt3.png', '3', '', '', 1, '2025-04-23 10:46:42', '2025-04-23 10:46:42'),
(15, '1745405212_dt4.png', '4', '', '', 1, '2025-04-23 10:46:52', '2025-04-23 10:46:52'),
(16, '1745405221_dt5.png', '5', '', '', 1, '2025-04-23 10:47:01', '2025-04-23 10:47:01'),
(17, '1745405230_dt6.png', '6', '', '', 1, '2025-04-23 10:47:10', '2025-04-23 10:50:53');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `category_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext,
  `stock` int NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `content` longtext,
  `original_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `current_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `slug` varchar(255) NOT NULL,
  `seo_image` varchar(255) DEFAULT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `seo_keywords` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `stock`, `image`, `created_at`, `content`, `original_price`, `current_price`, `is_active`, `slug`, `seo_image`, `seo_title`, `seo_description`, `seo_keywords`) VALUES
(3, 1, 'Mẫu Giày 05', 'Sample description for product.', 50352, 'uploads/products/mau-giay-020-0.png', '2025-04-14 23:41:03', 'This is a sample product description.', 3242421.00, 2131243.00, 1, 'mau-giay-05', NULL, NULL, NULL, NULL),
(4, 1, 'Mẫu Giày 06', 'Sample description for product.', 30256, 'uploads/products/mau-giay-019-3c-0.jpg', '2025-04-14 23:41:03', 'Another product with great features.', 2000000.00, 1690000.00, 0, 'mau-giay-06', NULL, NULL, NULL, NULL),
(5, 1, 'Mẫu Giày 04', 'Sample description for product.', 2354, 'uploads/products/mau-giay-015-0.png', '2025-04-15 00:01:16', 'This is a sample product description.', 1900000.00, 1500000.00, 1, 'mau-giay-04', NULL, NULL, NULL, NULL),
(6, 1, 'Mẫu Giày 03', 'Sample description for product.', 50143, 'uploads/products/mau-giay-015-0.png', '2025-04-15 00:01:29', 'This is a sample product description.fd', 1000000.00, 800000.00, 1, 'mau-giay-03', NULL, NULL, NULL, NULL),
(7, 0, 'Mẫu Giày 02', 'Sample description for product.', 2435, 'uploads/products/mau-giay-032-0.png', '2025-04-15 00:01:47', 'This is a sample product description.', 100000.00, 90000.00, 1, 'mau-giay-02', NULL, NULL, NULL, NULL),
(8, 14, 'Mẫu Giày 01', '<ol><li>- Giày Adidas Stan Smith x HER Bounty Sneakers giày thể thao nữ trrắng FW2524</li><li>- Hàng Chính Hãng - Cam kết chính hãng 100% nhập từ ADIDAS US, UK, JP - Fake đền x1</li><li>- GIÁ RẺ hơn các shop khác 15-20% - full box, tem, tag, giấy gói chính hãng.</li></ol><p>&nbsp;</p>', 0, 'uploads/products/mau-giay-015-0.png', '2025-04-15 00:04:59', '<p><i><strong>Sản phẩm Giày thể thao của Shop đã được kiểm tra trước khi đóng gói và có video quay lại quá trình đóng hàng và gửi hàng cho khách nguyên vẹn, không có lỗi. Khách hàng lưu ý khi nhận hàng thì quay clip lại quá trình kiểm hàng để tránh trường hợp nhận sai hàng hoặc hàng lỗi.</strong></i></p><ol><li><i><strong>1 bước 1</strong></i></li><li><i><strong>2 bước 2</strong></i></li><li><i><strong>3 bước 3</strong></i></li></ol>', 1000000.00, 800000.00, 1, 'mau-giay-01', 'Uploads/seo_images/mau-giay-019-3c-0.jpg', 'Mẫu Giày 01', 'mô tả seo mẫu giày 01', 'giày, giày đẹp,');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `rating` int NOT NULL,
  `comment` text NOT NULL,
  `is_approved` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `customer_name`, `rating`, `comment`, `is_approved`, `created_at`) VALUES
(1, 6, 'NGUYỄN THỊ NGỌC TRÂM', 5, 'tốt', 1, '2025-04-21 07:44:27'),
(4, 6, 'NGUYỄN THỊ NGỌC TRÂM', 4, 'nice', 0, '2025-04-21 13:04:35'),
(5, 6, 'Lê Văn Thắngsdsd', 5, 'j', 0, '2025-04-21 13:04:54'),
(6, 6, 'Nguyễn Thị Minh Thi', 5, 'very good', 0, '2025-04-22 06:27:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `review_replies`
--

CREATE TABLE `review_replies` (
  `id` int NOT NULL,
  `review_id` int NOT NULL,
  `reply_by` varchar(255) NOT NULL,
  `reply_content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `review_replies`
--

INSERT INTO `review_replies` (`id`, `review_id`, `reply_by`, `reply_content`, `created_at`) VALUES
(1, 1, 'Admin', 'Cảm ơn bạn', '2025-04-21 07:55:13');

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
(1, 'site_status', '1', '2025-04-21 12:29:20'),
(3, 'default_font_size', '16', '2025-04-15 11:10:27'),
(4, 'default_font_weight', 'normal', '2025-04-15 11:10:34'),
(10, 'scroll_top', '1', '2025-04-18 07:31:45'),
(13, 'columns_375', '2', '2025-04-15 13:56:12'),
(14, 'columns_425', '3', '2025-04-15 13:56:12'),
(15, 'columns_768', '4', '2025-04-15 13:47:14'),
(16, 'columns_1200', '5', '2025-04-15 13:47:14'),
(17, 'columns_max', '5', '2025-04-21 12:15:57'),
(88, 'favicon', '68073819ccee9-fhc.png', '2025-04-22 06:32:57'),
(91, 'default_bg_color', '#000000', '2025-04-21 12:16:48'),
(92, 'default_text_color', '#000000', '2025-04-21 03:49:32'),
(93, 'default_link_color', '#007bff', '2025-04-21 03:49:32'),
(94, 'default_opacity', '1', '2025-04-21 03:49:32'),
(114, 'review_columns_375', '1', '2025-04-23 03:44:43'),
(115, 'review_columns_425', '1', '2025-04-23 03:44:43'),
(116, 'review_columns_768', '2', '2025-04-23 03:44:43'),
(117, 'review_columns_1200', '3', '2025-04-23 03:44:43'),
(118, 'review_columns_max', '3', '2025-04-23 03:49:32'),
(169, 'partner_columns_375', '1', '2025-04-23 03:57:21'),
(170, 'partner_columns_425', '1', '2025-04-23 03:57:21'),
(171, 'partner_columns_768', '2', '2025-04-23 03:57:21'),
(172, 'partner_columns_1200', '3', '2025-04-23 03:57:21'),
(173, 'partner_columns_max', '6', '2025-04-23 04:00:43'),
(204, 'blog_columns_375', '1', '2025-04-23 09:31:43'),
(205, 'blog_columns_425', '1', '2025-04-23 09:31:43'),
(206, 'blog_columns_768', '2', '2025-04-23 09:31:43'),
(207, 'blog_columns_1200', '4', '2025-04-23 09:31:43'),
(208, 'blog_columns_max', '4', '2025-04-23 09:33:14');

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
-- Chỉ mục cho bảng `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

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
-- Chỉ mục cho bảng `customer_reviews`
--
ALTER TABLE `customer_reviews`
  ADD PRIMARY KEY (`id`);

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
-- Chỉ mục cho bảng `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `review_replies`
--
ALTER TABLE `review_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_id` (`review_id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT cho bảng `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `customer_reviews`
--
ALTER TABLE `customer_reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `logos`
--
ALTER TABLE `logos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT cho bảng `partners`
--
ALTER TABLE `partners`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `review_replies`
--
ALTER TABLE `review_replies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

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

--
-- Ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `review_replies`
--
ALTER TABLE `review_replies`
  ADD CONSTRAINT `review_replies_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
