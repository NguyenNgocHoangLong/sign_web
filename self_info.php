<?php
require_once("entities/khach.class.php");
include_once("header.php");

// Start session and check if the user is logged in
session_start();
if (!isset($_SESSION['KhachID'])) {
    header("Location: login.php");
    exit;
}

// Fetch the user's information from the database
try {
    $db = new Db();
    $conn = $db->connect();

    $stmt = $conn->prepare("SELECT KhachID, Khach_Name, SDT, Email, Sign FROM khach WHERE KhachID = ?");
    $stmt->bind_param("i", $_SESSION['KhachID']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $userInfo = $result->fetch_assoc();
    } else {
        echo "<p>Unable to retrieve user information.</p>";
        exit;
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
    exit;
}
?>

<h2>Your Information</h2>
<table border="1" style="border-collapse: collapse; width: 50%;">
    <tr>
        <th>Field</th>
        <th>Value</th>
    </tr>
    <tr>
        <td>User ID</td>
        <td><?php echo htmlspecialchars($userInfo['KhachID']); ?></td>
    </tr>
    <tr>
        <td>Username</td>
        <td><?php echo htmlspecialchars($userInfo['Khach_Name']); ?></td>
    </tr>
    <tr>
        <td>Phone Number</td>
        <td><?php echo htmlspecialchars($userInfo['SDT']); ?></td>
    </tr>
    <tr>
        <td>Email</td>
        <td><?php echo htmlspecialchars($userInfo['Email']); ?></td>
    </tr>
    <tr>
        <td>Signature</td>
        <td>
            <?php
            if (!empty($userInfo['Sign']) && file_exists("C:\Users\ACER\Downloads" . $userInfo['Sign'])) {
                echo '<img src="C:\Users\ACER\Downloads' . htmlspecialchars($userInfo['Sign']) . '" alt="Signature" style="width: 150px; height: auto;">';
            } else {
                echo "No signature uploaded.";
            }
            ?>
        </td>
    </tr>
</table>

<a href="home.php">Back to Home</a>

<?php include_once("footer.php"); ?>
