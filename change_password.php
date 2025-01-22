<?php
require_once("entities/khach.class.php");
include_once("header.php");

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['KhachID'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// Xử lý thay đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match.";
    } else {
        try {
            $db = new Db();
            $conn = $db->connect();

            // Lấy mật khẩu hiện tại từ cơ sở dữ liệu
            $stmt = $conn->prepare("SELECT Password FROM khach WHERE KhachID = ?");
            $stmt->bind_param("i", $_SESSION['KhachID']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                // Kiểm tra mật khẩu hiện tại
                if ($current_password === $row['Password']) { // Chú ý: Thay password_verify nếu mật khẩu đã mã hóa
                    // Cập nhật mật khẩu mới
                    //$hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE khach SET Password = ? WHERE KhachID = ?");
                    $stmt->bind_param("si", $new_password, $_SESSION['KhachID']);
                    if ($stmt->execute()) {
                        $message = "Password changed successfully.";
                    } else {
                        $message = "Error updating password.";
                    }
                } else {
                    $message = "Current password is incorrect.";
                }
            } else {
                $message = "User not found.";
            }

            $stmt->close();
            $conn->close();
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}
?>

<h2>Change Password</h2>
<p style="color: red;"><?php echo $message; ?></p>
<form action="change_password.php" method="post">
    <div>
        <label for="current_password">Current Password:</label>
        <input type="password" name="current_password" id="current_password" required>
    </div>
    <div>
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password" required>
    </div>
    <div>
        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>
    </div>
    <div>
        <button type="submit">Change Password</button>
    </div>
</form>

<a href="login.php">Back to Home</a>

<?php include_once("footer.php"); ?>
