login.php
<?php
require_once("entities/khach.class.php");
session_start();
$message = "";
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['login'])) {
    $khach_name = $_POST['Khach_name'] ?? '';
    $password = isset($_POST['Password']) ? $_POST['Password'] : '';
    if (!empty($khach_name) && !empty($password)) {
        $db = new Db();
        $conn = $db->connect();
        $stmt = $conn->prepare("SELECT * FROM Khach WHERE Khach_name = ?");
        $stmt->bind_param("s", $khach_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            //if (password_verify($password, $row['Password'])) {
                
            $_SESSION['KhachID'] = $row['KhachID'];
            $_SESSION['Khach_Name'] = $row['Khach_Name'];
            $_SESSION['Password']=$row['Password'];
            header("Location: home.php");
            exit;
            /*} else {
                $message = "Invalid username or password.";
            }*/
        } else {
            $message = "Invalid username or password.";
        }
        $stmt->close();
        $conn->close();
    } else {
        $message = "Both fields are required.";
    }
}
?>
<?php include_once("header.php");?>
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
<?php include_once("footer.php");?>
