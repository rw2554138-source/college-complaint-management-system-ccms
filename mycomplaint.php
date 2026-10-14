<?php
session_start();
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
require_once __DIR__ . '/config/database.php';
$conn = isset($pdo) ? $pdo : $conn;

$stmt = $conn->prepare("SELECT * FROM ccms_complaints WHERE user_id=? ORDER BY id DESC");
$stmt->execute([$_SESSION['user_id']]);
$rows = $stmt->fetchAll();
?>
<html><body style="font-family:sans-serif; padding:30px;">
<h2>My Complaints</h2>
<?php if(isset($_GET['success'])) echo "<p style='background:#d4edda; padding:10px;'>✅ Complaint Save Aayiduchu da!</p>"; ?>
<table border="1" cellpadding="10" style="border-collapse:collapse; width:100%;">
<tr style="background:#000; color:#fff;"><th>ID</th><th>Subject</th><th>Category</th><th>Status</th><th>Date</th></tr>
<?php foreach($rows as $r){ echo "<tr><td>{$r['id']}</td><td>{$r['subject']}</td><td>{$r['category']}</td><td>{$r['status']}</td><td>{$r['created_at']}</td></tr>"; } ?>
</table>
<br><a href="dashboard.php">+ New Complaint</a>
</body></html>