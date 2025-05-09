<!-- upload.php -->
<?php
function upload_file($file) {
    $allowed_extensions = ['jpg', 'png', 'pdf'];
    $max_size = 2 * 1024 * 1024;  // 2MB
    $upload_dir = "uploads/";

    $file_name = time() . "_" . basename($file['name']);
    $file_path = $upload_dir . $file_name;
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Kiểm tra kích thước file
    if ($file['size'] > $max_size) {
        return "File quá lớn. Tối đa là 2MB.";
    }

    // Kiểm tra định dạng file
    if (!in_array($file_extension, $allowed_extensions)) {
        return "Định dạng file không hợp lệ. Chỉ hỗ trợ jpg, png, pdf.";
    }

    // Di chuyển file vào thư mục uploads
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return $file_name;  // Trả về tên file đã upload
    }

    return "Lỗi khi upload file.";
}
?>
