<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] != 'student') {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user'];
$uid  = mysqli_real_escape_string($conn, $user['ID']);

$regs = mysqli_query($conn, "SELECT r.*, c.ClubName FROM registrations r JOIN clubs c ON r.ClubID = c.ClubID WHERE r.ID = '$uid' ORDER BY r.Date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>ClubMate - My Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary px-4">
    <span class="navbar-brand fw-bold">ClubMate</span>
    <div class="d-flex align-items-center gap-3">
        <span class="text-white"><?php echo htmlspecialchars($user['Name']); ?></span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>
<div class="container mt-4">
    <h5 class="fw-bold mb-3">My Registration Status</h5>
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>Club</th>
                        <th>Date Applied</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $has = false;
                while ($reg = mysqli_fetch_assoc($regs)) {
                    $has = true;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reg['ClubName']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Date']); ?></td>
                        <td>
                            <?php
                            if ($reg['Status'] == 'Approved')     echo "<span class='badge bg-success'>Approved</span>";
                            elseif ($reg['Status'] == 'Declined') echo "<span class='badge bg-danger'>Declined</span>";
                            else                                   echo "<span class='badge bg-warning text-dark'>Pending</span>";
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                <?php if (!$has) echo "<tr><td colspan='3' class='text-center text-muted'>No registrations yet</td></tr>"; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
        <a href="dashboard.php" class="btn btn-outline-primary">Back to Dashboard</a>
    </div>
</div>
</body>
</html>