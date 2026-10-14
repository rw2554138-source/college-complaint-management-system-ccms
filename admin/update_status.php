<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $status = $_POST['status'] ?? 'Pending';
    $admin_remark = $_POST['admin_remark'] ?? '';

    try {
        $stmt = $pdo->prepare("UPDATE ccms_complaints SET status = ?, admin_remark = ? WHERE id = ?");
        $stmt->execute([$status, $admin_remark, $id]);
        
        header("Location: complaints.php?msg=updated");
        exit;
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>