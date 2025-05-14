-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th5 14, 2025 lúc 06:27 AM
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
-- Cấu trúc bảng cho bảng `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int NOT NULL,
  `admin_id` int NOT NULL,
  `role_id` int NOT NULL,
  `action` varchar(255) NOT NULL,
  `page` varchar(100) NOT NULL,
  `target_id` int DEFAULT NULL,
  `details` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `admin_id`, `role_id`, `action`, `page`, `target_id`, `details`, `created_at`, `ip_address`) VALUES
(4, 9, 4, 'Nhân bản FAQ', 'question', 12, 'FAQ gốc ID: 10', '2025-05-10 14:27:48', '::1'),
(5, 9, 4, 'Xóa FAQ', 'question', 12, NULL, '2025-05-10 14:28:31', '::1'),
(6, 9, 4, 'Sửa FAQ', 'question', 10, 'Câu hỏi: adsadasdsds', '2025-05-10 14:28:44', '::1'),
(7, 9, 4, 'Xóa FAQ', 'question', 10, NULL, '2025-05-10 14:29:01', '::1'),
(8, 8, 3, 'Truy cập bị từ chối', 'question', NULL, 'Admin ID: 8, Role ID: 3', '2025-05-10 14:29:47', '::1'),
(15, 6, 1, 'Xóa FAQ', 'question', 15, NULL, '2025-05-10 14:32:27', '::1'),
(16, 6, 1, 'Nhân bản FAQ', 'question', 16, 'FAQ gốc ID: 6', '2025-05-10 14:38:06', '::1'),
(17, 6, 1, 'Nhân bản FAQ', 'question', 17, 'FAQ gốc ID: 16', '2025-05-10 14:38:18', '::1'),
(18, 6, 1, 'Xóa FAQ', 'question', 17, NULL, '2025-05-10 14:38:25', '::1'),
(19, 6, 1, 'Xóa FAQ', 'question', 16, NULL, '2025-05-10 14:38:27', '::1'),
(20, 1, 1, 'Thêm dịch vụ', 'service', 1, 'Dịch vụ ID: 1, Tên: Dịch vụ 1', '2025-05-10 15:05:16', '::1'),
(21, 1, 1, 'Thêm dịch vụ', 'service', 2, 'Dịch vụ ID: 2, Tên: Dịch vụ 2', '2025-05-10 15:07:36', '::1'),
(22, 1, 1, 'Sửa dịch vụ', 'service', 2, 'Dịch vụ ID: 2, Tên: Dịch vụ 2', '2025-05-13 09:56:26', '::1'),
(23, 1, 1, 'Thêm dự án', 'project', 1, 'Dự án ID: 1, Tên: Dự án 1', '2025-05-13 10:11:07', '::1'),
(24, 1, 1, 'Thêm bài viết', 'info', 1, 'Bài viết ID: 1, Tên: d', '2025-05-13 11:01:07', '::1'),
(25, 1, 1, 'Xóa bài viết', 'info', 1, 'Bài viết ID: 1', '2025-05-13 11:01:17', '::1'),
(26, 1, 1, 'Thêm bài viết', 'info', 2, 'Bài viết ID: 2, Tên: Về Chúng Tôi', '2025-05-13 11:14:17', '::1'),
(27, 2, 2, 'Truy cập bị từ chối', 'info', NULL, 'Admin ID: 2, Role ID: 2', '2025-05-13 13:21:10', '::1'),
(29, 2, 2, 'Truy cập bị từ chối', 'info', NULL, 'Admin ID: 2, Role ID: 2', '2025-05-13 13:23:19', '::1'),
(30, 2, 2, 'Truy cập bị từ chối', 'info', NULL, 'Admin ID: 2, Role ID: 2', '2025-05-13 13:23:24', '::1'),
(31, 2, 2, 'Truy cập bị từ chối', 'service', NULL, 'Admin ID: 2, Role ID: 2', '2025-05-13 13:23:58', '::1'),
(32, 2, 2, 'Nhân bản FAQ', 'question', 18, 'FAQ gốc ID: 6', '2025-05-13 13:24:12', '::1'),
(33, 2, 2, 'Sửa bài viết', 'info', 2, 'Bài viết ID: 2, Tên: Về Chúng Tôi', '2025-05-13 13:24:41', '::1'),
(34, 2, 2, 'Thêm', 'image', 1, 'Thêm ảnh: 6822e9fdc058f_image-20230920165606-3-removebg-preview.png', '2025-05-13 13:43:09', '::1'),
(35, 2, 2, 'Thêm', 'image', 2, 'Thêm ảnh: 6822e9fdc78c6_ecff1584-7410-4af0-857a-95799f84fa5c-removebg-preview.png', '2025-05-13 13:43:09', '::1'),
(36, 2, 2, 'Thêm', 'image', 3, 'Thêm ảnh: 6822e9fdcabf3_image-20230920165543-2-removebg-preview.png', '2025-05-13 13:43:09', '::1'),
(37, 2, 2, 'Thêm', 'image', 4, 'Thêm ảnh: 6822e9fdcd7c8_image-20230920165805-5-removebg-preview.png', '2025-05-13 13:43:09', '::1'),
(38, 2, 2, 'Thêm', 'image', 5, 'Thêm ảnh: 6822e9fdda4c5_image-20230920165711-4-removebg-preview.png', '2025-05-13 13:43:09', '::1'),
(39, 2, 2, 'Thêm', 'image', 6, 'Thêm ảnh: 6822e9fdddc72_image-20230920165855-6-removebg-preview.png', '2025-05-13 13:43:09', '::1'),
(40, 2, 2, 'Thêm', 'image', 7, 'Thêm ảnh: 6822ea14a65f2_xuong-may-non-dai-tin-1.png', '2025-05-13 13:43:32', '::1'),
(41, 2, 2, 'Xóa', 'image', 7, 'Xóa ảnh: 6822ea14a65f2_xuong-may-non-dai-tin-1.png', '2025-05-13 13:44:11', '::1'),
(42, 2, 2, 'Sửa', 'image', 1, 'Sửa bản ghi ID: 1, 5 ảnh', '2025-05-13 13:51:53', '::1'),
(43, 2, 2, 'Xóa', 'image', 6, 'Xóa bản ghi: 0 ảnh', '2025-05-13 13:52:01', '::1'),
(44, 2, 2, 'Xóa', 'image', 5, 'Xóa bản ghi: 0 ảnh', '2025-05-13 13:52:04', '::1'),
(45, 2, 2, 'Ẩn/Hiện', 'image', 1, 'Chuyển trạng thái bản ghi ID: 1 sang inactive', '2025-05-13 13:52:07', '::1'),
(46, 2, 2, 'Ẩn/Hiện', 'image', 1, 'Chuyển trạng thái bản ghi ID: 1 sang active', '2025-05-13 13:52:10', '::1'),
(47, 2, 2, 'Xóa', 'image', 4, 'Xóa bản ghi: 0 ảnh', '2025-05-13 13:52:13', '::1'),
(48, 2, 2, 'Xóa', 'image', 3, 'Xóa bản ghi: 0 ảnh', '2025-05-13 13:52:16', '::1'),
(49, 2, 2, 'Xóa', 'image', 2, 'Xóa bản ghi: 0 ảnh', '2025-05-13 13:52:20', '::1'),
(50, 2, 2, 'Truy cập bị từ chối', 'project', NULL, 'Admin ID: 2, Role ID: 2', '2025-05-13 15:16:22', '::1'),
(51, 2, 2, 'Sửa dự án', 'project', 1, 'Dự án ID: 1, Tên: Dự án 1', '2025-05-13 15:21:55', '::1');

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
(7, 'vanthang', 'Emg9P3Wh', 'Lê Văn Thắng', '2025-04-18 04:17:33', '0914476791', 'vanthang@webso.vn', 'thôn 3 số nhà 161\r\nXã khuê ngọc điền', 'uploads/avatars/admin_1744949852.jpg', '2FA', 3),
(8, 'loi', 'loi0123', 'Lê Văn Bá Lợi', '2025-04-18 08:27:26', '0914476793', 'levanthang200603@gmail.com', 'thôn 3 số nhà 161\r\nXã khuê ngọc điền', 'uploads/avatars/admin_1744964845.jpg', '2FA', 3),
(9, 'thangadmin', 'MjP9fBIm', 'Lê Văn Thắng', '2025-05-10 06:48:48', '0837342443', 'phimdankhoi@gmail.com', 'Sài Gòn', 'uploads/avatars/admin_1746859728.png', '2FA', 4);

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
(41, 1, 'Thắng Rai', '::1', '2025-04-23 03:10:14'),
(42, 1, 'Thắng Rai', '::1', '2025-04-24 14:28:09'),
(43, 1, 'Thắng Rai', '::1', '2025-04-24 15:31:11'),
(44, 1, 'Thắng Rai', '::1', '2025-05-09 08:39:49'),
(45, 1, 'Thắng Rai', '::1', '2025-05-10 02:01:28'),
(46, 8, 'Lê Văn Bá Lợi', '::1', '2025-05-10 02:52:24'),
(47, 2, 'Thắng Raiy', '::1', '2025-05-10 03:04:35'),
(48, 1, 'Thắng Rai', '::1', '2025-05-10 03:04:49'),
(49, 8, 'Lê Văn Bá Lợi', '::1', '2025-05-10 06:23:56'),
(50, 7, 'Lê Văn Thắng', '::1', '2025-05-10 06:28:54'),
(51, 7, 'Lê Văn Thắng', '::1', '2025-05-10 06:30:49'),
(52, 1, 'Thắng Rai', '::1', '2025-05-10 06:30:59'),
(53, 8, 'Lê Văn Bá Lợi', '::1', '2025-05-10 06:32:21'),
(54, 2, 'Thắng Raiy', '::1', '2025-05-10 06:38:59'),
(55, 1, 'Thắng Rai', '::1', '2025-05-10 06:39:17'),
(56, 9, 'Lê Văn Thắng', '::1', '2025-05-10 06:50:20'),
(57, 9, 'Lê Văn Thắng', '::1', '2025-05-10 06:51:59'),
(58, 9, 'Lê Văn Thắng', '::1', '2025-05-10 06:57:07'),
(59, 1, 'Thắng Rai', '::1', '2025-05-10 06:57:17'),
(60, 8, 'Lê Văn Bá Lợi', '::1', '2025-05-10 07:00:20'),
(61, 1, 'Thắng Rai', '::1', '2025-05-10 07:13:38'),
(62, 9, 'Lê Văn Thắng', '::1', '2025-05-10 07:16:31'),
(63, 1, 'Thắng Rai', '::1', '2025-05-10 07:16:41'),
(64, 2, 'Thắng Raiy', '::1', '2025-05-10 07:17:30'),
(65, 9, 'Lê Văn Thắng', '::1', '2025-05-10 07:19:46'),
(66, 9, 'Lê Văn Thắng', '::1', '2025-05-10 07:23:10'),
(67, 8, 'Lê Văn Bá Lợi', '::1', '2025-05-10 07:29:44'),
(68, 8, 'Lê Văn Bá Lợi', '::1', '2025-05-10 07:30:57'),
(69, 6, 'Trâm', '::1', '2025-05-10 07:31:56'),
(70, 8, 'Lê Văn Bá Lợi', '::1', '2025-05-10 07:55:28'),
(71, 9, 'Lê Văn Thắng', '::1', '2025-05-10 07:55:48'),
(72, 1, 'Thắng Rai', '::1', '2025-05-10 07:56:26'),
(73, 1, 'Thắng Rai', '::1', '2025-05-10 09:31:07'),
(74, 1, 'Thắng Rai', '::1', '2025-05-10 09:32:41'),
(75, 1, 'Thắng Rai', '::1', '2025-05-13 02:46:32'),
(76, 1, 'Thắng Rai', '::1', '2025-05-13 02:46:47'),
(77, 8, 'Lê Văn Bá Lợi', '::1', '2025-05-13 06:19:55'),
(78, 8, 'Lê Văn Bá Lợi', '::1', '2025-05-13 06:20:26'),
(79, 2, 'Thắng Raiy', '::1', '2025-05-13 06:20:59'),
(80, 1, 'Thắng Rai', '::1', '2025-05-13 06:22:14'),
(81, 1, 'Thắng Rai', '::1', '2025-05-13 06:45:38'),
(82, 1, 'Thắng Rai', '::1', '2025-05-13 08:23:33'),
(83, 1, 'Thắng Rai', '::1', '2025-05-13 08:26:59'),
(84, 1, 'Thắng Rai', '::1', '2025-05-14 02:07:20'),
(85, 8, 'Lê Văn Bá Lợi', '::1', '2025-05-13 23:19:52'),
(86, 9, 'Lê Văn Thắng', '::1', '2025-05-13 23:22:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `articles`
--

CREATE TABLE `articles` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `content` text,
  `thumbnail` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT '0',
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` varchar(160) DEFAULT NULL,
  `seo_keywords` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `articles`
--

INSERT INTO `articles` (`id`, `title`, `slug`, `description`, `content`, `thumbnail`, `is_published`, `seo_title`, `seo_description`, `seo_keywords`, `created_at`, `updated_at`) VALUES
(2, 'Về Chúng Tôi', 've-chung-toi', 'Trong bối cảnh nền kinh tế Việt Nam không ngừng phát triển và hội nhập sâu rộng với thế giới, nhu cầu về cơ sở vật chất, trang thiết bị nội thất trường học, văn phòng ngày càng được chú trọng cả về chất lượng lẫn tính thẩm mỹ. Với tầm nhìn dài hạn cùng sự nhạy bén trong việc nắm bắt xu hướng thị trường, Công ty Cổ phần Sản xuất Thương mại Gia Võ đã ra đời, mang theo sứ mệnh \"Kiến tạo không gian, nâng tầm giá trị sống và làm việc\".', '<p>Trong bối cảnh nền kinh tế Việt Nam không ngừng phát triển và hội nhập sâu rộng với thế giới, nhu cầu về cơ sở vật chất, trang thiết bị nội thất trường học, văn phòng ngày càng được chú trọng cả về chất lượng lẫn tính thẩm mỹ. Với tầm nhìn dài hạn cùng sự nhạy bén trong việc nắm bắt xu hướng thị trường, <strong>Công ty Cổ phần Sản xuất Thương mại Gia Võ</strong> đã ra đời, mang theo sứ mệnh \"Kiến tạo không gian, nâng tầm giá trị sống và làm việc\".</p><p>Ngay từ những ngày đầu thành lập, Gia Võ đã xác định rõ mục tiêu trở thành đơn vị tiên phong trong lĩnh vực sản xuất và cung cấp nội thất tại Việt Nam. Bằng sự đầu tư bài bản vào hệ thống nhà xưởng, trang thiết bị hiện đại, cùng đội ngũ nhân sự chuyên môn cao, công ty liên tục cho ra đời những sản phẩm chất lượng, bền vững, đáp ứng tối ưu nhu cầu của các trường học, văn phòng và các công trình công cộng.</p><p>Trên hành trình phát triển, Gia Võ luôn cam kết mang đến cho khách hàng không chỉ là sản phẩm nội thất tiện ích, hiện đại mà còn là những giải pháp tổng thể về không gian làm việc và học tập. Uy tín, chất lượng và sự sáng tạo không ngừng là những giá trị cốt lõi đã giúp Gia Võ trở thành đối tác tin cậy của hàng nghìn khách hàng lớn nhỏ trên toàn quốc.</p>', 'Uploads/thumbnails/1747109657_info1.jpg', 1, '', '', '', '2025-05-13 11:14:17', '2025-05-13 13:24:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `attribute_values`
--

CREATE TABLE `attribute_values` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `attribute_id` int NOT NULL,
  `value` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `attribute_values`
--

INSERT INTO `attribute_values` (`id`, `product_id`, `attribute_id`, `value`, `created_at`) VALUES
(5, 8, 1, '39,40,41', '2025-04-24 15:12:58'),
(6, 8, 2, 'Đỏ, Xanh, Đen', '2025-04-24 15:12:58'),
(13, 26, 1, '39', '2025-05-13 08:14:47'),
(14, 26, 2, 'Đen', '2025-05-13 08:14:47');

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
(3, 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 3', 'a', 'Mẫu giày này lấy cảm hứng từ các phong cách thể thao huyền thoại trong quá khứ và đưa đến tương lai. Giày mang phong cách hàng ngày với thân giày bằng da mượt mà.', '<p>Mẫu giày này lấy cảm hứng từ các phong cách thể thao huyền thoại trong quá khứ và đưa đến tương lai. Giày mang phong cách hàng ngày với thân giày bằng da mượt mà.</p><figure class=\"image\"><img src=\"http://localhost/2/admin/uploads/ckeditor/6808b58b618b7_ChatGPT Image 14_51_19 22 thg 4, 2025.png\"></figure>', 6, 'Uploads/thumbnails/1745399738_bl1.jpg', 1, 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 3', 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 3', 'dsd', '2025-04-23 09:15:38', '2025-05-10 08:58:32'),
(4, 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 2', 'nhap-mau-noi-dung-tin-tuc-va-khuyen-mai-cho-website-mau-2', 'Lorem Ipsum chỉ đơn giản là một đoạn văn bản giả, được dùng vào việc trình bày và dàn trang phục vụ cho in ấn. Lorem Ipsum đã được sử dụng như một văn bản chuẩn', '<p>Lorem Ipsum chỉ đơn giản là một đoạn văn bản giả, được dùng vào việc trình bày và dàn trang phục vụ cho in ấn. Lorem Ipsum đã được sử dụng như một văn bản chuẩn</p><figure class=\"image\"><img src=\"http://localhost/2/admin/uploads/ckeditor/6808b312ddb1b_nhap-mau-noi-dung-tin-tuc-va-khuyen-mai-cho-website-mau-2-0.jpg\"></figure>', 3, 'Uploads/thumbnails/1745400605_nhap-mau-noi-dung-tin-tuc-va-khuyen-mai-cho-website-mau-2-0.jpg', 1, 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 2', 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 2', 'giày, giày đẹp,', '2025-04-23 09:30:05', '2025-05-10 08:58:36'),
(5, 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 1', 'nhap-mau-noi-dung-tin-tuc-va-khuyen-mai-cho-website-mau-1', 'Hơn 300+ mẫu giày Adidas, Nike có tại shop từ STANSMITH, SUPERSTAR, ULTRABOOST, ZX 2k đến ADIDAS 4D ...', '<h2><strong>Thông tin sản phẩm:</strong></h2><p>- Giày Adidas Stan Smith x HER Bounty Sneakers giày thể thao nữ trrắng FW2524</p><h3><strong>- Hàng Chính Hãng</strong></h3><p>- Cam kết chính hãng 100% nhập từ ADIDAS US, UK, JP - Fake đền x10.</p><p>- GIÁ RẺ hơn các shop khác 15-20% - full box, tem, tag, giấy gói chính hãng.</p><h3><strong>- Miễn phí đổi size, đổi mẫu trong vòng 3 ngày.</strong></h3><h3><strong>- NHIỀU MẪU:</strong></h3><p>* Hơn 300+ mẫu giày Adidas, Nike có tại shop từ STANSMITH, SUPERSTAR, ULTRABOOST, ZX 2k đến ADIDAS 4D ...</p><p>+ Nhiệm vụ của Web Số sẽ cài đặt và tối ưu quảng cáo Google cho quý khách trong quá trình hoạt động của quảng cáo. + Theo dõi quảng cáo, tối ưu mẫu quảng cáo khi cần thiết. + Thay đổi mục tiêu quảng cáo và từ khóa cho phù hợp với dịch vụ của quý khách. Chi phí web Số sẽ nhận 15% dựa vào số tiền khách hàng nạp vào tài khoản quảng cáo. VD: Quý khách nạp 5tr vào tk quảng cáo, thì cần chuyển khoản 5.750.000 và Web Số sẽ nhận 750.000 phí quản lý quảng cáo và chi phí nạp tiền vào Google. % Chi phí có thể thay đổi tùy vào khách hàng chạy ngân sách nhiều hay ít.</p><figure class=\"media\"><div data-oembed-url=\"https://www.youtube.com/watch?v=ECxVfrwwTp0&amp;list=RDSK7GEHzTmAA&amp;index=8\"><div style=\"position: relative; padding-bottom: 100%; height: 0; padding-bottom: 56.2493%;\"><iframe src=\"https://www.youtube.com/embed/ECxVfrwwTp0\" style=\"position: absolute; width: 100%; height: 100%; top: 0; left: 0;\" frameborder=\"0\" allow=\"autoplay; encrypted-media\" allowfullscreen=\"\"></iframe></div></div></figure>', 18, 'Uploads/thumbnails/1745400662_nhap-mau-noi-dung-tin-tuc-website-mau-1-0.jpg', 1, 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 1', 'Nhập mẫu nội dung tin tức và khuyến mãi cho website mẫu 1', 'giày, giày đẹp,', '2025-04-23 09:31:02', '2025-05-13 02:48:15');

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
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `slug` varchar(255) NOT NULL,
  `module` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_id`, `link`, `status`, `order`, `created_at`, `updated_at`, `slug`, `module`) VALUES
(11, 'Trang chủ', 0, 'http://localhost/2/public/', 1, 0, '2025-04-15 09:09:34', '2025-04-15 09:13:13', '', NULL),
(12, 'Giới thiệu', 0, 'http://localhost/2/public/pages/info.php', 1, 1, '2025-04-15 09:10:17', '2025-05-13 04:14:44', '', NULL),
(14, 'Sản phẩm', 0, 'http://localhost/2/public/pages/product.php', 1, 4, '2025-04-15 09:45:23', '2025-04-18 07:01:18', '', NULL),
(15, 'Liên hệ', 0, 'http://localhost/2/public/pages/lienhe.php', 1, 7, '2025-04-18 07:01:51', '2025-05-10 08:09:18', '', NULL),
(16, 'Blog', 0, 'http://localhost/2/public/pages/blog.php', 1, 6, '2025-04-23 09:51:53', '2025-05-10 08:09:13', '', NULL),
(17, 'Dịch vụ', 0, 'http://localhost/2/public/pages/dichvu.php', 1, 5, '2025-05-10 08:09:01', '2025-05-10 08:09:01', '', NULL);

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
-- Cấu trúc bảng cho bảng `faqs`
--

CREATE TABLE `faqs` (
  `id` int NOT NULL,
  `question` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_published` tinyint(1) DEFAULT '0',
  `seo_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `is_published`, `seo_title`, `seo_description`, `seo_keywords`, `created_at`, `updated_at`) VALUES
(1, 'Website là gì ?', 'Website là một tập hợp các trang web có liên quan, được truy cập qua internet, thường có cùng tên miền.', 1, '', '', '', '2025-05-10 09:26:37', '2025-05-10 10:19:44'),
(2, 'Tôi cần website để làm gì?', 'Website giúp bạn giới thiệu doanh nghiệp, bán hàng online, tăng độ tin cậy và tiếp cận khách hàng trên toàn quốc hoặc toàn cầu.', 1, '', '', '', '2025-05-10 09:30:00', '2025-05-10 10:20:05'),
(6, 'Thiết kế website mất bao lâu?', 'Tùy theo mức độ phức tạp, thời gian thiết kế có thể từ 3 ngày đến vài tuần.', 1, '', '', '', '2025-05-10 09:59:59', '2025-05-10 10:20:19'),
(18, 'Thiết kế website mất bao lâu? (Sao chép)', 'Tùy theo mức độ phức tạp, thời gian thiết kế có thể từ 3 ngày đến vài tuần.', 1, '', '', '', '2025-05-13 13:24:12', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `flash_sales`
--

CREATE TABLE `flash_sales` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `sale_price` decimal(10,2) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `flash_sales`
--

INSERT INTO `flash_sales` (`id`, `product_id`, `sale_price`, `start_time`, `end_time`, `is_active`, `created_at`) VALUES
(6, 26, 9000.00, '2025-05-10 10:29:00', '2025-05-12 15:14:00', 1, '2025-05-10 03:29:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `images`
--

CREATE TABLE `images` (
  `id` int NOT NULL,
  `admin_id` int NOT NULL,
  `role_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `filenames` text NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `images`
--

INSERT INTO `images` (`id`, `admin_id`, `role_id`, `title`, `description`, `filenames`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 'hình ảnh 1', 'ảnh', '[\"6822ec04738f4_trich.jpg\",\"6822ec0477cf3_sofia.png\",\"6822ec047b8b7_thunn.jpg\",\"6822ec04811c7_SLyder.jpg\",\"6822ec04860d1_ruler.png\"]', 'active', '2025-05-13 13:43:09', '2025-05-13 13:52:10');

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
(15, 4, 21312430.00, 'shipped', 'cần gấp', 'cod', '2025-04-17 15:42:15'),
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
-- Cấu trúc bảng cho bảng `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` int NOT NULL,
  `email` varchar(100) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `otp_codes`
--

INSERT INTO `otp_codes` (`id`, `email`, `otp`, `created_at`, `expires_at`) VALUES
(1, 'thangwebso@gmail.com', '763663', '2025-05-10 04:27:45', '2025-05-09 21:42:45'),
(2, 'thangwebso@gmail.com', '949098', '2025-05-10 04:29:04', '2025-05-09 21:44:04'),
(3, 'thangwebso@gmail.com', '562643', '2025-05-10 04:29:34', '2025-05-09 21:44:34');

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
(5, 1, 'Mẫu Giày 04', 'Sample description for product.', 2354, 'uploads/products/mau-giay-015-0.png', '2025-04-15 00:01:16', 'This is a sample product description.', 1900000.00, 1500000.00, 0, 'mau-giay-04', NULL, NULL, NULL, NULL),
(6, 1, 'Mẫu Giày 03', 'Sample description for product.', 50143, 'uploads/products/mau-giay-015-0.png', '2025-04-15 00:01:29', 'This is a sample product description.fd', 1000000.00, 800000.00, 1, 'mau-giay-03', NULL, NULL, NULL, NULL),
(7, 0, 'Mẫu Giày 02', 'Sample description for product.', 2435, 'uploads/products/mau-giay-032-0.png', '2025-04-15 00:01:47', 'This is a sample product description.', 100000.00, 90000.00, 1, 'mau-giay-02', NULL, NULL, NULL, NULL),
(8, 14, 'Mẫu Giày 01', '<ol><li>- Giày Adidas Stan Smith x HER Bounty Sneakers giày thể thao nữ trrắng FW2524</li><li>- Hàng Chính Hãng - Cam kết chính hãng 100% nhập từ ADIDAS US, UK, JP - Fake đền x1</li><li>- GIÁ RẺ hơn các shop khác 15-20% - full box, tem, tag, giấy gói chính hãng.</li></ol><p>&nbsp;</p>', 1, 'uploads/products/mau-giay-015-0.png', '2025-04-15 00:04:59', '<p><i><strong>Sản phẩm Giày thể thao của Shop đã được kiểm tra trước khi đóng gói và có video quay lại quá trình đóng hàng và gửi hàng cho khách nguyên vẹn, không có lỗi. Khách hàng lưu ý khi nhận hàng thì quay clip lại quá trình kiểm hàng để tránh trường hợp nhận sai hàng hoặc hàng lỗi.</strong></i></p><ol><li><i><strong>1 bước 1</strong></i></li><li><i><strong>2 bước 2</strong></i></li><li><i><strong>3 bước 3</strong></i></li></ol>', 1000000.00, 800000.00, 1, 'mau-giay-01', 'Uploads/seo_images/mau-giay-019-3c-0.jpg', 'Mẫu Giày 01', 'mô tả seo mẫu giày 01', 'giày, giày đẹp,'),
(26, 14, 'Mẫu Giày 07', '<p><i>Sản phẩm Giày thể thao của Shop đã được kiểm tra trước khi đóng gói và có video quay lại quá trình đóng hàng và gửi hàng cho khách nguyên vẹn, không có lỗi. Khách hàng lưu ý khi nhận hàng thì quay clip lại quá trình kiểm hàng để tránh trường hợp nhận sai hàng hoặc hàng lỗi. Shop sẽ không xử lý khiếu nại khi khách không thực hiện thao tác trên.</i></p><p><br>&nbsp;</p>', 564, 'uploads/products/1745505468_mau-giay-022-0 (1).png', '2025-04-24 14:37:48', '<h2>Thông tin sản phẩm:</h2><p>- Giày Adidas Stan Smith x HER Bounty Sneakers giày thể thao nữ trrắng FW2524</p><h3>- Hàng Chính Hãng</h3><p>- Cam kết chính hãng 100% nhập từ ADIDAS US, UK, JP - Fake đền x10.</p><p>- GIÁ RẺ hơn các shop khác 15-20% - full box, tem, tag, giấy gói chính hãng.</p><h3>- Miễn phí đổi size, đổi mẫu trong vòng 3 ngày.</h3><h3>- NHIỀU MẪU:</h3><p>* Hơn 300+ mẫu giày Adidas, Nike có tại shop từ STANSMITH, SUPERSTAR, ULTRABOOST, ZX 2k đến ADIDAS 4D ...</p><p>Tác giả: Thắng Raiy</p>', 500000.00, 400000.00, 1, 'mau-giay-07', NULL, '', '', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_attributes`
--

CREATE TABLE `product_attributes` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `product_attributes`
--

INSERT INTO `product_attributes` (`id`, `name`, `created_at`) VALUES
(1, 'Kích thước', '2025-04-24 14:59:19'),
(2, 'Màu sắc', '2025-04-24 15:00:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `projects`
--

CREATE TABLE `projects` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `content` text,
  `thumbnail` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT '0',
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` varchar(160) DEFAULT NULL,
  `seo_keywords` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `projects`
--

INSERT INTO `projects` (`id`, `title`, `slug`, `description`, `content`, `thumbnail`, `is_published`, `seo_title`, `seo_description`, `seo_keywords`, `created_at`, `updated_at`) VALUES
(1, 'Dự án 1', 'du-an-1', 'Mô tả dự án 1', '<p><i>+ Nhiệm vụ của Web Số sẽ cài đặt và tối ưu quảng cáo Google cho quý khách trong quá trình hoạt động của quảng cáo.</i><br><i>+ Theo dõi quảng cáo, tối ưu mẫu quảng cáo khi cần thiết.</i><br><i>+ Thay đổi mục tiêu quảng cáo và từ khóa cho phù hợp với dịch vụ của quý khách.</i><br><i>Chi phí web Số sẽ nhận 15% dựa vào số tiền khách hàng nạp vào tài khoản quảng cáo.</i><br><i>VD: Quý khách nạp 5tr vào tk quảng cáo, thì cần chuyển khoản 5.750.000 và Web Số sẽ nhận 750.000 phí quản lý quảng cáo và chi phí nạp tiền vào Google.</i><br><i>% Chi phí có thể thay đổi tùy vào khách hàng chạy ngân sách nhiều hay ít.</i></p>', 'Uploads/thumbnails/1747105867_hinh3 (2).jpg', 1, 'Dự án 1', 'Mô tả dự án 1', 'Dự án 1', '2025-05-13 10:11:07', '2025-05-13 15:21:55');

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
(6, 6, 'Nguyễn Thị Minh Thi', 5, 'very good', 0, '2025-04-22 06:27:54'),
(7, 26, 'Nguyễn Thị Minh Thi', 5, 'oke la', 1, '2025-05-10 08:59:15');

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
(1, 1, 'Admin', 'Cảm ơn bạn', '2025-04-21 07:55:13'),
(4, 7, 'Admin', 'cảm ơn bạn!', '2025-05-10 08:59:42');

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
(3, 'content_manager', 'Quản lý sản phẩm và bài viết'),
(4, 'admin', 'Quyền quản trị viên, có thể tùy chỉnh');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `services`
--

CREATE TABLE `services` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `content` text,
  `thumbnail` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT '0',
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` varchar(160) DEFAULT NULL,
  `seo_keywords` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `services`
--

INSERT INTO `services` (`id`, `title`, `slug`, `description`, `content`, `thumbnail`, `is_published`, `seo_title`, `seo_description`, `seo_keywords`, `created_at`, `updated_at`) VALUES
(1, 'Dịch vụ 1', 'dich-vu-1', 'Mô tả dịch vụ 1', '<p>* Hơn 300+ mẫu giày Adidas, Nike có tại shop từ STANSMITH, SUPERSTAR, ULTRABOOST, ZX 2k đến ADIDAS 4D ...</p><p>+ Nhiệm vụ của Web Số sẽ cài đặt và tối ưu quảng cáo Google cho quý khách trong quá trình hoạt động của quảng cáo. + Theo dõi quảng cáo, tối ưu mẫu quảng cáo khi cần thiết. + Thay đổi mục tiêu quảng cáo và từ khóa cho phù hợp với dịch vụ của quý khách. Chi phí web Số sẽ nhận 15% dựa vào số tiền khách hàng nạp vào tài khoản quảng cáo. VD: Quý khách nạp 5tr vào tk quảng cáo, thì cần chuyển khoản 5.750.000 và Web Số sẽ nhận 750.000 phí quản lý quảng cáo và chi phí nạp tiền vào Google. % Chi phí có thể thay đổi tùy vào khách hàng chạy ngân sách nhiều hay ít.</p><figure class=\"media\"><div data-oembed-url=\"https://www.youtube.com/watch?v=ECxVfrwwTp0&amp;list=RDSK7GEHzTmAA&amp;index=8\"><div style=\"position: relative; padding-bottom: 100%; height: 0; padding-bottom: 56.2493%;\"><iframe src=\"https://www.youtube.com/embed/ECxVfrwwTp0\" style=\"position: absolute; width: 100%; height: 100%; top: 0; left: 0;\" frameborder=\"0\" allow=\"autoplay; encrypted-media\" allowfullscreen=\"\"></iframe></div></div></figure>', 'Uploads/thumbnails/1746864316_anh1.jpg', 1, '', '', '', '2025-05-10 15:05:16', NULL);
INSERT INTO `services` (`id`, `title`, `slug`, `description`, `content`, `thumbnail`, `is_published`, `seo_title`, `seo_description`, `seo_keywords`, `created_at`, `updated_at`) VALUES
(2, 'Dịch vụ 2', 'dich-vu-2', 'Mô tả dịch vụ 2', '<p>&nbsp;</p><p><img src=\"http://localhost/2/admin/uploads/ckeditor/6822b4d381780_yk1.jpg\">&lt;?php<br>ob_start();<br>require_once \'C:/laragon/www/2/admin/config/db_connect.php\';</p><p>// Hàm tạo slug<br>function createSlug($string, $pdo) {<br>&nbsp; &nbsp;$search = [<br>&nbsp; &nbsp; &nbsp; &nbsp;\'à\',\'á\',\'ạ\',\'ả\',\'ã\',\'â\',\'ầ\',\'ấ\',\'ậ\',\'ẩ\',\'ẫ\',\'ă\',\'ằ\',\'ắ\',\'ặ\',\'ẳ\',\'ẵ\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'è\',\'é\',\'ẹ\',\'ẻ\',\'ẽ\',\'ê\',\'ề\',\'ế\',\'ệ\',\'ể\',\'ễ\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'ì\',\'í\',\'ị\',\'ỉ\',\'ĩ\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'ò\',\'ó\',\'ọ\',\'ỏ\',\'õ\',\'ô\',\'ồ\',\'ố\',\'ộ\',\'ổ\',\'ỗ\',\'ơ\',\'ờ\',\'ớ\',\'ợ\',\'ở\',\'ỡ\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'ù\',\'ú\',\'ụ\',\'ủ\',\'ũ\',\'ư\',\'ừ\',\'ứ\',\'ự\',\'ử\',\'ữ\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'ỳ\',\'ý\',\'ỵ\',\'ỷ\',\'ỹ\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'đ\',\'À\',\'Á\',\'Ạ\',\'Ả\',\'Ã\',\'Â\',\'Ầ\',\'Ấ\',\'Ậ\',\'Ẩ\',\'Ẫ\',\'Ă\',\'Ằ\',\'Ắ\',\'Ặ\',\'Ẳ\',\'Ẵ\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'È\',\'É\',\'Ẹ\',\'Ẻ\',\'Ẽ\',\'Ê\',\'Ề\',\'Ế\',\'Ệ\',\'Ể\',\'Ẽ\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'Ì\',\'Í\',\'Ị\',\'Ỉ\',\'Ĩ\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'Ò\',\'Ó\',\'Ọ\',\'Ỏ\',\'Õ\',\'Ô\',\'Ồ\',\'Ố\',\'Ộ\',\'Ổ\',\'Ỗ\',\'Ơ\',\'Ờ\',\'Ớ\',\'Ợ\',\'Ở\',\'Ỡ\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'Ù\',\'Ú\',\'Ụ\',\'Ủ\',\'Ũ\',\'Ư\',\'Ừ\',\'Ứ\',\'Ự\',\'Ử\',\'Ữ\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'Ỳ\',\'Ý\',\'Ỵ\',\'Ỷ\',\'Ỹ\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'Đ\'<br>&nbsp; &nbsp;];<br>&nbsp; &nbsp;$replace = [<br>&nbsp; &nbsp; &nbsp; &nbsp;\'a\',\'a\',\'a\',\'a\',\'a\',\'a\',\'a\',\'a\',\'a\',\'a\',\'a\',\'a\',\'a\',\'a\',\'a\',\'a\',\'a\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'e\',\'e\',\'e\',\'e\',\'e\',\'e\',\'e\',\'e\',\'e\',\'e\',\'e\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'i\',\'i\',\'i\',\'i\',\'i\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'o\',\'o\',\'o\',\'o\',\'o\',\'o\',\'o\',\'o\',\'o\',\'o\',\'o\',\'o\',\'o\',\'o\',\'o\',\'o\',\'o\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'u\',\'u\',\'u\',\'u\',\'u\',\'u\',\'u\',\'u\',\'u\',\'u\',\'u\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'y\',\'y\',\'y\',\'y\',\'y\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'d\',\'A\',\'A\',\'A\',\'A\',\'A\',\'A\',\'A\',\'A\',\'A\',\'A\',\'A\',\'A\',\'A\',\'A\',\'A\',\'A\',\'A\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'E\',\'E\',\'E\',\'E\',\'E\',\'E\',\'E\',\'E\',\'E\',\'E\',\'E\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'I\',\'I\',\'I\',\'I\',\'I\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'O\',\'O\',\'O\',\'O\',\'O\',\'O\',\'O\',\'O\',\'O\',\'O\',\'O\',\'O\',\'O\',\'O\',\'O\',\'O\',\'O\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'U\',\'U\',\'U\',\'U\',\'U\',\'U\',\'U\',\'U\',\'U\',\'U\',\'U\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'Y\',\'Y\',\'Y\',\'Y\',\'Y\',<br>&nbsp; &nbsp; &nbsp; &nbsp;\'D\'<br>&nbsp; &nbsp;];<br>&nbsp; &nbsp;$string = str_replace($search, $replace, $string);<br>&nbsp; &nbsp;$string = preg_replace(\'/[^a-zA-Z0-9\\s]/\', \'\', $string);<br>&nbsp; &nbsp;$string = strtolower(trim(preg_replace(\'/\\s+/\', \'-\', $string), \'-\'));<br>&nbsp; &nbsp;$baseSlug = $string;<br>&nbsp; &nbsp;$counter = 1;<br>&nbsp; &nbsp;while (true) {<br>&nbsp; &nbsp; &nbsp; &nbsp;$stmt = $pdo-&gt;prepare(\"SELECT COUNT(*) FROM services WHERE slug = ?\");<br>&nbsp; &nbsp; &nbsp; &nbsp;$stmt-&gt;execute([$string]);<br>&nbsp; &nbsp; &nbsp; &nbsp;if ($stmt-&gt;fetchColumn() == 0) {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;break;<br>&nbsp; &nbsp; &nbsp; &nbsp;}<br>&nbsp; &nbsp; &nbsp; &nbsp;$string = $baseSlug . \'-\' . $counter++;<br>&nbsp; &nbsp;}<br>&nbsp; &nbsp;return $string;<br>}</p><p>// Hàm ghi log hoạt động (tái sử dụng từ activity_logs.php)<br>function logActivity($pdo, $admin_id, $role_id, $action, $page, $target_id = null, $details = null) {<br>&nbsp; &nbsp;$role_id = $role_id ?? 0;<br>&nbsp; &nbsp;$ip_address = $_SERVER[\'REMOTE_ADDR\'] ?? \'unknown\';<br>&nbsp; &nbsp;try {<br>&nbsp; &nbsp; &nbsp; &nbsp;$stmt = $pdo-&gt;prepare(\"<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;INSERT INTO activity_logs (admin_id, role_id, action, page, target_id, details, ip_address, created_at)<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;VALUES (?, ?, ?, ?, ?, ?, ?, NOW())<br>&nbsp; &nbsp; &nbsp; &nbsp;\");<br>&nbsp; &nbsp; &nbsp; &nbsp;$stmt-&gt;execute([$admin_id, $role_id, $action, $page, $target_id, $details, $ip_address]);<br>&nbsp; &nbsp;} catch (Exception $e) {<br>&nbsp; &nbsp; &nbsp; &nbsp;error_log(\"Lỗi ghi log vào activity_logs: \" . $e-&gt;getMessage());<br>&nbsp; &nbsp;}<br>}</p><p>// Kiểm tra session<br>if (!isset($_SESSION[\'admin_id\'])) {<br>&nbsp; &nbsp;$_SESSION[\'message\'] = [\'type\' =&gt; \'error\', \'text\' =&gt; \'Phiên đăng nhập không hợp lệ. Vui lòng đăng nhập lại.\'];<br>&nbsp; &nbsp;echo \'&lt;script&gt;window.location.href=\"login.php\";&lt;/script&gt;\';<br>&nbsp; &nbsp;exit;<br>}</p><p>// Kiểm tra quyền truy cập<br>$stmt = $pdo-&gt;prepare(\"SELECT role_id FROM admins WHERE id = ?\");<br>$stmt-&gt;execute([$_SESSION[\'admin_id\']]);<br>$admin = $stmt-&gt;fetch(PDO::FETCH_ASSOC);</p><p>if (!$admin || $admin[\'role_id\'] != 1) {<br>&nbsp; &nbsp;logActivity($pdo, $_SESSION[\'admin_id\'] ?? 0, $admin[\'role_id\'] ?? 0, \'Truy cập bị từ chối\', \'service\', null, \'Admin ID: \' . ($_SESSION[\'admin_id\'] ?? \'không có\') . \', Role ID: \' . ($admin[\'role_id\'] ?? \'không có\'));<br>&nbsp; &nbsp;$_SESSION[\'message\'] = [\'type\' =&gt; \'error\', \'text\' =&gt; \'Bạn không có quyền truy cập trang này.\'];<br>&nbsp; &nbsp;echo \'&lt;script&gt;window.location.href=\"index.php?page=dashboard\";&lt;/script&gt;\';<br>&nbsp; &nbsp;exit;<br>}</p><p>// Xử lý xóa dịch vụ<br>if (isset($_GET[\'action\']) &amp;&amp; $_GET[\'action\'] === \'delete\' &amp;&amp; isset($_GET[\'id\'])) {<br>&nbsp; &nbsp;$id = (int)$_GET[\'id\'];<br>&nbsp; &nbsp;$stmt = $pdo-&gt;prepare(\"SELECT thumbnail FROM services WHERE id = ?\");<br>&nbsp; &nbsp;$stmt-&gt;execute([$id]);<br>&nbsp; &nbsp;$service = $stmt-&gt;fetch(PDO::FETCH_ASSOC);</p><p>&nbsp; &nbsp;if ($service) {<br>&nbsp; &nbsp; &nbsp; &nbsp;try {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;// Xóa ảnh đại diện nếu tồn tại<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;if ($service[\'thumbnail\'] &amp;&amp; file_exists(\"C:/laragon/www/2/admin/\" . $service[\'thumbnail\'])) {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;unlink(\"C:/laragon/www/2/admin/\" . $service[\'thumbnail\']);<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;}<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;// Xóa dịch vụ<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$stmt = $pdo-&gt;prepare(\"DELETE FROM services WHERE id = ?\");<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$stmt-&gt;execute([$id]);<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;logActivity($pdo, $_SESSION[\'admin_id\'], $admin[\'role_id\'], \'Xóa dịch vụ\', \'service\', $id, \'Dịch vụ ID: \' . $id);<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$_SESSION[\'message\'] = [\'type\' =&gt; \'success\', \'text\' =&gt; \'Xóa dịch vụ thành công.\'];<br>&nbsp; &nbsp; &nbsp; &nbsp;} catch (Exception $e) {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;error_log(\'Delete service error: \' . $e-&gt;getMessage());<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$_SESSION[\'message\'] = [\'type\' =&gt; \'error\', \'text\' =&gt; \'Lỗi khi xóa dịch vụ: \' . $e-&gt;getMessage()];<br>&nbsp; &nbsp; &nbsp; &nbsp;}<br>&nbsp; &nbsp;} else {<br>&nbsp; &nbsp; &nbsp; &nbsp;$_SESSION[\'message\'] = [\'type\' =&gt; \'error\', \'text\' =&gt; \'Dịch vụ không tồn tại.\'];<br>&nbsp; &nbsp;}<br>&nbsp; &nbsp;echo \'&lt;script&gt;window.location.href=\"?page=service\";&lt;/script&gt;\';<br>&nbsp; &nbsp;exit;<br>}</p><p>// Xử lý thêm/sửa dịch vụ<br>$action = isset($_GET[\'action\']) ? $_GET[\'action\'] : \'list\';<br>$service = null;<br>if ($action === \'edit\' &amp;&amp; isset($_GET[\'id\'])) {<br>&nbsp; &nbsp;$id = (int)$_GET[\'id\'];<br>&nbsp; &nbsp;$stmt = $pdo-&gt;prepare(\"SELECT * FROM services WHERE id = ?\");<br>&nbsp; &nbsp;$stmt-&gt;execute([$id]);<br>&nbsp; &nbsp;$service = $stmt-&gt;fetch(PDO::FETCH_ASSOC);<br>&nbsp; &nbsp;if (!$service) {<br>&nbsp; &nbsp; &nbsp; &nbsp;$_SESSION[\'message\'] = [\'type\' =&gt; \'error\', \'text\' =&gt; \'Dịch vụ không tồn tại.\'];<br>&nbsp; &nbsp; &nbsp; &nbsp;echo \'&lt;script&gt;window.location.href=\"?page=service\";&lt;/script&gt;\';<br>&nbsp; &nbsp; &nbsp; &nbsp;exit;<br>&nbsp; &nbsp;}<br>}</p><p>if ($_SERVER[\'REQUEST_METHOD\'] === \'POST\' &amp;&amp; ($action === \'add\' || $action === \'edit\')) {<br>&nbsp; &nbsp;error_log(\'POST data: \' . print_r($_POST, true));<br>&nbsp; &nbsp;error_log(\'FILES data: \' . print_r($_FILES, true));</p><p>&nbsp; &nbsp;$title = trim($_POST[\'title\'] ?? \'\');<br>&nbsp; &nbsp;$description = trim($_POST[\'description\'] ?? \'\');<br>&nbsp; &nbsp;$content = trim($_POST[\'noidung\'] ?? \'\');<br>&nbsp; &nbsp;$is_published = isset($_POST[\'is_published\']) ? 1 : 0;<br>&nbsp; &nbsp;$seo_title = trim($_POST[\'seo_title\'] ?? \'\');<br>&nbsp; &nbsp;$seo_description = trim($_POST[\'seo_description\'] ?? \'\');<br>&nbsp; &nbsp;$seo_keywords = trim($_POST[\'seo_keywords\'] ?? \'\');</p><p>&nbsp; &nbsp;// Kiểm tra lỗi<br>&nbsp; &nbsp;$errors = [];<br>&nbsp; &nbsp;if (empty($title)) $errors[] = \'Tên dịch vụ không được để trống.\';<br>&nbsp; &nbsp;if (empty($description)) $errors[] = \'Mô tả không được để trống.\';<br>&nbsp; &nbsp;if (!empty($_FILES[\'thumbnail\'][\'name\'])) {<br>&nbsp; &nbsp; &nbsp; &nbsp;$allowed_types = [\'image/jpeg\', \'image/png\'];<br>&nbsp; &nbsp; &nbsp; &nbsp;$max_size = 2 * 1024 * 1024; // 2MB<br>&nbsp; &nbsp; &nbsp; &nbsp;if (!in_array($_FILES[\'thumbnail\'][\'type\'], $allowed_types)) {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$errors[] = \'Ảnh đại diện phải là định dạng JPG hoặc PNG.\';<br>&nbsp; &nbsp; &nbsp; &nbsp;}<br>&nbsp; &nbsp; &nbsp; &nbsp;if ($_FILES[\'thumbnail\'][\'size\'] &gt; $max_size) {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$errors[] = \'Ảnh đại diện không được lớn hơn 2MB.\';<br>&nbsp; &nbsp; &nbsp; &nbsp;}<br>&nbsp; &nbsp;}<br>&nbsp; &nbsp;if (!empty($seo_title) &amp;&amp; strlen($seo_title) &gt; 255) {<br>&nbsp; &nbsp; &nbsp; &nbsp;$errors[] = \'Tiêu đề SEO không được vượt quá 255 ký tự.\';<br>&nbsp; &nbsp;}<br>&nbsp; &nbsp;if (!empty($seo_description) &amp;&amp; strlen($seo_description) &gt; 160) {<br>&nbsp; &nbsp; &nbsp; &nbsp;$errors[] = \'Mô tả SEO không được vượt quá 160 ký tự.\';<br>&nbsp; &nbsp;}</p><p>&nbsp; &nbsp;if (empty($errors)) {<br>&nbsp; &nbsp; &nbsp; &nbsp;try {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;// Xử lý ảnh đại diện<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$thumbnail = $service[\'thumbnail\'] ?? null;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;if (!empty($_FILES[\'thumbnail\'][\'name\']) &amp;&amp; $_FILES[\'thumbnail\'][\'error\'] === UPLOAD_ERR_OK) {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$target_dir = \"Uploads/thumbnails/\";<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;if (!is_dir($target_dir)) {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;mkdir($target_dir, 0755, true);<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;}<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$thumbnail = $target_dir . time() . \'_\' . basename($_FILES[\'thumbnail\'][\'name\']);<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;if (!move_uploaded_file($_FILES[\'thumbnail\'][\'tmp_name\'], $thumbnail)) {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;throw new Exception(\'Lỗi khi tải lên ảnh đại diện.\');<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;}<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;error_log(\'Thumbnail uploaded: \' . $thumbnail);<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;}</p><p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;// Chuẩn bị và thực thi SQL<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;if ($action === \'edit\' &amp;&amp; isset($_POST[\'id\'])) {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$id = (int)$_POST[\'id\'];<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$stmt = $pdo-&gt;prepare(\"UPDATE services SET title = ?, description = ?, content = ?, thumbnail = ?, is_published = ?, seo_title = ?, seo_description = ?, seo_keywords = ?, updated_at = NOW() WHERE id = ?\");<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$stmt-&gt;execute([$title, $description, $content, $thumbnail, $is_published, $seo_title, $seo_description, $seo_keywords, $id]);<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;logActivity($pdo, $_SESSION[\'admin_id\'], $admin[\'role_id\'], \'Sửa dịch vụ\', \'service\', $id, \'Dịch vụ ID: \' . $id . \', Tên: \' . $title);<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$_SESSION[\'message\'] = [\'type\' =&gt; \'success\', \'text\' =&gt; \'Cập nhật dịch vụ thành công.\'];<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;} else {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$slug = createSlug($title, $pdo);<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$stmt = $pdo-&gt;prepare(\"INSERT INTO services (title, slug, description, content, thumbnail, is_published, seo_title, seo_description, seo_keywords, created_at)&nbsp;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())\");<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$stmt-&gt;execute([$title, $slug, $description, $content, $thumbnail, $is_published, $seo_title, $seo_description, $seo_keywords]);<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$new_id = $pdo-&gt;lastInsertId();<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;logActivity($pdo, $_SESSION[\'admin_id\'], $admin[\'role_id\'], \'Thêm dịch vụ\', \'service\', $new_id, \'Dịch vụ ID: \' . $new_id . \', Tên: \' . $title);<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$_SESSION[\'message\'] = [\'type\' =&gt; \'success\', \'text\' =&gt; \'Thêm dịch vụ thành công.\'];<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;}</p><p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;echo \'&lt;script&gt;window.location.href=\"?page=service\";&lt;/script&gt;\';<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;exit;<br>&nbsp; &nbsp; &nbsp; &nbsp;} catch (Exception $e) {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;error_log(\'Service save error: \' . $e-&gt;getMessage());<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$_SESSION[\'message\'] = [\'type\' =&gt; \'error\', \'text\' =&gt; \'Lỗi khi lưu dịch vụ: \' . $e-&gt;getMessage()];<br>&nbsp; &nbsp; &nbsp; &nbsp;}<br>&nbsp; &nbsp;} else {<br>&nbsp; &nbsp; &nbsp; &nbsp;error_log(\'Validation errors: \' . implode(\', \', $errors));<br>&nbsp; &nbsp; &nbsp; &nbsp;$_SESSION[\'message\'] = [\'type\' =&gt; \'error\', \'text\' =&gt; implode(\'&lt;br&gt;\', $errors)];<br>&nbsp; &nbsp;}<br>}</p><p>// Xử lý danh sách dịch vụ<br>if ($action === \'list\') {<br>&nbsp; &nbsp;$search = isset($_GET[\'s\']) ? trim($_GET[\'s\']) : \'\';<br>&nbsp; &nbsp;$page = isset($_GET[\'p\']) ? max(1, (int)$_GET[\'p\']) : 1;<br>&nbsp; &nbsp;$per_page = 10;<br>&nbsp; &nbsp;$offset = ($page - 1) * $per_page;</p><p>&nbsp; &nbsp;// Xây dựng truy vấn danh sách<br>&nbsp; &nbsp;$sql = \"SELECT * FROM services\";<br>&nbsp; &nbsp;$count_sql = \"SELECT COUNT(*) as total FROM services\";<br>&nbsp; &nbsp;$params = [];</p><p>&nbsp; &nbsp;if ($search) {<br>&nbsp; &nbsp; &nbsp; &nbsp;$sql .= \" WHERE title LIKE ? OR description LIKE ?\";<br>&nbsp; &nbsp; &nbsp; &nbsp;$count_sql .= \" WHERE title LIKE ? OR description LIKE ?\";<br>&nbsp; &nbsp; &nbsp; &nbsp;$search_param = \'%\' . $search . \'%\';<br>&nbsp; &nbsp; &nbsp; &nbsp;$params = [$search_param, $search_param];<br>&nbsp; &nbsp;}</p><p>&nbsp; &nbsp;$sql .= \" ORDER BY created_at DESC LIMIT ? OFFSET ?\";<br>&nbsp; &nbsp;$params[] = $per_page;<br>&nbsp; &nbsp;$params[] = $offset;</p><p>&nbsp; &nbsp;// Đếm tổng số bản ghi<br>&nbsp; &nbsp;$count_stmt = $pdo-&gt;prepare($count_sql);<br>&nbsp; &nbsp;$count_stmt-&gt;execute($search ? [$search_param, $search_param] : []);<br>&nbsp; &nbsp;$total_services = $count_stmt-&gt;fetch(PDO::FETCH_ASSOC)[\'total\'];<br>&nbsp; &nbsp;$total_pages = ceil($total_services / $per_page);</p><p>&nbsp; &nbsp;// Lấy danh sách dịch vụ<br>&nbsp; &nbsp;$stmt = $pdo-&gt;prepare($sql);<br>&nbsp; &nbsp;$stmt-&gt;bindValue(count($params) - 1, $per_page, PDO::PARAM_INT);<br>&nbsp; &nbsp;$stmt-&gt;bindValue(count($params), $offset, PDO::PARAM_INT);<br>&nbsp; &nbsp;if ($search) {<br>&nbsp; &nbsp; &nbsp; &nbsp;$stmt-&gt;bindValue(1, $search_param, PDO::PARAM_STR);<br>&nbsp; &nbsp; &nbsp; &nbsp;$stmt-&gt;bindValue(2, $search_param, PDO::PARAM_STR);<br>&nbsp; &nbsp;}<br>&nbsp; &nbsp;$stmt-&gt;execute();<br>&nbsp; &nbsp;$services = $stmt-&gt;fetchAll(PDO::FETCH_ASSOC);<br>}<br>?&gt;</p><p>&lt;!DOCTYPE html&gt;<br>&lt;html lang=\"vi\"&gt;<br>&lt;head&gt;<br>&nbsp; &nbsp;&lt;meta charset=\"UTF-8\"&gt;<br>&nbsp; &nbsp;&lt;meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\"&gt;<br>&nbsp; &nbsp;&lt;title&gt;Quản lý dịch vụ&lt;/title&gt;<br>&nbsp; &nbsp;&lt;link href=\"/2/admin/assets/vendor/fontawesome/css/all.min.css\" rel=\"stylesheet\" type=\"text/css\"&gt;<br>&nbsp; &nbsp;&lt;link href=\"/2/admin/assets/css/sb-admin-2.min.css\" rel=\"stylesheet\"&gt;<br>&nbsp; &nbsp;&lt;script src=\"https://cdn.jsdelivr.net/npm/sweetalert2@11\"&gt;&lt;/script&gt;<br>&nbsp; &nbsp;&lt;?php if ($action === \'add\' || $action === \'edit\'): ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;script src=\"https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js\"&gt;&lt;/script&gt;<br>&nbsp; &nbsp;&lt;?php endif; ?&gt;<br>&nbsp; &nbsp;&lt;style&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;.card { border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }<br>&nbsp; &nbsp; &nbsp; &nbsp;.form-group { margin-bottom: 1.5rem; }<br>&nbsp; &nbsp; &nbsp; &nbsp;.btn-secondary { background: #6c757d; border: none; }<br>&nbsp; &nbsp; &nbsp; &nbsp;.ck-editor__editable { min-height: 300px; }<br>&nbsp; &nbsp; &nbsp; &nbsp;.table-responsive { margin-top: 20px; }<br>&nbsp; &nbsp; &nbsp; &nbsp;.pagination { justify-content: center; margin-top: 20px; }<br>&nbsp; &nbsp; &nbsp; &nbsp;.page-item.disabled .page-link { pointer-events: none; opacity: 0.6; }<br>&nbsp; &nbsp; &nbsp; &nbsp;@media (max-width: 768px) {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;.table { font-size: 0.9em; }<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;.form-group label, .form-group input, .form-group select, .form-group textarea { font-size: 0.9em; }<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;.btn { font-size: 0.9em; }<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;.pagination { font-size: 0.9em; }<br>&nbsp; &nbsp; &nbsp; &nbsp;}<br>&nbsp; &nbsp;&lt;/style&gt;<br>&lt;/head&gt;<br>&lt;body&gt;<br>&nbsp; &nbsp;&lt;!-- Hiển thị thông báo --&gt;<br>&nbsp; &nbsp;&lt;?php if (isset($_SESSION[\'message\'])): ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;script&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Swal.fire({<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;icon: \'&lt;?php echo $_SESSION[\'message\'][\'type\']; ?&gt;\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;title: \'&lt;?php echo $_SESSION[\'message\'][\'type\'] === \'success\' ? \'Thành công\' : \'Lỗi\'; ?&gt;\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;html: \'&lt;?php echo htmlspecialchars($_SESSION[\'message\'][\'text\']); ?&gt;\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;confirmButtonText: \'OK\'<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;});<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;/script&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;?php unset($_SESSION[\'message\']); ?&gt;<br>&nbsp; &nbsp;&lt;?php endif; ?&gt;</p><p>&nbsp; &nbsp;&lt;?php if ($action === \'list\'): ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;!-- Danh sách dịch vụ --&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"d-sm-flex align-items-center justify-content-between mb-4\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;h1 class=\"h3 mb-0 text-gray-800\"&gt;Danh sách dịch vụ&lt;/h1&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;a href=\"?page=service&amp;action=add\" class=\"btn btn-primary\"&gt;Thêm dịch vụ&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;</p><p>&nbsp; &nbsp; &nbsp; &nbsp;&lt;!-- Form tìm kiếm --&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"card shadow mb-4\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"card-body\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;form method=\"GET\" action=\"?page=service\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"input-group\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;input type=\"text\" class=\"form-control\" name=\"s\" placeholder=\"Tìm theo tên dịch vụ hoặc mô tả...\" value=\"&lt;?php echo htmlspecialchars($search); ?&gt;\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"input-group-append\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;button class=\"btn btn-primary\" type=\"submit\"&gt;&lt;i class=\"fas fa-search\"&gt;&lt;/i&gt; Tìm&lt;/button&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/form&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;</p><p>&nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"card shadow mb-4\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"card-header py-3\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;h6 class=\"m-0 font-weight-bold text-primary\"&gt;Danh sách dịch vụ&lt;/h6&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"card-body\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"table-responsive\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;table class=\"table table-bordered\" id=\"dataTable\" width=\"100%\" cellspacing=\"0\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;thead&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;tr&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;th&gt;STT&lt;/th&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;th&gt;Tên dịch vụ&lt;/th&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;th&gt;Mô tả&lt;/th&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;th&gt;Ảnh đại diện&lt;/th&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;th&gt;Trạng thái&lt;/th&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;th&gt;Ngày tạo&lt;/th&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;th&gt;Hành động&lt;/th&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/tr&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/thead&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;tbody&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;?php if (empty($services)): ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;tr&gt;&lt;td colspan=\"7\" class=\"text-center\"&gt;Không tìm thấy dịch vụ nào.&lt;/td&gt;&lt;/tr&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;?php else: ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;?php foreach ($services as $index =&gt; $svc): ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;tr&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;td&gt;&lt;?php echo $index + 1 + $offset; ?&gt;&lt;/td&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;td&gt;&lt;?php echo htmlspecialchars($svc[\'title\']); ?&gt;&lt;/td&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;td&gt;&lt;?php echo htmlspecialchars(substr($svc[\'description\'], 0, 100)) . (strlen($svc[\'description\']) &gt; 100 ? \'...\' : \'\'); ?&gt;&lt;/td&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;td&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;?php if ($svc[\'thumbnail\']): ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;img src=\"/2/admin/&lt;?php echo $svc[\'thumbnail\']; ?&gt;\" width=\"50\" alt=\"Thumbnail\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;?php else: ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;N/A<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;?php endif; ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/td&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;td&gt;&lt;?php echo $svc[\'is_published\'] ? \'Hiển thị\' : \'Ẩn\'; ?&gt;&lt;/td&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;td&gt;&lt;?php echo $svc[\'created_at\']; ?&gt;&lt;/td&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;td&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;a href=\"?page=service&amp;action=edit&amp;id=&lt;?php echo $svc[\'id\']; ?&gt;\" class=\"btn btn-sm btn-primary\"&gt;&lt;i class=\"fas fa-edit\"&gt;&lt;/i&gt; Sửa&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;a href=\"#\" class=\"btn btn-sm btn-danger delete-service\" data-id=\"&lt;?php echo $svc[\'id\']; ?&gt;\" data-title=\"&lt;?php echo htmlspecialchars($svc[\'title\']); ?&gt;\"&gt;&lt;i class=\"fas fa-trash\"&gt;&lt;/i&gt; Xóa&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/td&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/tr&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;?php endforeach; ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;?php endif; ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/tbody&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/table&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;</p><p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;!-- Phân trang --&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;?php if ($total_pages &gt; 1): ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;nav aria-label=\"Page navigation\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;ul class=\"pagination\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;li class=\"page-item &lt;?php echo $page &lt;= 1 ? \'disabled\' : \'\'; ?&gt;\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;a class=\"page-link\" href=\"?page=service&amp;s=&lt;?php echo urlencode($search); ?&gt;&amp;p=&lt;?php echo $page - 1; ?&gt;\" aria-label=\"Previous\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;span aria-hidden=\"true\"&gt;«&lt;/span&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/li&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;?php for ($i = 1; $i &lt;= $total_pages; $i++): ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;li class=\"page-item &lt;?php echo $i == $page ? \'active\' : \'\'; ?&gt;\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;a class=\"page-link\" href=\"?page=service&amp;s=&lt;?php echo urlencode($search); ?&gt;&amp;p=&lt;?php echo $i; ?&gt;\"&gt;&lt;?php echo $i; ?&gt;&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/li&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;?php endfor; ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;li class=\"page-item &lt;?php echo $page &gt;= $total_pages ? \'disabled\' : \'\'; ?&gt;\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;a class=\"page-link\" href=\"?page=service&amp;s=&lt;?php echo urlencode($search); ?&gt;&amp;p=&lt;?php echo $page + 1; ?&gt;\" aria-label=\"Next\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;span aria-hidden=\"true\"&gt;»&lt;/span&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/li&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/ul&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/nav&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;?php endif; ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;</p><p>&nbsp; &nbsp;&lt;?php elseif ($action === \'add\' || $action === \'edit\'): ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;!-- Form thêm/sửa dịch vụ --&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"d-sm-flex align-items-center justify-content-between mb-4\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;h1 class=\"h3 mb-0 text-gray-800\"&gt;&lt;?php echo $action === \'edit\' ? \'Sửa dịch vụ\' : \'Thêm dịch vụ\'; ?&gt;&lt;/h1&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;a href=\"?page=service\" class=\"btn btn-secondary\"&gt;Hủy&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;</p><p>&nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"card shadow mb-4\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"card-header py-3\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;h6 class=\"m-0 font-weight-bold text-primary\"&gt;&lt;?php echo $action === \'edit\' ? \'Sửa dịch vụ\' : \'Thêm dịch vụ\'; ?&gt;&lt;/h6&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"card-body\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;form method=\"POST\" enctype=\"multipart/form-data\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;?php if ($action === \'edit\'): ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;input type=\"hidden\" name=\"id\" value=\"&lt;?php echo $service[\'id\']; ?&gt;\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;?php endif; ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"row\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"col-md-6\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"form-group\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;label for=\"title\"&gt;Tên dịch vụ &lt;span class=\"text-danger\"&gt;*&lt;/span&gt;&lt;/label&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;input type=\"text\" class=\"form-control\" id=\"title\" name=\"title\" value=\"&lt;?php echo isset($service) ? htmlspecialchars($service[\'title\']) : \'\'; ?&gt;\" required&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"form-group\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;label for=\"description\"&gt;Mô tả &lt;span class=\"text-danger\"&gt;*&lt;/span&gt;&lt;/label&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;textarea class=\"form-control\" id=\"description\" name=\"description\" rows=\"4\" required&gt;&lt;?php echo isset($service) ? htmlspecialchars($service[\'description\']) : \'\'; ?&gt;&lt;/textarea&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"form-group\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;label for=\"noidung\"&gt;Nội dung&lt;/label&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;textarea class=\"form-control\" id=\"noidung\" name=\"noidung\"&gt;&lt;?php echo isset($service) ? htmlspecialchars($service[\'content\']) : \'\'; ?&gt;&lt;/textarea&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"form-group\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;label for=\"thumbnail\"&gt;Ảnh đại diện&lt;/label&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;input type=\"file\" class=\"form-control-file\" id=\"thumbnail\" name=\"thumbnail\" accept=\"image/jpeg,image/png\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;?php if (isset($service) &amp;&amp; $service[\'thumbnail\']): ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;img src=\"/2/admin/&lt;?php echo $service[\'thumbnail\']; ?&gt;\" width=\"100\" alt=\"Thumbnail\" class=\"mt-2\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;?php endif; ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"form-group\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"form-check\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;input type=\"checkbox\" class=\"form-check-input\" id=\"is_published\" name=\"is_published\" &lt;?php echo (isset($service) &amp;&amp; $service[\'is_published\']) ? \'checked\' : \'\'; ?&gt;&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;label class=\"form-check-label\" for=\"is_published\"&gt;Hiển thị&lt;/label&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"col-md-6\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"form-group\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;label for=\"seo_title\"&gt;Tiêu đề SEO (tối đa 255 ký tự)&lt;/label&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;input type=\"text\" class=\"form-control\" id=\"seo_title\" name=\"seo_title\" maxlength=\"255\" value=\"&lt;?php echo isset($service) ? htmlspecialchars($service[\'seo_title\']) : \'\'; ?&gt;\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"form-group\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;label for=\"seo_description\"&gt;Mô tả SEO (tối đa 160 ký tự)&lt;/label&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;textarea class=\"form-control\" id=\"seo_description\" name=\"seo_description\" rows=\"3\" maxlength=\"160\"&gt;&lt;?php echo isset($service) ? htmlspecialchars($service[\'seo_description\']) : \'\'; ?&gt;&lt;/textarea&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;div class=\"form-group\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;label for=\"seo_keywords\"&gt;Từ khóa SEO (phân cách bằng dấu phẩy)&lt;/label&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;input type=\"text\" class=\"form-control\" id=\"seo_keywords\" name=\"seo_keywords\" value=\"&lt;?php echo isset($service) ? htmlspecialchars($service[\'seo_keywords\']) : \'\'; ?&gt;\"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;button type=\"submit\" class=\"btn btn-primary\"&gt;Lưu&lt;/button&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;a href=\"?page=service\" class=\"btn btn-secondary\"&gt;Hủy&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/form&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;/div&gt;<br>&nbsp; &nbsp;&lt;?php endif; ?&gt;</p><p>&nbsp; &nbsp;&lt;script src=\"/2/admin/assets/js/jquery.min.js\"&gt;&lt;/script&gt;<br>&nbsp; &nbsp;&lt;script src=\"/2/admin/assets/js/bootstrap.bundle.min.js\"&gt;&lt;/script&gt;<br>&nbsp; &nbsp;&lt;script src=\"/2/admin/assets/js/sb-admin-2.min.js\"&gt;&lt;/script&gt;<br>&nbsp; &nbsp;&lt;?php if ($action === \'add\' || $action === \'edit\'): ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;script&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;document.addEventListener(\'DOMContentLoaded\', function() {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ClassicEditor<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;.create(document.querySelector(\'#noidung\'), {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;language: \'vi\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;toolbar: [<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\'heading\', \'|\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\'bold\', \'italic\', \'underline\', \'strikethrough\', \'|\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\'fontSize\', \'fontColor\', \'fontBackgroundColor\', \'alignment\', \'|\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\'link\', \'bulletedList\', \'numberedList\', \'blockQuote\', \'|\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\'insertTable\', \'imageUpload\', \'imageResize\', \'linkImage\', \'mediaEmbed\', \'|\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\'undo\', \'redo\'<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;],<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;placeholder: \'Nhập nội dung dịch vụ...\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;height: \'400px\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;image: {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;toolbar: [<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\'imageTextAlternative\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\'imageStyle:alignLeft\', \'imageStyle:alignCenter\', \'imageStyle:alignRight\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\'imageResize\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\'linkImage\'<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;],<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;resizeOptions: [<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;{ name: \'resizeImage:original\', value: null, label: \'Kích thước gốc\' },<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;{ name: \'resizeImage:50\', value: \'50\', label: \'50%\' },<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;{ name: \'resizeImage:75\', value: \'75\', label: \'75%\' }<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;],<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;styles: [\'alignLeft\', \'alignCenter\', \'alignRight\']<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;},<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;fontSize: { options: [10, 12, 14, \'default\', 18, 20, 24, 30, 36] },<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;alignment: { options: [\'left\', \'center\', \'right\', \'justify\'] },<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ckfinder: {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;uploadUrl: \'/2/admin/pages/products/upload_ckeditor.php\'<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;},<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;mediaEmbed: { previewsInData: true }<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;})<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;.then(editor =&gt; {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;console.log(\'CKEditor initialized for noidung\');<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;})<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;.catch(error =&gt; {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;console.error(\'CKEditor initialization error for noidung:\', error);<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;});<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;});<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;/script&gt;<br>&nbsp; &nbsp;&lt;?php endif; ?&gt;<br>&nbsp; &nbsp;&lt;?php if ($action === \'list\'): ?&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;script&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;document.addEventListener(\'DOMContentLoaded\', function() {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;document.querySelectorAll(\'.delete-service\').forEach(button =&gt; {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;button.addEventListener(\'click\', function(e) {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;e.preventDefault();<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;const id = this.getAttribute(\'data-id\');<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;const title = this.getAttribute(\'data-title\');<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Swal.fire({<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;title: \'Xác nhận xóa\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;html: `Bạn có chắc muốn xóa dịch vụ \"&lt;strong&gt;${title}&lt;/strong&gt;\"?`,<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;icon: \'warning\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;showCancelButton: true,<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;confirmButtonText: \'Xóa\',<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;cancelButtonText: \'Hủy\'<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;}).then(result =&gt; {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;if (result.isConfirmed) {<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;window.location.href = `?page=service&amp;action=delete&amp;id=${id}`;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;}<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;});<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;});<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;});<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;});<br>&nbsp; &nbsp; &nbsp; &nbsp;&lt;/script&gt;<br>&nbsp; &nbsp;&lt;?php endif; ?&gt;<br>&lt;/body&gt;<br>&lt;/html&gt;<br>&lt;?php ob_end_flush(); ?&gt;</p>', 'Uploads/thumbnails/1746864456_avt2.png', 1, '', '', '', '2025-05-10 15:07:36', '2025-05-13 09:56:26');

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
(4, 'default_font_weight', 'bold', '2025-05-14 03:30:33'),
(10, 'scroll_top', '1', '2025-04-18 07:31:45'),
(13, 'columns_375', '2', '2025-04-15 13:56:12'),
(14, 'columns_425', '3', '2025-04-15 13:56:12'),
(15, 'columns_768', '4', '2025-04-15 13:47:14'),
(16, 'columns_1200', '5', '2025-04-15 13:47:14'),
(17, 'columns_max', '5', '2025-04-21 12:15:57'),
(88, 'favicon', '68073819ccee9-fhc.png', '2025-04-22 06:32:57'),
(91, 'default_bg_color', '#ffffff', '2025-05-10 03:44:49'),
(92, 'default_text_color', '#000000', '2025-04-21 03:49:32'),
(93, 'default_link_color', '#007bff', '2025-04-21 03:49:32'),
(94, 'default_opacity', '1', '2025-04-21 03:49:32'),
(114, 'review_columns_375', '1', '2025-04-23 03:44:43'),
(115, 'review_columns_425', '1', '2025-04-23 03:44:43'),
(116, 'review_columns_768', '2', '2025-04-23 03:44:43'),
(117, 'review_columns_1200', '3', '2025-04-23 03:44:43'),
(118, 'review_columns_max', '3', '2025-04-23 03:49:32'),
(169, 'partner_columns_375', '2', '2025-05-10 03:22:57'),
(170, 'partner_columns_425', '1', '2025-04-23 03:57:21'),
(171, 'partner_columns_768', '2', '2025-04-23 03:57:21'),
(172, 'partner_columns_1200', '3', '2025-04-23 03:57:21'),
(173, 'partner_columns_max', '6', '2025-04-23 04:00:43'),
(204, 'blog_columns_375', '1', '2025-04-23 09:31:43'),
(205, 'blog_columns_425', '1', '2025-04-23 09:31:43'),
(206, 'blog_columns_768', '2', '2025-04-23 09:31:43'),
(207, 'blog_columns_1200', '4', '2025-04-23 09:31:43'),
(208, 'blog_columns_max', '4', '2025-04-23 09:33:14'),
(250, 'service_columns_375', '2', '2025-05-10 08:24:00'),
(251, 'service_columns_425', '3', '2025-05-10 08:24:00'),
(252, 'service_columns_768', '4', '2025-05-10 08:24:00'),
(253, 'service_columns_1200', '5', '2025-05-10 08:24:00'),
(254, 'service_columns_max', '5', '2025-05-10 08:52:46'),
(330, 'project_columns_375', '1', '2025-05-13 03:20:34'),
(331, 'project_columns_425', '1', '2025-05-13 03:20:34'),
(332, 'project_columns_768', '2', '2025-05-13 03:20:34'),
(333, 'project_columns_1200', '3', '2025-05-13 03:20:34'),
(334, 'project_columns_max', '4', '2025-05-13 03:34:23'),
(395, 'smtp_host', 'smtp.gmail.com', '2025-05-13 07:39:51'),
(396, 'smtp_port', '465', '2025-05-13 07:39:51'),
(397, 'smtp_username', 'badaotulong123@gmail.com', '2025-05-13 07:39:51'),
(398, 'smtp_password', 'hisl ytee gyip kzat', '2025-05-13 07:39:51'),
(399, 'smtp_from', 'badaotulong123@gmail.com', '2025-05-13 07:39:51'),
(400, 'smtp_from_name', 'Thắng Raiy', '2025-05-13 07:39:51'),
(401, 'embed_code', '', '2025-05-13 08:16:01');

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

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `password`, `created_at`) VALUES
(1, 'thangweb', 'thangwebso@gmail.com', '0914476792', '$2y$10$HKGqfOl5T70LD4VJQOVP5eiric.0AzS/SMBHd1bPzM6VPlMfb.AOG', '2025-05-10 04:17:57');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `role_id` (`role_id`);

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
-- Chỉ mục cho bảng `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `attribute_values`
--
ALTER TABLE `attribute_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `attribute_id` (`attribute_id`);

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
-- Chỉ mục cho bảng `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `flash_sales`
--
ALTER TABLE `flash_sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `role_id` (`role_id`);

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
-- Chỉ mục cho bảng `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`);

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
-- Chỉ mục cho bảng `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

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
-- Chỉ mục cho bảng `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_created_at` (`created_at`);

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
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT cho bảng `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `admin_logins`
--
ALTER TABLE `admin_logins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT cho bảng `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `attribute_values`
--
ALTER TABLE `attribute_values`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
-- AUTO_INCREMENT cho bảng `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `flash_sales`
--
ALTER TABLE `flash_sales`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `images`
--
ALTER TABLE `images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- AUTO_INCREMENT cho bảng `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `partners`
--
ALTER TABLE `partners`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `product_attributes`
--
ALTER TABLE `product_attributes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `review_replies`
--
ALTER TABLE `review_replies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `services`
--
ALTER TABLE `services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=404;

--
-- AUTO_INCREMENT cho bảng `slides`
--
ALTER TABLE `slides`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_logs_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

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
-- Ràng buộc cho bảng `attribute_values`
--
ALTER TABLE `attribute_values`
  ADD CONSTRAINT `attribute_values_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attribute_values_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `flash_sales`
--
ALTER TABLE `flash_sales`
  ADD CONSTRAINT `flash_sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`),
  ADD CONSTRAINT `images_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

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
