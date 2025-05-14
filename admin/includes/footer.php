<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; <?php echo SITE_NAME; ?> <?php echo date('Y'); ?></span>
        </div>
    </div>
    <!-- Thêm nút Top và Hỗ trợ -->
    <div class="footer-buttons">
        <button id="scrollTopButton" class="btn btn-primary btn-circle">
            <i class="fas fa-arrow-up"></i>
        </button>
        <button id="supportButton" class="btn btn-info btn-circle">
            <i class="fas fa-headset"></i>
        </button>
    </div>
</footer>

<style>
    .footer-buttons {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }

    .footer-buttons .btn {
        margin-bottom: 10px;
        display: block;
    }

    #scrollTopButton {
        background-color: #007bff;
        color: white;
        border-radius: 50%;
        padding: 10px;
        display: none; /* Ban đầu ẩn đi */
        position: fixed;
        bottom: 70px;
        right: 20px;
        z-index: 1000;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    #scrollTopButton:hover {
        background-color: #0056b3;
    }

    #supportButton {
        background-color: #17a2b8;
        color: white;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 15px;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    /* Hiệu ứng rung */
    @keyframes shake {
        0% { transform: translateX(0); }
        25% { transform: translateX(-6px); }
        50% { transform: translateX(6px); }
        75% { transform: translateX(-6px); }
        100% { transform: translateX(0); }
    }

    #supportButton.shake {
        animation: shake 0.5s ease-in-out infinite;
    }

    /* Nút hiển thị chữ "Hỗ trợ" */
    #supportButton i {
        margin-right: 8px;
    }

    /* Nút hỗ trợ hover */
    #supportButton:hover {
        background-color: #138496;
    }

    /* Cải thiện form hỗ trợ */
    #supportForm .form-group {
        margin-bottom: 15px;
    }

    #supportForm input,
    #supportForm textarea {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #ddd;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    /* Hiệu ứng khi input hoặc textarea focus */
    #supportForm input:focus,
    #supportForm textarea:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
    }

    /* Nút gửi form */
    #supportForm button {
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 0 auto !important;
        display: flex;
        width: 50%;
    justify-content: center;
    }

    /* Hiệu ứng hover cho nút gửi */
    #supportForm button:hover {
        background-color: #218838;
    }

    /* Nút Hotline và Zalo */
    .contact-btns {
        margin-top: 20px;
        display: flex;
    }

    .contact-btns .btn {
        margin-bottom: 10px;
        width: 80%;
    }

    .contact-btns .btn i {
        margin-right: 10px;
    }
</style>

<!-- Modal Hỗ trợ -->
<div class="modal" id="supportModal" tabindex="-1" role="dialog" aria-labelledby="supportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supportModalLabel">Hỗ trợ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="supportForm">
                    <div class="form-group">
                        <label for="userName">Họ và tên</label>
                        <input type="text" class="form-control" id="userName" required>
                    </div>
                    <div class="form-group">
                        <label for="userMessage">Thông tin hỗ trợ</label>
                        <textarea class="form-control" id="userMessage" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Gửi</button>
                </form>

                <!-- Nút Hotline và Zalo -->
                <div class="contact-btns">
                    <a href="tel:0914476792" class="btn btn-danger">
                        Hotline: 0914476792
                    </a>
                    <a href="https://zalo.me/0914476792" target="_blank" class="btn btn-success">
                        Zalo: 0914476792
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Khi bấm vào nút hỗ trợ
    document.getElementById('supportButton').addEventListener('click', function() {
        $('#supportModal').modal('show'); // Hiển thị modal hỗ trợ
    });

    // Khi gửi form hỗ trợ
    document.getElementById('supportForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const userName = document.getElementById('userName').value;
        const userMessage = document.getElementById('userMessage').value;

        // Gửi yêu cầu hỗ trợ qua Telegram bot
        const telegramBotToken = '6608663537:AAExeC77L9XmTSK3lpW0Q3zt_kGfC1qKZfA';
        const telegramChatId = '5901907211';
        const message = `Họ và tên: ${userName}\nThông tin yêu cầu hỗ trợ: ${userMessage}`;

        fetch(`https://api.telegram.org/bot${telegramBotToken}/sendMessage`, {
            method: 'POST',
            body: new URLSearchParams({
                chat_id: telegramChatId,
                text: message
            }),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('Yêu cầu hỗ trợ đã được gửi.');
            $('#supportModal').modal('hide'); // Ẩn modal sau khi gửi
        })
        .catch(error => {
            console.error('Có lỗi xảy ra:', error);
            alert('Có lỗi xảy ra khi gửi yêu cầu hỗ trợ.');
        });
    });

    // Lắng nghe sự kiện cuộn trang
    window.addEventListener('scroll', function() {
        const scrollTopButton = document.getElementById('scrollTopButton');
        
        // Kiểm tra vị trí cuộn trang
        if (window.scrollY > 100) {
            // Nếu người dùng cuộn xuống, hiện nút "Top"
            scrollTopButton.style.display = 'block';
        } else {
            // Nếu người dùng cuộn lên đầu trang, ẩn nút "Top"
            scrollTopButton.style.display = 'none';
        }
    });

    // Khi bấm vào nút "Top", cuộn lên đầu trang
    document.getElementById('scrollTopButton').addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>
