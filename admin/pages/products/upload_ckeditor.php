<?php
session_start();

// Kiểm tra quyền truy cập
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Không có quyền truy cập']);
    exit;
}

// Đường dẫn lưu trữ ảnh
$uploadDir = 'C:/laragon/www/2/admin/uploads/ckeditor/';
$baseUrl = 'http://localhost/2/admin/uploads/ckeditor/';

// Tạo thư mục nếu chưa tồn tại
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload'])) {
    $file = $_FILES['upload'];
    $fileName = uniqid() . '_' . basename($file['name']);
    $filePath = $uploadDir . $fileName;
    $fileUrl = $baseUrl . $fileName;

    // Kiểm tra loại file và kích thước
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['error' => 'Chỉ cho phép file JPEG, PNG, hoặc GIF']);
        exit;
    }
    if ($file['size'] > $maxSize) {
        echo json_encode(['error' => 'File quá lớn, tối đa 5MB']);
        exit;
    }

    // Di chuyển file vào thư mục uploads
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        echo json_encode([
            'uploaded' => true,
            'url' => $fileUrl
        ]);
    } else {
        echo json_encode(['error' => 'Không thể tải file lên']);
    }
} else {
    echo json_encode(['error' => 'Yêu cầu không hợp lệ']);
}
?>