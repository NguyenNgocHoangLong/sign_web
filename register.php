<?php
require_once("entities/khach.class.php");

$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $khach_name = $_POST['khach_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $sign = $_POST['sign'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Xử lý file tải lên
    $upload_dir = "uploads/"; // Thư mục lưu file
    $sign_file = $_FILES['sign'] ?? null;

    if ($sign_file && $sign_file['error'] === UPLOAD_ERR_OK) {
        // Tạo tên file duy nhất để tránh trùng lặp
        $file_extension = pathinfo($sign_file['name'], PATHINFO_EXTENSION);
        $file_name = uniqid("sign_", true) . "." . $file_extension;
        $file_path = $upload_dir . $file_name;

        // Di chuyển file vào thư mục
        if (!move_uploaded_file($sign_file['tmp_name'], $file_path)) {
            $message = "Failed to upload signature.";
            $file_path = null;
        }
    } else {
        $message = "Please upload a valid signature image.";
        $file_path = null;
    }

    // Kiểm tra dữ liệu đầu vào
    if (empty($khach_name) || empty($email) || empty($sign) || empty($password)) {
        $message = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        try {
            // Kết nối đến cơ sở dữ liệu
            $db = new Db();
            $conn = $db->connect();

            // Kiểm tra xem username hoặc email đã tồn tại chưa
            $stmt = $conn->prepare("SELECT * FROM khach WHERE khach_name = ? OR email = ?");
            $stmt->bind_param("ss", $khach_name, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = "Username or email already exists.";
            } else {
                // Mã hóa mật khẩu
                //$hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Thêm người dùng vào cơ sở dữ liệu
                $stmt = $conn->prepare("INSERT INTO khach (khach_name, email, sign, password) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $khach_name, $ $email, $sign, $password /*$hashed_password*/);

                if ($stmt->execute()) {
                    $message = "Registration successful. <a href='login.php'>Login here</a>.";
                } else {
                    $message = "Error in registration.";
                }
            }

            $stmt->close();
            $conn->close();
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}
?>
<?php include_once("header.php"); ?>
<h2>Register</h2>
<p style="color: red;"><?php echo $message; ?></p>
<form action="register.php" method="post">
    <div>
        <label for="khach_name">Username:</label>
        <input type="text" name="khach_name" id="khach_name" required>
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
    </div>
    <div>
        <label for="sign">Signature (Upload Image):</label>
        <input type="file" name="sign" id="sign" accept="image/*" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
    </div>
    <div>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>
    </div>
    <div>
        <button type="submit">Register</button>
    </div>
</form>
<?php include_once("footer.php"); ?>
