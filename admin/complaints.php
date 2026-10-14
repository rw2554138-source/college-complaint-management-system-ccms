<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'admin'
) {
    header('Location: ../login.php');
    exit;
}

try {
    $stmt = $conn->query(
        "SELECT c.id, c.user_id, c.category, c.subject,
                c.description, c.status, c.admin_response,
                u.name AS student_name, u.email
         FROM complaints c
         JOIN users u ON c.user_id = u.id
         ORDER BY c.id DESC"
    );

    $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('Unable to load complaints. Please check your database tables.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Complaints</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f4f6f9;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .complaint-card {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 8px #ddd;
            overflow-wrap: anywhere;
        }

        input, select, textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            margin: 8px 0 15px;
        }

        button {
            padding: 10px 18px;
            background: #234a84;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        a { color: #234a84; }
    </style>
</head>
<body>
<div class="container">
    <h1>Student Complaints</h1>

    <p><a href="dashboard.php">Back to Admin Dashboard</a></p>

    <?php if (empty($complaints)): ?>
        <p>No complaints found.</p>
    <?php else: ?>

        <?php foreach ($complaints as $c): ?>
            <div class="complaint-card">
                <h3>
                    Complaint #<?= (int)$c['id'] ?> —
                    <?= htmlspecialchars($c['subject'], ENT_QUOTES, 'UTF-8') ?>
                </h3>

                <p>
                    <strong>Student:</strong>
                    <?= htmlspecialchars($c['student_name'], ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    <strong>Email:</strong>
                    <?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    <strong>Category:</strong>
                    <?= htmlspecialchars($c['category'], ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    <strong>Description:</strong><br>
                    <?= nl2br(htmlspecialchars($c['description'], ENT_QUOTES, 'UTF-8')) ?>
                </p>

                <p>
                    <strong>Current Status:</strong>
                    <?= htmlspecialchars($c['status'], ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    <strong>Admin Response:</strong><br>
                    <?= htmlspecialchars($c['admin_response'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </p>

                <form action="update_status.php" method="POST">
                    <input type="hidden" name="complaint_id"
                           value="<?= (int)$c['id'] ?>">

                    <label>Status</label>
                    <select name="status" required>
                        <option value="Pending" <?= $c['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="In Progress" <?= $c['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="Resolved" <?= $c['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                        <option value="Closed" <?= $c['status'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
                    </select>

                    <label>Response</label>
                    <textarea name="admin_reply" rows="3"
                              placeholder="Enter your response"><?= htmlspecialchars($c['admin_response'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

                    <button type="submit">Update Complaint</button>
                </form>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

    <p><a href="../logout.php">Logout</a></p>
</div>
</body>
</html>