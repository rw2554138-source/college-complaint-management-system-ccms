<?php
session_start();
require_once '../config/database.php';
if(!isset($_SESSION['user_id'])){
 header("Location: ../login.php");
 exit();
}
$list = $pdo->query("SELECT * FROM ccms_complaints ORDER BY id DESC");
$rows = $list->fetchAll();
?>
<!DOCTYPE html>
<html><head><title>All Complaints</title></head>
<body>
<h2>All Complaints (<?php echo count($rows); ?>)</h2>
<a href="dashboard.php">Back to Dashboard</a><hr>
<table border="1" cellpadding="10" cellspacing="0">
<tr><th>ID</th><th>Subject</th><th>Status</th><th>Action</th></tr>
<?php foreach($rows as $r): ?>
<tr>
<td>CMP<?php echo $r['id']; ?></td>
<td><?php echo htmlspecialchars($r['subject']); ?></td>
<td><?php echo $r['status']; ?></td>
<td><a href="view_complaint.php?id=<?php echo $r['id']; ?>">View</a></td>
</tr>
<?php endforeach; ?>
</table>
</body></html>