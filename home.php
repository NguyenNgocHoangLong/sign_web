<?php
session_start();
if (!isset($_SESSION['KhachID'])) {
    header("Location: login.php");
    exit;
}

$message = "";
$file_url = "";
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES['uploaded_file'])) {
    $file = $_FILES['uploaded_file'];
    $allowed_extensions = ['pdf', 'doc', 'docx'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Kiểm tra file hợp lệ
    if (!in_array($file_extension, $allowed_extensions)) {
        $message = "Chỉ chấp nhận các file PDF, DOC, hoặc DOCX.";
    } elseif ($file['error'] != 0) {
        $message = "Lỗi khi tải file. Vui lòng thử lại.";
    } else {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_path = $upload_dir . basename($file['name']);

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $file_url = $file_path;
        } else {
            $message = "Không thể lưu file.";
        }
    }
}
?>
<?php include_once("header.php"); ?>
    <h2>Welcome to Home</h2>
    <p style="color: red;"><?php echo $message; ?></p>
    <form action="home.php" method="post" enctype="multipart/form-data">
        <div>
            <label for="uploaded_file">Chọn file:</label>
            <input type="file" name="uploaded_file" id="uploaded_file" accept=".pdf, .doc, .docx" required>
        </div>
        <div>
            <button type="submit">Tải lên và mở file</button>
        </div>
    </form>
    <div>
        <p>Chỉ chấp nhận file có định dạng PDF, DOC, hoặc DOCX.</p>
    </div>
    <?php if ($file_url): ?>
        <div style="margin-top: 20px;">
            <h3>File của bạn:</h3>
            <?php if (pathinfo($file_url, PATHINFO_EXTENSION) === 'pdf'): ?>
                <!-- Hiển thị PDF trực tiếp -->
                <iframe src="<?php echo $file_url; ?>" width="100%" height="500px" style="border: none;"></iframe>
            <?php else: ?>
                <!-- Hiển thị DOC hoặc DOCX bằng Google Docs Viewer -->
                <iframe src="https://docs.google.com/gview?url=<?php echo urlencode('http://localhost:8080/Signature%20Website/home.php' . $file_url); ?>&embedded=true" 
                        width="100%" height="500px" style="border: none;"></iframe>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php include_once("footer.php"); ?>
