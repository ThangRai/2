<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Hàm createSlug
function createSlug($string, $pdo) {
    $search = [
        'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
        'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
        'ì','í','ị','ỉ','ĩ',
        'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
        'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
        'ỳ','ý','ỵ','ỷ','ỹ',
        'đ','À','Á','Ạ','Ả','Ã','Â','Ầ','Ấ','Ậ','Ẩ','Ẫ','Ă','Ằ','Ắ','Ặ','Ẳ','Ẵ',
        'È','É','Ẹ','Ẻ','Ẽ','Ê','Ề','Ế','Ệ','Ể','Ẽ',
        'Ì','Í','Ị','Ỉ','Ĩ',
        'Ò','Ó','Ọ','Ỏ','Õ','Ô','Ồ','Ố','Ộ','Ổ','Ỗ','Ơ','Ờ','Ớ','Ợ','Ở','Ỡ',
        'Ù','Ú','Ụ','Ủ','Ũ','Ư','Ừ','Ứ','Ự','Ử','Ữ',
        'Ỳ','Ý','Ỵ','Ỷ','Ỹ',
        'Đ'
    ];
    $replace = [
        'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
        'e','e','e','e','e','e','e','e','e','e','e',
        'i','i','i','i','i',
        'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
        'u','u','u','u','u','u','u','u','u','u','u',
        'y','y','y','y','y',
        'd','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A',
        'E','E','E','E','E','E','E','E','E','E','E',
        'I','I','I','I','I',
        'O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O',
        'U','U','U','U','U','U','U','U','U','U','U',
        'Y','Y','Y','Y','Y',
        'D'
    ];
    $string = str_replace($search, $replace, $string);
    $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
    $string = strtolower(trim(preg_replace('/\s+/', '-', $string), '-'));
    $baseSlug = $string;
    $counter = 1;
    while (true) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM blogs WHERE slug = ?");
        $stmt->execute([$string]);
        if ($stmt->fetchColumn() == 0) {
            break;
        }
        $string = $baseSlug . '-' . $counter++;
    }
    return $string;
}

// Xử lý thêm/sửa bài viết
$action = isset($_GET['action']) ? $_GET['action'] : 'add';
$blog = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->execute([$id]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$blog) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Bài viết không tồn tại.'];
        echo '<script>window.location.href="?page=blog";</script>';
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug POST và FILES
    error_log('POST data: ' . print_r($_POST, true));
    error_log('FILES data: ' . print_r($_FILES, true));

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $content = trim($_POST['noidung'] ?? '');
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $seo_title = trim($_POST['seo_title'] ?? '');
    $seo_description = trim($_POST['seo_description'] ?? '');
    $seo_keywords = trim($_POST['seo_keywords'] ?? '');

    // Kiểm tra lỗi
    $errors = [];
    if (empty($title)) $errors[] = 'Tiêu đề không được để trống.';
    if (empty($description)) $errors[] = 'Mô tả không được để trống.';
    if (!empty($_FILES['thumbnail']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB
        if (!in_array($_FILES['thumbnail']['type'], $allowed_types)) {
            $errors[] = 'Ảnh đại diện phải là định dạng JPG hoặc PNG.';
        }
        if ($_FILES['thumbnail']['size'] > $max_size) {
            $errors[] = 'Ảnh đại diện không được lớn hơn 2MB.';
        }
    }
    if (!empty($seo_title) && strlen($seo_title) > 255) {
        $errors[] = 'Tiêu đề SEO không được vượt quá 255 ký tự.';
    }
    if (!empty($seo_description) && strlen($seo_description) > 160) {
        $errors[] = 'Mô tả SEO không được vượt quá 160 ký tự.';
    }

    if (empty($errors)) {
        try {
            // Xử lý ảnh đại diện
            $thumbnail = $blog['thumbnail'] ?? null;
            if (!empty($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $target_dir = "Uploads/thumbnails/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                $thumbnail = $target_dir . time() . '_' . basename($_FILES['thumbnail']['name']);
                if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnail)) {
                    throw new Exception('Lỗi khi tải lên ảnh đại diện.');
                }
                error_log('Thumbnail uploaded: ' . $thumbnail);
            }

            // Chuẩn bị và thực thi SQL
            if ($action === 'edit' && isset($_POST['id'])) {
                $id = (int)$_POST['id'];
                $stmt = $pdo->prepare("UPDATE blogs SET title = ?, description = ?, content = ?, thumbnail = ?, is_published = ?, seo_title = ?, seo_description = ?, seo_keywords = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$title, $description, $content, $thumbnail, $is_published, $seo_title, $seo_description, $seo_keywords, $id]);
                error_log('Updated blog ID: ' . $id . ', Content: ' . substr($content, 0, 100) . ', Rows affected: ' . $stmt->rowCount());
            } else {
                $slug = createSlug($title, $pdo);
                $stmt = $pdo->prepare("INSERT INTO blogs (title, slug, description, content, thumbnail, is_published, seo_title, seo_description, seo_keywords, created_at) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$title, $slug, $description, $content, $thumbnail, $is_published, $seo_title, $seo_description, $seo_keywords]);
                error_log('Inserted new blog: ' . $title . ', Content: ' . substr($content, 0, 100) . ', New ID: ' . $pdo->lastInsertId());
            }

            $_SESSION['message'] = ['type' => 'success', 'text' => $action === 'edit' ? 'Cập nhật bài viết thành công.' : 'Thêm bài viết thành công.'];
            echo '<script>window.location.href="?page=blog";</script>';
            exit;
        } catch (Exception $e) {
            error_log('Blog save error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi lưu bài viết: ' . $e->getMessage()];
        }
    } else {
        error_log('Validation errors: ' . implode(', ', $errors));
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $action === 'edit' ? 'Sửa bài viết' : 'Thêm bài viết'; ?></title>
    <link href="/2/admin/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="/2/admin/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <style>
        .card { border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .form-group { margin-bottom: 1.5rem; }
        .btn-secondary { background: #6c757d; border: none; }
        .ck-editor__editable { min-height: 300px; }
        @media (max-width: 768px) {
            .form-group label, .form-group input, .form-group select, .form-group textarea { font-size: 0.9em; }
            .btn { font-size: 0.9em; }
        }
    </style>
</head>
<body>
    <!-- Hiển thị thông báo -->
    <?php if (isset($_SESSION['message'])): ?>
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

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo $action === 'edit' ? 'Sửa bài viết' : 'Thêm bài viết'; ?></h1>
        <a href="?page=blog" class="btn btn-secondary">Hủy</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $action === 'edit' ? 'Sửa bài viết' : 'Thêm bài viết'; ?></h6>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?php if ($action === 'edit'): ?>
                    <input type="hidden" name="id" value="<?php echo $blog['id']; ?>">
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($blog) ? htmlspecialchars($blog['title']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Mô tả <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo isset($blog) ? htmlspecialchars($blog['description']) : ''; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="noidung">Nội dung</label>
                            <textarea class="form-control" id="noidung" name="noidung"><?php echo isset($blog) ? htmlspecialchars($blog['content']) : ''; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="thumbnail">Ảnh đại diện</label>
                            <input type="file" class="form-control-file" id="thumbnail" name="thumbnail" accept="image/jpeg,image/png">
                            <?php if (isset($blog) && $blog['thumbnail']): ?>
                                <img src="/2/admin/<?php echo $blog['thumbnail']; ?>" width="100" alt="Thumbnail" class="mt-2">
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_published" name="is_published" <?php echo (isset($blog) && $blog['is_published']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_published">Hiển thị</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="seo_title">Tiêu đề SEO (tối đa 255 ký tự)</label>
                            <input type="text" class="form-control" id="seo_title" name="seo_title" maxlength="255" value="<?php echo isset($blog) ? htmlspecialchars($blog['seo_title']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="seo_description">Mô tả SEO (tối đa 160 ký tự)</label>
                            <textarea class="form-control" id="seo_description" name="seo_description" rows="3" maxlength="160"><?php echo isset($blog) ? htmlspecialchars($blog['seo_description']) : ''; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="seo_keywords">Từ khóa SEO (phân cách bằng dấu phẩy)</label>
                            <input type="text" class="form-control" id="seo_keywords" name="seo_keywords" value="<?php echo isset($blog) ? htmlspecialchars($blog['seo_keywords']) : ''; ?>">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="?page=blog" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
<script src="/2/admin/assets/js/jquery.min.js"></script>
<script src="/2/admin/assets/js/bootstrap.bundle.min.js"></script>
<script src="/2/admin/assets/js/sb-admin-2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            ClassicEditor
                .create(document.querySelector('#noidung'), {
                    language: 'vi',
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'fontSize', 'fontColor', 'fontBackgroundColor', 'alignment', '|',
                        'link', 'bulletedList', 'numberedList', 'blockQuote', '|',
                        'insertTable', 'imageUpload', 'imageResize', 'linkImage', 'mediaEmbed', '|',
                        'undo', 'redo'
                    ],
                    placeholder: 'Nhập nội dung bài viết...',
                    height: '400px',
                    image: {
                        toolbar: [
                            'imageTextAlternative',
                            'imageStyle:alignLeft', 'imageStyle:alignCenter', 'imageStyle:alignRight',
                            'imageResize',
                            'linkImage'
                        ],
                        resizeOptions: [
                            { name: 'resizeImage:original', value: null, label: 'Kích thước gốc' },
                            { name: 'resizeImage:50', value: '50', label: '50%' },
                            { name: 'resizeImage:75', value: '75', label: '75%' }
                        ],
                        styles: ['alignLeft', 'alignCenter', 'alignRight']
                    },
                    fontSize: { options: [10, 12, 14, 'default', 18, 20, 24, 30, 36] },
                    alignment: { options: ['left', 'center', 'right', 'justify'] },
                    ckfinder: {
                        uploadUrl: '/2/admin/pages/products/upload_ckeditor.php'
                    },
                    mediaEmbed: { previewsInData: true }
                })
                .then(editor => {
                    console.log('CKEditor initialized for noidung');
                })
                .catch(error => {
                    console.error('CKEditor initialization error for noidung:', error);
                });
        });
    </script>
</body>
</html>