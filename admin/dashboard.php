<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){ header("Location: ../login.php"); exit; }

require_once __DIR__ . '/../config/database.php';
$conn = isset($pdo) ? $pdo : $conn;

// Status Update
if(isset($_POST['update_status'])){
    $conn->prepare("UPDATE ccms_complaints SET status=? WHERE id=?")->execute([$_POST['status'], $_POST['id']]);
    header("Location: dashboard.php?updated=1"); exit;
}

// Stats
$total = $conn->query("SELECT COUNT(*) FROM ccms_complaints")->fetchColumn();
$pending = $conn->query("SELECT COUNT(*) FROM ccms_complaints WHERE status='Pending'")->fetchColumn();
$progress = $conn->query("SELECT COUNT(*) FROM ccms_complaints WHERE status='In Progress'")->fetchColumn();
$resolved = $conn->query("SELECT COUNT(*) FROM ccms_complaints WHERE status='Resolved'")->fetchColumn();

$rows = $conn->query("SELECT c.*, u.name, u.email FROM ccms_complaints c JOIN ccms_users u ON c.user_id=u.id ORDER BY c.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html><head><title>Admin - CCMS</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:sans-serif}
body{background:#f5f7fb;padding:20px}
.header{background:#000;color:#fff;padding:20px;border-radius:15px;display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:15px;margin-bottom:20px}
.stat{background:#fff;padding:20px;border-radius:15px;box-shadow:0 5px 15px rgba(0,0,0,0.05);border-left:5px solid #000}
.stat h3{font-size:28px}.stat.pending{border-color:orange}.stat.progress{border-color:blue}.stat.resolved{border-color:green}
.table-box{background:#fff;padding:20px;border-radius:15px;box-shadow:0 5px 15px rgba(0,0,0,0.05)}
table{width:100%;border-collapse:collapse;margin-top:10px}
th{background:#000;color:#fff;padding:12px;text-align:left}
td{padding:12px;border-bottom:1px solid #eee}
.badge{padding:5px 10px;border-radius:20px;font-size:11px;color:#fff}
.badge.Pending{background:orange}.badge.Resolved{background:green}.badge.In.Progress{background:blue}
select,button{padding:6px 10px;border-radius:6px;border:1px solid #ccc}
button{background:#000;color:#fff;cursor:pointer}
</style></head>
<body>

<div class="header">
<div><h2>🛡️ CCMS Admin Panel</h2><p>Welcome, <?php echo $_SESSION['name']; ?></p></div>
<div><a href="../logout.php" style="color:#fff;text-decoration:none;background:#333;padding:10px 15px;border-radius:8px;">Logout</a></div>
</div>

<div class="stats">
<div class="stat"><p>Total</p><h3><?php echo $total; ?></h3></div>
<div class="stat pending"><p>Pending</p><h3><?php echo $pending; ?></h3></div>
<div class="stat progress"><p>In Progress</p><h3><?php echo $progress; ?></h3></div>
<div class="stat resolved"><p>Resolved</p><h3><?php echo $resolved; ?></h3></div>
</div>

<div class="table-box">
<h3>All Complaints - Student Submit Panna Ella Complaint Um Inga Varum ✅</h3>
<?php if(isset($_GET['updated'])) echo "<p style='background:#d4edda;padding:10px;margin:10px 0;border-radius:8px;'>✅ Status Updated!</p>"; ?>
<table>
<tr><th>ID</th><th>Student</th><th>Subject</th><th>Description</th><th>Status</th><th>Action</th></tr>
<?php foreach($rows as $r): ?>
<tr>
<td>#<?php echo $r['id']; ?></td>
<td><b><?php echo $r['name']; ?></b><br><small><?php echo $r['email']; ?></small></td>
<td><?php echo $r['subject']; ?><br><small style="color:#666"><?php echo $r['category']; ?></small></td>
<td style="max-width:200px;"><?php echo $r['description']; ?></td>
<td><span class="badge <?php echo str_replace(' ','.', $r['status']); ?>"><?php echo $r['status']; ?></span></td>
<td>
<form method="POST" style="display:flex;gap:5px;">
<input type="hidden" name="id" value="<?php echo $r['id']; ?>">
<select name="status"><option <?php if($r['status']=='Pending') echo 'selected'; ?>>Pending</option><option <?php if($r['status']=='In Progress') echo 'selected'; ?>>In Progress</option><option <?php if($r['status']=='Resolved') echo 'selected'; ?>>Resolved</option></select>
<button name="update_status">Update</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php if(empty($rows)) echo "<p style='text-align:center;padding:30px;color:#999'>Innum complaint varala da... Student login panni complaint anupa sollu!</p>"; ?>
</div>

</body></html>