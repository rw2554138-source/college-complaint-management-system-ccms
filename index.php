<?php
session_start();
require_once __DIR__. '/config/database.php';
if (!isset($pdo) && isset($conn)) $pdo = $conn;
$msg="";
if($_SERVER['REQUEST_METHOD']=='POST'){
 $e=$_POST['email']; $p=$_POST['password'];
 $s=$pdo->prepare("SELECT * FROM ccms_users WHERE email=?"); $s->execute([$e]); $u=$s->fetch();
 if($u && ($p==$u['password'] || password_verify($p,$u['password']))){
  $_SESSION['user_id']=$u['id']; $_SESSION['user_name']=$u['name']; $_SESSION['user_email']=$u['email']; $_SESSION['role']=$u['role']??'student';
  header("Location: dashboard.php"); exit();
 } else $msg="Wrong email or password!";
}
?>
<html><head><meta name="viewport" content="width=device-width,initial-scale=1"><style>
*{margin:0;padding:0;box-sizing:border-box} body{height:100vh;background:#6c5ce7;display:flex;align-items:center;justify-content:center;font-family:Arial}
.box{width:900px;background:white;display:flex;border-radius:20px;overflow:hidden;box-shadow:0 10px 40px rgba(0,0,0,.3)}
.l{width:50%;background:linear-gradient(135deg,#6c5ce7,#a29bfe);color:white;padding:40px}
.r{width:50%;padding:40px} input{width:100%;padding:12px;margin:8px 0 15px;border:1px solid #ddd;border-radius:8px} button{width:100%;padding:12px;background:#6c5ce7;color:white;border:0;border-radius:8px;font-weight:bold;cursor:pointer}
</style></head><body>
<div class="box">
<div class="l"><h1 style="font-size:60px">🔒</h1><h2>CCMS</h2><p style="margin-top:20px;line-height:1.6">College Complaint System<br><br>✓ Secure Login<br>✓ Track Complaints<br>✓ Admin Reply in 24hrs</p></div>
<div class="r"><h2>Welcome Back! 👋</h2><p style="color:gray;font-size:13px;margin-bottom:20px">Login to continue</p>
<?php if($msg) echo "<div style='background:#ffdddd;padding:10px;border-radius:8px;margin-bottom:15px;color:red;font-size:13px'>$msg</div>";?>
<form method="POST"><label style="font-size:13px;font-weight:bold">Email</label><input type="email" name="email" placeholder="Enter your email" required><label style="font-size:13px;font-weight:bold">Password</label><input type="password" name="password" placeholder="Enter password" required><button>LOGIN →</button></form>
<p style="text-align:center;margin-top:20px;font-size:13px">Demo: student@gmail.com / 123456</p></div>
</div></body></html>