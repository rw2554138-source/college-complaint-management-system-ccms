<?php
session_start();
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

require_once __DIR__ . '/config/database.php';
$conn = isset($pdo) ? $pdo : $conn;
$msg = "";

if($_SERVER['REQUEST_METHOD']=='POST'){
    $subject = $_POST['subject'];
    $desc = $_POST['description'];
    $cat = $_POST['category'] ?? 'General';
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("INSERT INTO ccms_complaints (user_id, subject, description, category, status) VALUES (?, ?, ?, ?, 'Pending')");
    if($stmt->execute([$user_id, $subject, $desc, $cat])){
        $msg = "Complaint Saved Successfully! ✅";
        // Login page ku pogathu, my_complaints ku pogum
        header("Location: my_complaints.php?success=1");
        exit;
    } else {
        $msg = "Save Failed!";
    }
}
?>
<!DOCTYPE html>
<html><body style="font-family:sans-serif; padding:30px; max-width:600px; margin:auto;">
<h2>Submit Complaint - <?php echo $_SESSION['name']; ?></h2>
<?php if($msg) echo "<p style='color:green'>$msg</p>"; ?>
<form method="POST">
<input name="subject" placeholder="Subject" style="width:100%; padding:12px; margin:10px 0;" required><br>
<select name="category" style="width:100%; padding:12px; margin:10px 0;">
<option>General</option><option>Hostel</option><option>Food</option><option>Lab</option><option>Fees</option>
</select><br>
<textarea name="description" placeholder="Full Complaint" style="width:100%; padding:12px; height:120px;" required></textarea><br>
<button style="padding:12px 30px; background:black; color:white; border:none; cursor:pointer;">SUBMIT</button>
</form>
<br><a href="my_complaints.php">→ My Complaints Paarkka</a> | <a href="logout.php">Logout</a>
</body></html>