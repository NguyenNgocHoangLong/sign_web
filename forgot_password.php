<?php
require_once("entities/khach.class.php");
include_once("header.php");

$message = "";

// Lấy tên người dùng từ URL
$username = $_GET['username'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($new_password) || empty($confirm_password)) {
        $message = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match.";
    } else {
        try {
            $db = new Db();
            $conn = $db->connect();

            // Kiểm tra xem người dùng có tồn tại không
            $stmt = $conn->prepare("SELECT * FROM khach WHERE khach_name = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                // Cập nhật mật khẩu
                $stmt = $conn->prepare("UPDATE khach SET password = ? WHERE khach_name = ?");
                $stmt->bind_param("ss", $new_password, $username);
                if ($stmt->execute()) {
                    $message = "Password successfully change.";
                } else {
                    $message = "Error resetting password.";
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

<h2>Forgot Password</h2>
<p style="color: red;"><?php echo $message; ?></p>
<form action="forgot_password.php" method="post">
    <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
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

<a href="login.php">Back to Login</a>

<?php include_once("footer.php"); ?>
