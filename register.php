<?php
session_start();
error_reporting(0);
require_once __DIR__ . '/config/database.php';
if (!isset($pdo) && isset($conn)) $pdo = $conn;

$msg=""; $ok="";

if($_SERVER['REQUEST_METHOD']=='POST'){
 $name = trim($_POST['name']??'');
 $email = trim($_POST['email']??'');
 $pass = $_POST['password']??'';
 $dept = $_POST['department']??'';

 if(!$name || !$email || !$pass){
   $msg = "All fields required da!";
 } else {
  try{
   // check already exists
   $chk = $pdo->prepare("SELECT id FROM ccms_users WHERE email=? LIMIT 1");
   $chk->execute([$email]);
   if($chk->fetch()){
     $msg = "Email already exists! Login pannu da!";
   } else {
     // FIXED QUERY - simple-a vechirukken
     $sql = "INSERT INTO ccms_users (name, email, password, department, role) VALUES (?, ?, ?, ?, 'student')";
     $stmt = $pdo->prepare($sql);
     $stmt->execute([$name, $email, $pass, $dept]);
     $ok = "Account created successfully! Ready to login!";
   }
  } catch(Exception $e){
   $msg = "DB Error: " . $e->getMessage();
   // table name vera na try pannu
   try{
     $sql2 = "INSERT INTO users (name, email, password, department, role) VALUES (?, ?, ?, ?, 'student')";
     $stmt2 = $pdo->prepare($sql2);
     $stmt2->execute([$name, $email, $pass, $dept]);
     $ok = "Account created! Login pannu da!";
     $msg = "";
   } catch(Exception $e2){}
  }
 }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>CCMS - Register</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@800;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Outfit',sans-serif}
body{min-height:100vh;background:#eef2ff;display:flex;align-items:center;justify-content:center;padding:20px}
.box{width:1000px;max-width:100%;background:#fff;border-radius:24px;display:flex;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.15)}
.left{width:48%;background:linear-gradient(135deg,#2563eb,#1e40af);padding:50px 44px;color:#fff}
.logo{width:56px;height:56px;background:#fff;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:28px}
.title{font-size:38px;font-weight:900;line-height:1.05;margin-top:24px;letter-spacing:-1px}
.title span{color:#93c5fd}
.desc{font-size:13.5px;color:#bfdbfe;margin-top:12px;line-height:1.5}
.right{width:52%;padding:40px 44px}
.right h2{font-size:26px;font-weight:800;color:#0f172a}
.sub{font-size:13px;color:#64748b;margin:6px 0 20px}
label{font-size:11px;font-weight:700;letter-spacing:.5px;color:#334155}
.inp,.sel{width:100%;padding:12px 16px;border-radius:12px;border:1.5px solid #e2e8f0;background:#f8fafc;margin:7px 0 14px;outline:none;font-size:14px}
.inp:focus,.sel:focus{border-color:#2563eb;background:#fff}
.btn{width:100%;padding:13px;border:0;border-radius:12px;background:#2563eb;color:#fff;font-weight:700;font-size:15px;cursor:pointer}
.alert-err{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:14px}
.alert-ok{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:14px}
</style>
</head>
<body>
<div class="box">
<div class="left">
<div class="logo">🎓</div>
<div class="title">College<br><span>Complaint</span><br>Management<br>System</div>
<p class="desc">Create account in 30 seconds. Start submitting complaints securely.</p>
</div>
<div class="right">
<h2>Create Account ✨</h2>
<p class="sub">Join 2000+ students using CCMS</p>
<?php if($msg) echo "<div class='alert-err'>$msg</div>"; ?>
<?php if($ok) echo "<div class='alert-ok'>$ok <br><a href='index.php' style='color:#15803d;font-weight:700'>Login now →</a></div>"; ?>
<form method="POST">
<label>FULL NAME</label>
<input class="inp" type="text" name="name" required placeholder="Your name">
<label>EMAIL</label>
<input class="inp" type="email" name="email" required placeholder="you@college.edu">
<label>DEPARTMENT</label>
<select class="sel" name="department" required>
<option value="">Select Dept</option>
<option>CSE</option><option>ECE</option><option>EEE</option><option>MECH</option><option>CIVIL</option><option>IT</option>
</select>
<label>PASSWORD</label>
<input class="inp" type="password" name="password" required placeholder="Min 6 chars">
<button class="btn">Create Account →</button>
</form>
<p style="text-align:center;margin-top:16px;font-size:12px;color:#64748b">Already have account? <a href="index.php" style="color:#2563eb;font-weight:700;text-decoration:none">Sign in</a></p>
</div>
</div>
</body>
</html>