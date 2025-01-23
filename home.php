<?php
session_start();
if (!isset($_SESSION['KhachID'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "ql_chu_ky");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$khach_id = $_SESSION['KhachID'];
$sql = "SELECT Sign FROM Khach WHERE KhachID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $khach_id);
$stmt->execute();
$result = $stmt->get_result();
$signature_path = "";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $signature_path = $row['Sign']; // Đường dẫn ảnh chữ ký
}

$stmt->close();
$conn->close();

$message = "";
$file_url = "";
$image_url = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Xử lý file văn bản
    if (isset($_FILES['uploaded_file'])) {
        $file = $_FILES['uploaded_file'];
        $allowed_extensions = ['pdf', 'doc', 'docx'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            $message = "Chỉ chấp nhận các file, JPG, JPEG, PNG, GIF, PDF, DOC, hoặc DOCX.";
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

    // Xử lý ảnh
    if (isset($_FILES['uploaded_image'])) {
        $image = $_FILES['uploaded_image'];
        $allowed_image_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $image_extension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

        if (!in_array($image_extension, $allowed_image_extensions)) {
            $message = "Chỉ chấp nhận các file, JPG, JPEG, PNG, GIF, PDF, DOC, hoặc DOCX.";
        } elseif ($image['error'] != 0) {
            $message = "Lỗi khi tải ảnh. Vui lòng thử lại.";
        } else {
            $image_dir = "uploads/images/";
            if (!is_dir($image_dir)) {
                mkdir($image_dir, 0777, true);
            }
            $image_path = $image_dir . basename($image['name']);

            if (move_uploaded_file($image['tmp_name'], $image_path)) {
                $image_url = $image_path;
            } else {
                $message = "Không thể lưu ảnh.";
            }
        }
    }
}

// Lấy danh sách ảnh từ thư mục
$message = "";
$image_url = "";
$image_dir = "images/";
$images = [];
if (is_dir($image_dir)) {
    $images = array_diff(scandir($image_dir), ['.', '..']);
}
?>
<?php include_once("header.php"); ?>
    <h2>Welcome to Home</h2>
    <p style="color: red;"><?php echo $message; ?></p>

    <!-- Form tải file -->
    <form action="home.php" method="post" enctype="multipart/form-data">
        <div>
            <label for="uploaded_file">Chọn file văn bản:</label>
            <input type="file" name="uploaded_file" id="uploaded_file" accept=".pdf, .doc, .docx">
        </div>
        <div>
            <label for="uploaded_image">Chọn ảnh:</label>
            <input type="file" name="uploaded_image" id="uploaded_image" accept=".jpg, .jpeg, .png, .gif">
        </div>
        <div>
            <button type="submit">Tải lên</button>
        </div>
    </form>
    <div>
        <p>Chỉ chấp nhận file PDF, DOC, DOCX và ảnh JPG, JPEG, PNG, GIF.</p>
    </div>

    <h2>Chèn chữ ký lên ảnh</h2>
<p>Nhấp vào bất kỳ vị trí nào trên ảnh để chèn chữ ký của bạn.</p>

<div style="position: relative; display: inline-block;">
    <!-- Ảnh nền -->
    <img id="main-image" src="uploads/images/test.jpg" alt="Ảnh nền" style="max-width: 100%; cursor: crosshair;">
    <!-- Canvas để vẽ chữ ký -->
    <canvas id="signature-canvas" style="position: absolute; top: 0; left: 0;"></canvas>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const mainImage = document.getElementById("main-image");
    const canvas = document.getElementById("signature-canvas");
    const ctx = canvas.getContext("2d");

    // Đặt kích thước canvas bằng kích thước ảnh
    canvas.width = mainImage.offsetWidth;
    canvas.height = mainImage.offsetHeight;

    // Đường dẫn ảnh chữ ký
    const signaturePath = "<?php echo $signature_path; ?>";

    // Tải ảnh chữ ký
    const signatureImage = new Image();
    signatureImage.src = signaturePath;

    // Lắng nghe sự kiện nhấp chuột trên ảnh
    mainImage.addEventListener("click", function (event) {
        const rect = mainImage.getBoundingClientRect();

        // Lấy tọa độ nhấp chuột
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;

        // Vẽ ảnh chữ ký lên canvas
        signatureImage.onload = function () {
            const sigWidth = 100; // Chiều rộng chữ ký
            const sigHeight = 50; // Chiều cao chữ ký
            ctx.drawImage(signatureImage, x - sigWidth / 2, y - sigHeight / 2, sigWidth, sigHeight);
        };
    });
});
</script>

    <!-- Hiển thị file văn bản -->
    <?php if ($file_url): ?>
        <div style="margin-top: 20px;">
            <h3>File văn bản của bạn:</h3>
            <?php if (pathinfo($file_url, PATHINFO_EXTENSION) === 'pdf'): ?>
                <iframe src="<?php echo $file_url; ?>" width="100%" height="500px" style="border: none;"></iframe>
            <?php else: ?>
                <iframe src="https://docs.google.com/gview?url=<?php echo urlencode('http://localhost/' . $file_url); ?>&embedded=true" 
                        width="100%" height="500px" style="border: none;"></iframe>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Hiển thị danh sách ảnh trong bảng -->
    <table border="1" cellpadding="0" cellspacing="0" style="position: auto; background-color: rgba(255, 255, 255, 0); border: 1px solid rgba(200, 200, 200, 0.5); margin-bottom: 120px; width: 100%; text-align: center;">
    <tbody>
        <tr>
            <?php foreach ($images as $img): ?>
                <td>
                    <img src="<?php echo $image_dir . $img; ?>" alt="Uploaded Image" style="max-width: 150px; max-height: 75px;">
                </td>
            <?php endforeach; ?>
        </tr>
    </tbody>
    </table>


    <!-- Hiển thị ảnh -->
    <!-- <div style="margin-top: 20px;">
        <h3>Ảnh đã tải lên:</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            <?php foreach ($images as $img): ?>
                <div style="border: 1px solid #ccc; padding: 10px;">
                    <img src="<?php echo $image_dir . $img; ?>" alt="Uploaded Image" style="width: 100%; height: 500px;">
                </div>
            <?php endforeach; ?>
        </div>
    </div> -->
<?php include_once("footer.php"); ?>
