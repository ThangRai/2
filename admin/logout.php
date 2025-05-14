<?php
// Bắt đầu phiên
session_start();

// Xóa tất cả session
session_unset();
session_destroy();

// Xóa thời gian bắt đầu khỏi localStorage trong trình duyệt của người dùng
echo "<script>
    localStorage.removeItem('startTime');
    window.location.href = 'index.php';  // Điều hướng người dùng về trang đăng nhập (hoặc trang chủ)
</script>";
?>
