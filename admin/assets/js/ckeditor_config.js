document.addEventListener('DOMContentLoaded', function() {
    ClassicEditor
    .create(document.querySelector('#noidung'), {
        language: 'vi',
        toolbar: [
            'heading', '|',
            'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|',
            'insertTable', 'imageUpload', 'mediaEmbed', '|',
            'imageStyle:full', 'imageStyle:side', 'imageResize', '|', // 👈 thêm imageResize
            'undo', 'redo'
        ],
        image: {
            resizeUnit: 'px', // hoặc '%' nếu bạn thích tỉ lệ %
            toolbar: [
                'imageStyle:full',
                'imageStyle:side',
                'imageTextAlternative',
                'imageResize' // 👈 thêm công cụ resize
            ],
            // Thêm cấu hình hiển thị thanh điều chỉnh kích thước khi chọn ảnh
            resizeOptions: [
                { name: 'imageResize:original', value: null, icon: 'original' },
                { name: 'imageResize:50', value: '50', icon: 'resize-50' },
                { name: 'imageResize:75', value: '75', icon: 'resize-75' },
                { name: 'imageResize:100', value: '100', icon: 'resize-100' }
            ]
        },
        ckfinder: {
            uploadUrl: '/2/admin/pages/products/upload_ckeditor.php'
        },
        mediaEmbed: {
            previewsInData: true
        }
    })
    .then(editor => {
        console.log('CKEditor with image resize initialized');
    })
    .catch(error => {
        console.error(error);
    });

    ClassicEditor
        .create(document.querySelector('#description'), {
            language: 'vi',
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|',
                'insertTable', 'imageUpload', 'mediaEmbed', '|',
                'imageStyle:full', 'imageStyle:side', 'imageResize', '|', // 👈 thêm imageResize
                'undo', 'redo'
            ],
            image: {
                resizeUnit: 'px', // hoặc '%' nếu bạn thích tỉ lệ %
                toolbar: [
                    'imageStyle:full',
                    'imageStyle:side',
                    'imageTextAlternative',
                    'imageResize' // 👈 thêm công cụ resize
                ],
                resizeOptions: [
                    { name: 'imageResize:original', value: null, icon: 'original' },
                    { name: 'imageResize:50', value: '50', icon: 'resize-50' },
                    { name: 'imageResize:75', value: '75', icon: 'resize-75' },
                    { name: 'imageResize:100', value: '100', icon: 'resize-100' }
                ]
            },
            ckfinder: {
                uploadUrl: '/2/admin/pages/products/upload_ckeditor.php'
            },
            mediaEmbed: {
                previewsInData: true
            }
        })
        .then(editor => {
            console.log('CKEditor with image resize initialized');
        })
        .catch(error => {
            console.error(error);
        });
});
