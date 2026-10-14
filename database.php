<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// database.php ah direct ah include pannama, ingaye connect pannrom
// Un database.php la enna host/db/user/pass irukko atha inga potrukken - thappu na mathu
$host = 'localhost';
$db   = 'ccms';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Throwable $e) {
    // config file la iruntha try pannu
    if (file_exists(__DIR__ . '/config/database.php')) {
        require_once __DIR__ . '/config/database.php';
    } elseif (file_exists(__DIR__ . '/../config/database.php')) {
        require_once __DIR__ . '/../config/database.php';
    } else {
        die("DB Connect Error: " . $e->getMessage() . "<br>database.php code ah anuppu");
    }
}

if (!isset($pdo) && isset($conn) && $conn instanceof PDO) { $pdo = $conn; }
if (!isset($pdo)) { die("DB Error: PDO object create aagala. database.php la \$pdo irukka nu check pannu"); }

$user_id = (int)($_SESSION['user_id'] ?? 0);
$user_name = (string)($_SESSION['user_name'] ?? $_SESSION['name'] ?? 'User');
$user_role = strtolower(trim((string)($_SESSION['role'] ?? 'student')));

$total = $pending = $inprogress = $resolved = 0;
$all = [];

try {
    if ($user_role === 'admin') {
        $total = (int)$pdo->query("SELECT COUNT(*) FROM ccms_complaints")->fetchColumn();
        $pending = (int)$pdo->query("SELECT COUNT(*) FROM ccms_complaints WHERE status='Pending'")->fetchColumn();
        $inprogress = (int)$pdo->query("SELECT COUNT(*) FROM ccms_complaints WHERE status='In Progress'")->fetchColumn();
        $resolved = (int)$pdo->query("SELECT COUNT(*) FROM ccms_complaints WHERE status='Resolved'")->fetchColumn();
        $all = $pdo->query("SELECT c.*, u.name as student_name, u.email FROM ccms_complaints c LEFT JOIN ccms_users u ON c.user_id = u.id ORDER BY c.id DESC")->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $s = $pdo->prepare("SELECT COUNT(*) FROM ccms_complaints WHERE user_id=?"); $s->execute([$user_id]); $total=(int)$s->fetchColumn();
        $s = $pdo->prepare("SELECT COUNT(*) FROM ccms_complaints WHERE user_id=? AND status='Pending'"); $s->execute([$user_id]); $pending=(int)$s->fetchColumn();
        $s = $pdo->prepare("SELECT COUNT(*) FROM ccms_complaints WHERE user_id=? AND status='In Progress'"); $s->execute([$user_id]); $inprogress=(int)$s->fetchColumn();
        $s = $pdo->prepare("SELECT COUNT(*) FROM ccms_complaints WHERE user_id=? AND status='Resolved'"); $s->execute([$user_id]); $resolved=(int)$s->fetchColumn();
    }
} catch (Throwable $e) {
    die("DB Query Error: " . $e->getMessage());
}
?>
<html><head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>.desc-row{display:none; background:#f8f9fa;} .desc-box{padding:15px;}</style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark"><div class="container"><span class="navbar-brand">CCMS - <?= strtoupper(htmlspecialchars($user_role)) ?> - <?= htmlspecialchars($user_name) ?></span><a href="logout.php" class="btn btn-sm btn-light">Logout</a></div></nav>
<div class="container mt-4">
<div class="row g-2"><div class="col-3"><div class="card p-2 text-center"><h4><?= $total ?></h4>Total</div></div><div class="col-3"><div class="card p-2 text-center"><h4><?= $pending ?></h4>Pending</div></div><div class="col-3"><div class="card p-2 text-center"><h4><?= $inprogress ?></h4>In Progress</div></div><div class="col-3"><div class="card p-2 text-center"><h4><?= $resolved ?></h4>Resolved</div></div></div>
<?php if ($user_role === 'admin') { ?>
<h4 class="mt-4">All Complaints - Admin Only</h4>
<table class="table table-bordered mt-3"><tr><th>ID</th><th>Student</th><th>Subject</th><th>Status</th><th>View</th></tr>
<?php foreach ($all as $r) { ?>
<tr><td><?= (int)$r['id'] ?></td><td><?= htmlspecialchars((string)($r['student_name'] ?? 'Unknown')) ?></td><td><?= htmlspecialchars((string)($r['subject'] ?? '')) ?></td><td><span class="badge bg-warning text-dark"><?= htmlspecialchars((string)($r['status'] ?? 'Pending')) ?></span></td><td><button onclick="toggleDesc(<?= (int)$r['id'] ?>)" class="btn btn-sm btn-primary">View</button></td></tr>
<tr id="d-<?= (int)$r['id'] ?>" class="desc-row"><td colspan="5"><div class="desc-box"><b>ID #<?= (int)$r['id'] ?> - <?= htmlspecialchars((string)($r['subject'] ?? '')) ?></b><br><b>Student:</b> <?= htmlspecialchars((string)($r['student_name'] ?? 'Unknown')) ?> (<?= htmlspecialchars((string)($r['email'] ?? '')) ?>)<br><b>Category:</b> <?= htmlspecialchars((string)($r['category'] ?? 'N/A')) ?> | <b>Priority:</b> <?= htmlspecialchars((string)($r['priority'] ?? 'N/A')) ?><br><br><b>Full Description:</b><br><?= nl2br(htmlspecialchars((string)($r['description'] ?? ''))) ?><br><br><button onclick="toggleDesc(<?= (int)$r['id'] ?>)" class="btn btn-sm btn-secondary">Close</button></div></td></tr>
<?php } ?></table>
<?php if (count($all) === 0) echo "<p>Complaints innum illa.</p>"; ?>
<?php } else { ?>
<h4 class="mt-4">Submit Complaint - Student Only</h4>
<form method="POST" action="submit_complaint.php" class="card p-3">
  <div class="row"><div class="col-6 mb-2"><label>Category</label><select name="category" class="form-control" required><option value="">Select</option><option>Hostel</option><option>Food</option><option>Transport</option><option>Academics</option><option>Fees</option><option>Others</option></select></div><div class="col-6 mb-2"><label>Priority</label><select name="priority" class="form-control"><option>Medium</option><option>High</option><option>Low</option></select></div></div>
  <label>Subject</label><input name="subject" class="form-control mb-2" required>
  <label>Description</label><textarea name="description" class="form-control mb-2" rows="4" required></textarea>
  <button class="btn btn-primary w-100">Submit</button>
</form>
<h5 class="mt-4">My Complaints</h5>
<table class="table table-bordered"><tr><th>ID</th><th>Subject</th><th>Status</th></tr>
<?php $my = $pdo->prepare("SELECT * FROM ccms_complaints WHERE user_id=? ORDER BY id DESC"); $my->execute([$user_id]); foreach ($my->fetchAll(PDO::FETCH_ASSOC) as $m) { ?>
<tr><td><?= (int)$m['id'] ?></td><td><?= htmlspecialchars((string)($m['subject'] ?? '')) ?></td><td><?= htmlspecialchars((string)($m['status'] ?? 'Pending')) ?></td></tr><?php } ?></table>
<?php } ?>
</div>
<script>function toggleDesc(id){ var el = document.getElementById('d-'+id); el.style.display = el.style.display === 'table-row' ? 'none' : 'table-row'; }</script>
</body></html>