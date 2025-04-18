<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Lấy thông tin liên hệ từ bảng contacts_info
$contacts_info = $pdo->query("SELECT * FROM contacts_info LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// Xử lý biểu mẫu liên hệ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $errors = [];

    if (empty($name)) {
        $errors[] = 'Tên không được để trống';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }
    if (!empty($phone) && !preg_match('/^\+?[0-9]{9,15}$/', $phone)) {
        $errors[] = 'Số điện thoại không hợp lệ';
    }
    if (empty($message)) {
        $errors[] = 'Nội dung không được để trống';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contacts (name, email, phone, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone ?: null, $message]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Gửi thông tin liên hệ thành công! Chúng tôi sẽ phản hồi sớm.'];
        } catch (Exception $e) {
            error_log('Lỗi lưu liên hệ: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi gửi thông tin: ' . $e->getMessage()];
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .contact-section {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-bottom: 40px;
        }
        .contact-section h1 {
            font-weight: 700;
            color: #1a2e35;
            margin-bottom: 30px;
        }
        .contact-info {
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa, #e4e9f0);
            border-radius: 10px;
            transition: transform 0.3s ease;
        }
        .contact-info:hover {
            transform: translateY(-5px);
        }
        .contact-info i {
            color: #007bff;
            margin-right: 15px;
            font-size: 1.2rem;
        }
        .contact-info p {
            margin-bottom: 15px;
            font-size: 1.1rem;
            color: #333;
        }
        .contact-form {
            padding: 20px;
        }
        .form-group {
            position: relative;
            margin-bottom: 25px;
        }
        .form-group i {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #007bff;
            font-size: 1rem; /* Giảm kích thước để tránh che */
            z-index: 1;
        }
        .form-control {
            padding-left: 40px; /* Giảm để căn chỉnh gọn */
            border-radius: 25px;
            border: 1px solid #ced4da;
            box-shadow: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            font-size: 1rem;
        }
        .input-icon {
    position: absolute;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    z-index: 2;
    color: #007bff;
    font-size: 1rem;
}

.textarea-group .input-icon {
    top: 7px;
    transform: none;
}

.form-control {
    padding-left: 30px !important;
    border-radius: 25px;
    border: 1px solid #ced4da;
    box-shadow: none;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    font-size: 1rem;
}

textarea.form-control {
    border-radius: 15px;
    padding: 15px 15px 15px 50px;
    resize: none;
}

        /* Thêm padding cho placeholder */
        .form-control::placeholder {
            padding-left: 10px; /* Đẩy placeholder ra sau biểu tượng */
            color: #6c757d;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
        }
        /* Tách CSS cho textarea */
        textarea.form-control {
            padding: 15px;
            border-radius: 15px;
            resize: none;
        }
        /* Biểu tượng cho textarea */
        .form-group.textarea-group i {
            top: 15px;
            transform: none;
        }
        .btn-primary {
            background: #007bff;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            background: #0056b3;
            transform: scale(1.05);
        }
        .map-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .map-container:hover {
            transform: scale(1.02);
        }
        .map-container iframe {
            width: 100%;
            height: 400px;
            border: 0;
        }
        @media (max-width: 767px) {
            .contact-section {
                padding: 20px;
            }
            .contact-info, .contact-form {
                padding: 15px;
            }
            .form-control {
                padding-left: 35px;
            }
            .form-group i {
                left: 8px;
                font-size: 0.9rem;
            }
            .form-control::placeholder {
                padding-left: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'C:/laragon/www/2/public/includes/header.php'; ?>

    <div class="container my-5">
        <div class="contact-section">
            <h1 class="text-center">Liên hệ với chúng tôi</h1>
            <div class="row">
                <!-- Cột thông tin liên hệ (6) -->
                <div class="col-md-6 mb-4">
                    <h3 class="mb-3">Thông tin liên hệ</h3>
                    <div class="contact-info">
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($contacts_info['address'] ?? 'Chưa thiết lập địa chỉ'); ?></p>
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($contacts_info['phone'] ?? 'Chưa thiết lập số điện thoại'); ?></p>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($contacts_info['email'] ?? 'Chưa thiết lập email'); ?></p>
                        <p><i class="fas fa-clock"></i> <?php echo htmlspecialchars($contacts_info['working_hours'] ?? 'Chưa thiết lập giờ làm việc'); ?></p>
                    </div>
                </div>

                <!-- Cột biểu mẫu liên hệ (6) -->
                <div class="col-md-6 mb-4">
                    <h3 class="mb-3">Gửi thông tin cho chúng tôi</h3>
                    <div class="contact-form">
                        <form method="POST">
                            <div class="form-group position-relative">
                                <span class="input-icon"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" name="name" placeholder="Họ và tên"
                                    value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                            </div>
                            <div class="form-group position-relative">
                                <span class="input-icon"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" name="email" placeholder="Email"
                                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                            </div>
                            <div class="form-group position-relative">
                                <span class="input-icon"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-control" name="phone" placeholder="Số điện thoại"
                                    value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
                            </div>
                            <div class="form-group position-relative textarea-group">
                                <span class="input-icon"><i class="fas fa-comment"></i></span>
                                <textarea class="form-control" name="message" rows="5" placeholder="Nội dung"
                                    required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Gửi</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <!-- Bản đồ Google Maps -->
        <div class="map-container">
            <?php echo $contacts_info['map_iframe'] ?? '<p class="text-muted text-center py-5">Chưa thiết lập bản đồ.</p>'; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'C:/laragon/www/2/public/includes/footer.php'; ?>

    <!-- Hiển thị thông báo SweetAlert -->
    <?php if (isset($_SESSION['message'])): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: '<?php echo $_SESSION['message']['type']; ?>',
                title: '<?php echo $_SESSION['message']['type'] === 'success' ? 'Thành công' : 'Lỗi'; ?>',
                html: '<?php echo htmlspecialchars($_SESSION['message']['text']); ?>',
                confirmButtonText: 'OK'
            });
        </script>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>