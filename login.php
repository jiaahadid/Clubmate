<?php
session_start();
include 'includes/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id       = mysqli_real_escape_string($conn, $_POST['student_id']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE ID='$id' LIMIT 1");

    if (!$result) {
        die(mysqli_error($conn));
    }

    $user = mysqli_fetch_assoc($result);

    if ($user) {
        if ($password == $user['Password']) {
            $user['Role'] = strtolower($user['Role']);
            $_SESSION['user'] = $user;
            $role = $user['Role'];

            if ($role == "admin") {
                header("Location: admin/home.php");
                exit();
            } elseif ($role == "committee") {
                header("Location: commitee/home.php");
                exit();
            } elseif ($role == "student") {
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Role not recognized: " . htmlspecialchars($user['Role']);
            }
        } else {
            $error = "Wrong password.";
        }
    } else {
        $error = "ID not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ClubMate Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh">
    <div class="card p-4 shadow" style="width:400px">
        <h4 class="text-center fw-bold mb-1">ClubMate</h4>
        <p class="text-center text-muted mb-4">KPMIM Club Registration System</p>
        <?php if ($error != "") echo "<div class='alert alert-danger'>".htmlspecialchars($error)."</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">ID</label>
                <input type="text" name="student_id" class="form-control" placeholder="Enter your ID" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <p class="text-center mt-3 text-muted" style="font-size:13px">Don't have an account? <a href="register.php">Register here</a></p>
        <p class="text-center mt-1 text-muted" style="font-size:13px"><a href="index.php">Back to home</a></p>
    </div>
</div>
</body>
</html>