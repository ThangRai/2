<?php
try {
    require_once 'C:/laragon/www/2/admin/config/db_connect.php';
    // Lấy thông tin liên hệ
    $stmt = $pdo->query("SELECT type, value, icon, `order`, status FROM contact_info WHERE status = 1 ORDER BY `order`, id");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Lấy cài đặt khác (nút Top, mã nhúng)
    $stmt = $pdo->query("SELECT name, value FROM settings WHERE name IN ('scroll_top', 'embed_code')");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $scroll_top = isset($settings['scroll_top']) ? (int)$settings['scroll_top'] : 1;
    $embed_code = $settings['embed_code'] ?? '';
} catch (Exception $e) {
    error_log('Fetch footer error: ' . $e->getMessage());
    $contacts = [];
    $scroll_top = 1;
    $embed_code = '';
}
?>

<style>
/* Footer Styles */
.footer {
    color:rgb(75, 75, 75);
    position: relative;
    font-family: 'Roboto', sans-serif;
}
.footer .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}
.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}
.footer-section h3 {
    font-size: 1.5em;
    font-weight: 700;
    margin-bottom: 20px;
    background: linear-gradient(90deg, #3b82f6, #93c5fd);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.footer-section p, .footer-section li {
    font-size: 1em;
    line-height: 1.6;
    color: #d1d5db;
}
.footer-section ul {
    list-style: none;
    padding: 0;
}
.footer-section ul li {
    margin-bottom: 10px;
}
.footer-section ul li a {
    color: #d1d5db;
    text-decoration: none;
    transition: color 0.3s ease;
}
.footer-section ul li a:hover {
    color: #3b82f6;
}
.footer-section .contact-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}
.footer-section .contact-item img {
    width: 24px;
    height: 24px;
    object-fit: cover;
}
.footer-section .contact-item a {
    color: #d1d5db;
    text-decoration: none;
    transition: color 0.3s ease;
}
.footer-section .contact-item a:hover {
    color: #3b82f6;
}
.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 20px;
    text-align: center;
    font-size: 0.9em;
}
.footer-bottom p {
    margin: 0;
}

/* Contact Box Styles */
.contact-box {
    position: fixed;
    bottom: 20px;
    left: 20px;
    z-index: 1000;
    width: 60px;
    background: linear-gradient(135deg, #ffffff, #e8f0fe);
    border-radius: 12px;
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
    animation: fadeScale 0.6s ease-out;
}
.contact-box.hidden {
    transform: translateX(-110%);
}
.contact-toggle {
    position: absolute;
    top: -50px;
    left: 50%;
    transform: translateX(-50%);
    width: 44px;
    height: 44px;
    background: linear-gradient(45deg, #3498db, #2980b9);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.25);
    transition: all 0.3s ease;
}
.contact-toggle:hover {
    transform: translateX(-50%) rotate(360deg);
    background: linear-gradient(45deg, #2980b9, #1b6ca8);
}
.contact-toggle i {
    font-size: 1.3em;
}
.contact-item {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px;
    transition: all 0.3s ease;
}
.contact-item:hover {
    background: rgba(52, 152, 219, 0.1);
    transform: scale(1.1);
}
.contact-item a {
    display: block;
}
.contact-item img {
    width: 40px;
    height: 40px;
    object-fit: cover;
}
.contact-item.no-icon {
    display: none;
}

/* Scroll Top Styles */
.scroll-top {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: #fff;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 1000;
    opacity: 0;
    transition: opacity 0.3s, transform 0.3s;
}
.scroll-top.show {
    opacity: 1;
}
.scroll-top:hover {
    transform: scale(1.1);
}

/* Animations */
@keyframes fadeScale {
    from {
        opacity: 0;
        transform: scale(0.8) translateX(-50%);
    }
    to {
        opacity: 1;
        transform: scale(1) translateX(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .footer {
        padding: 40px 0 20px;
    }
    .footer-content {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    .footer-section h3 {
        font-size: 1.3em;
    }
    .footer-section p, .footer-section li, .footer-section .contact-item a {
        font-size: 0.95em;
    }
    .contact-box {
        width: 50px;
        bottom: 15px;
        left: 15px;
    }
    .contact-toggle {
        top: -45px;
        width: 40px;
        height: 40px;
    }
    .contact-item {
        padding: 8px;
    }
    .contact-item img {
        width: 36px;
        height: 36px;
    }
    .scroll-top {
        bottom: 15px;
        right: 15px;
        width: 40px;
        height: 40px;
    }
}
@media (max-width: 425px) {
    .footer {
        padding: 30px 0 15px;
    }
    .footer-section h3 {
        font-size: 1.2em;
    }
    .footer-section p, .footer-section li, .footer-section .contact-item a {
        font-size: 0.9em;
    }
    .footer-bottom {
        font-size: 0.85em;
    }
}
</style>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <!-- About Section -->
            <!-- <div class="footer-section about">
                <h3>Về Chúng Tôi</h3>
                <p>
                    Công ty TNHH Thắng Raiy cung cấp các sản phẩm chất lượng cao, dịch vụ tận tâm, và trải nghiệm mua sắm tuyệt vời.
                </p>
            </div>

            <!-- Quick Links Section -->
            <!-- <div class="footer-section links">
                <h3>Liên Kết Nhanh</h3>
                <ul>
                    <li><a href="/2/public/">Trang Chủ</a></li>
                    <li><a href="/2/public/pages/products.php">Sản Phẩm</a></li>
                    <li><a href="/2/public/pages/cart.php">Giỏ Hàng</a></li>
                    <li><a href="/2/public/pages/checkout.php">Thanh Toán</a></li>
                    <li><a href="/2/public/pages/contact.php">Liên Hệ</a></li>
                </ul>
            </div> -->

            <!-- Contact Section -->
            <!-- <div class="footer-section contact">
                <h3>Thông Tin Liên Hệ</h3>
                <?php foreach ($contacts as $contact): ?>
                    <div class="contact-item">
                        <?php if ($contact['icon']): ?>
                            <img src="/2/admin/uploads/contact/<?php echo htmlspecialchars($contact['icon'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($contact['type'], ENT_QUOTES); ?>">
                        <?php endif; ?>
                        <a href="<?php
                            $link = '';
                            switch ($contact['type']) {
                                case 'hotline':
                                    $link = 'tel:' . preg_replace('/[^0-9+]/', '', $contact['value']);
                                    break;
                                case 'email':
                                    $link = 'mailto:' . $contact['value'];
                                    break;
                                case 'zalo':
                                    $value = trim($contact['value']);
                                    if (preg_match('/^[0-9+\-\s]+$/', $value)) {
                                        $link = 'https://zalo.me/' . preg_replace('/[^0-9]/', '', $value);
                                    } elseif (preg_match('/^https?:\/\/zalo\.me\//i', $value)) {
                                        $link = $value;
                                    } else {
                                        $link = '#';
                                    }
                                    break;
                                case 'facebook':
                                    $link = $contact['value'];
                                    break;
                                default:
                                    $link = '#';
                            }
                            echo htmlspecialchars($link, ENT_QUOTES);
                        ?>" target="_blank">
                            <?php echo htmlspecialchars($contact['value'], ENT_QUOTES); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div> --> 

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Công ty TNHH Thắng Raiy. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Contact Box -->
<?php if (!empty($contacts)): ?>
    <div class="contact-box" id="contactBox">
        <div class="contact-toggle" id="contactToggle" onclick="toggleContactBox()">
            <i class="fas fa-comments"></i>
        </div>
        <?php foreach ($contacts as $contact): ?>
            <div class="contact-item <?php echo empty($contact['icon']) ? 'no-icon' : ''; ?>">
                <?php if ($contact['icon']): ?>
                    <a href="<?php
                        $link = '';
                        switch ($contact['type']) {
                            case 'hotline':
                                $link = 'tel:' . preg_replace('/[^0-9+]/', '', $contact['value']);
                                break;
                            case 'email':
                                $link = 'mailto:' . $contact['value'];
                                break;
                            case 'zalo':
                                $value = trim($contact['value']);
                                if (preg_match('/^[0-9+\-\s]+$/', $value)) {
                                    $link = 'https://zalo.me/' . preg_replace('/[^0-9]/', '', $value);
                                } elseif (preg_match('/^https?:\/\/zalo\.me\//i', $value)) {
                                    $link = $value;
                                } else {
                                    $link = '#';
                                }
                                break;
                            case 'facebook':
                                $link = $contact['value'];
                                break;
                            default:
                                $link = '#';
                        }
                        echo htmlspecialchars($link, ENT_QUOTES);
                    ?>" target="_blank">
                        <img src="/2/admin/uploads/contact/<?php echo htmlspecialchars($contact['icon'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($contact['type'], ENT_QUOTES); ?>">
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
    function toggleContactBox() {
        var box = document.getElementById('contactBox');
        var toggle = document.getElementById('contactToggle');
        box.classList.toggle('hidden');
        toggle.innerHTML = box.classList.contains('hidden') ? '<i class="fas fa-comments"></i>' : '<i class="fas fa-times"></i>';
    }
    </script>
<?php endif; ?>

<!-- Scroll Top -->
<?php if ($scroll_top): ?>
    <div class="scroll-top" id="scrollTop" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fas fa-arrow-up"></i>
    </div>
    <script>
    window.addEventListener('scroll', function() {
        var scrollTop = document.getElementById('scrollTop');
        if (window.scrollY > 200) {
            scrollTop.classList.add('show');
        } else {
            scrollTop.classList.remove('show');
        }
    });
    </script>
<?php endif; ?>

<!-- Embed Code -->
<?php echo $embed_code; ?>