<?php
require_once("entities/khach.class.php");
session_start();
$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['login'])) {
    $khach_name = $_POST['Khach_name'] ?? '';
    $password = $_POST['Password'] ?? '';

    // Kiểm tra xem đã nhập đầy đủ thông tin chưa
    if (empty($khach_name) || empty($password)) {
        $message = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $db = new Db();
        $conn = $db->connect();
        $stmt = $conn->prepare("SELECT * FROM Khach WHERE Khach_name = ?");
        $stmt->bind_param("s", $khach_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Kiểm tra mật khẩu
            if ($password === $row['Password']) { 
                $_SESSION['KhachID'] = $row['KhachID'];
                $_SESSION['Khach_Name'] = $row['Khach_Name'];
                $_SESSION['Password'] = $row['Password'];
                header("Location: home.php");
                exit;
            } else {
                $message = "Sai username hoặc password.";
            }
        } else {
            $message = "Sai username hoặc password.";
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<?php include_once("header.php"); ?>
    <h2>Login</h2>
    <p style="color: red;"><?php echo $message; ?></p>
    <form action="login.php" method="post">
        <div>
            <label for="Khach_name">Username:</label>
            <input type="text" name="Khach_name" id="Khach_name" required>
        </div>
        <div>
            <label for="Password">Password:</label>
            <input type="Password" name="Password" id="Password" required>
        </div>
        <div>
            <button type="submit" name="login">Login</button>
        </div>
    </form>
    <div>
        <button id="forgot-password" onclick="redirectToChangePassword()">Forgot Password?</button>
    </div>

    <script>
        function redirectToChangePassword() {
            const username = document.getElementById('Khach_name').value;
            if (username) {
                const url = `forgot_password.php?username=${encodeURIComponent(username)}`;
                window.location.href = url;
            } else {
                alert("Please enter your username to reset your password.");
            }
        }
    </script>  
<?php include_once("footer.php"); ?>
