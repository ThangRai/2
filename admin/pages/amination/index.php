<?php
// Danh sách 78 hiệu ứng từ Animate.css (phiên bản 4.1.1)
$animations = [
    'bounce', 'flash', 'pulse', 'rubberBand', 'shakeX', 'shakeY', 'headShake', 'swing', 'tada', 'wobble', 'jello', 'heartBeat',
    'backInDown', 'backInLeft', 'backInRight', 'backInUp',
    'backOutDown', 'backOutLeft', 'backOutRight', 'backOutUp',
    'bounceIn', 'bounceInDown', 'bounceInLeft', 'bounceInRight', 'bounceInUp',
    'bounceOut', 'bounceOutDown', 'bounceOutLeft', 'bounceOutRight', 'bounceOutUp',
    'fadeIn', 'fadeInDown', 'fadeInDownBig', 'fadeInLeft', 'fadeInLeftBig', 'fadeInRight', 'fadeInRightBig', 'fadeInUp', 'fadeInUpBig',
    'fadeOut', 'fadeOutDown', 'fadeOutDownBig', 'fadeOutLeft', 'fadeOutLeftBig', 'fadeOutRight', 'fadeOutRightBig', 'fadeOutUp', 'fadeOutUpBig',
    'flip', 'flipInX', 'flipInY', 'flipOutX', 'flipOutY',
    'lightSpeedInRight', 'lightSpeedInLeft', 'lightSpeedOutRight', 'lightSpeedOutLeft',
    'rotateIn', 'rotateInDownLeft', 'rotateInDownRight', 'rotateInUpLeft', 'rotateInUpRight',
    'rotateOut', 'rotateOutDownLeft', 'rotateOutDownRight', 'rotateOutUpLeft', 'rotateOutUpRight',
    'hinge', 'jackInTheBox', 'rollIn', 'rollOut',
    'zoomIn', 'zoomInDown', 'zoomInLeft', 'zoomInRight', 'zoomInUp',
    'zoomOut', 'zoomOutDown', 'zoomOutLeft', 'zoomOutRight', 'zoomOutUp',
    'slideInDown', 'slideInLeft', 'slideInRight', 'slideInUp',
    'slideOutDown', 'slideOutLeft', 'slideOutRight', 'slideOutUp'
];
?>

<h1 class="h3 mb-4 text-gray-800">Danh sách Hiệu ứng Animate.css</h1>
<p class="mb-4">Danh sách các hiệu ứng Animate.css. Nhấp vào hiệu ứng để xem trước và sao chép mã.</p>

<!-- Khu vực xem trước -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Xem trước</h6>
    </div>
    <div class="card-body text-center">
        <div id="preview-box" class="animate__animated d-inline-block p-4 bg-primary text-white rounded" style="font-size: 24px;">
            <i class="fas fa-star"></i> Xem trước
        </div>
    </div>
</div>

<!-- Lưới hiệu ứng -->
<div class="row" id="animation-grid">
    <?php foreach ($animations as $animation): ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 animation-card" data-animation="animate__<?php echo htmlspecialchars($animation); ?>">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="animate__animated animate__<?php echo htmlspecialchars($animation); ?> mb-2">
                        <i class="fas fa-film fa-3x text-primary"></i>
                    </div>
                    <p class="mb-1"><strong><?php echo htmlspecialchars($animation); ?></strong></p>
                    <code class="copy-code" data-code='<div class="animate__animated animate__<?php echo htmlspecialchars($animation); ?>">Nội dung của bạn</div>'>
                        animate__<?php echo htmlspecialchars($animation); ?>
                    </code>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Thông báo sao chép -->
<div class="toast" id="copyToast" style="position: fixed; bottom: 20px; right: 20px;" data-delay="2000">
    <div class="toast-body bg-success text-white">
        Đã sao chép mã hiệu ứng!
    </div>
</div>

<link rel="stylesheet" href="/2/admin/assets/css/animate.min.css">
<style>
    .copy-code {
        cursor: pointer;
        background: #f8f9fa;
        padding: 5px 10px;
        border-radius: 4px;
        display: inline-block;
        transition: background 0.2s;
    }
    .copy-code:hover {
        background: #e9ecef;
    }
    .card:hover {
        transform: translateY(-5px);
        transition: transform 0.3s;
    }
    #preview-box {
        min-height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
<script>
    // Áp dụng hiệu ứng khi bấm
    document.querySelectorAll('.animation-card').forEach(card => {
        card.addEventListener('click', function() {
            const animationClass = this.getAttribute('data-animation');
            const previewBox = document.getElementById('preview-box');
            
            // Xóa các lớp hiệu ứng trước
            previewBox.className = 'animate__animated d-inline-block p-4 bg-primary text-white rounded';
            // Thêm lớp hiệu ứng mới
            previewBox.classList.add(animationClass);
            
            // Buộc reflow để chạy lại hiệu ứng
            void previewBox.offsetWidth;
        });
    });

    // Sao chép mã
    document.querySelectorAll('.copy-code').forEach(code => {
        code.addEventListener('click', function(e) {
            e.stopPropagation(); // Ngăn kích hoạt sự kiện bấm card
            const textToCopy = this.getAttribute('data-code');
            navigator.clipboard.writeText(textToCopy).then(() => {
                const toast = document.getElementById('copyToast');
                $(toast).toast('show');
            }).catch(err => {
                console.error('Lỗi khi sao chép mã: ', err);
            });
        });
    });
</script>