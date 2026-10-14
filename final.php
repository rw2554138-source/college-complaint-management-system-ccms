<?php
session_start();
$_SESSION['admin']=1;
$_SESSION['admin_id']=1;
$conn = new mysqli("localhost","root","","ccms_db");
if($conn->connect_error){ die("DB Name thappu: ".$conn->connect_error); }

if(isset($_GET['id'])){
 $id=intval($_GET['id']);
 $q=$conn->query("SELECT * FROM ccms_complaints WHERE id=$id");
 $c=$q->fetch_assoc();
 echo "<h2>#{$c['id']} - {$c['subject']}</h2><p><b>Status:</b> {$c['status']}</p><div style='background:#eee;padding:15px'>{$c['description']}</div><br><a href='final.php'>Back to List</a>";
 exit();
}
$res=$conn->query("SELECT * FROM ccms_complaints ORDER BY id DESC");
echo "<h2>ADMIN - Click View to see Details</h2><table border=1 cellpadding=10><tr><th>ID</th><th>Subject</th><th>View</th></tr>";
while($c=$res->fetch_assoc()){
 echo "<tr><td>{$c['id']}</td><td>{$c['subject']}</td><td><a href='final.php?id={$c['id']}'><b>View</b></a></td></tr>";
}
?>