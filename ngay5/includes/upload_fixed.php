<?php
function upload_file($file) {
    $allowed_extensions = ['jpg', 'png', 'pdf'];
    $max_size = 2 * 1024 * 1024;  // 2MB
    $upload_dir = "uploads/";

    // Ensure uploads directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $file_name = time() . "_" . basename($file['name']);
    $file_path = $upload_dir . $file_name;
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Kiểm tra lỗi upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "Lỗi khi upload file: " . $file['error'];
    }

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
        return "Upload thành công: " . htmlspecialchars($file_name);
    }

    return "Lỗi khi upload file.";
}
?>
