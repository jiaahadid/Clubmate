<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id       = mysqli_real_escape_string($conn, $_POST['student_id']);
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role     = $_POST['role'];

    // FIX: only allow valid roles — prevents self-registering as admin
    $allowed_roles = ['student', 'committee'];
    if (!in_array($role, $allowed_roles)) {
        $error = "Invalid role selected.";
    } else {
        $role = mysqli_real_escape_string($conn, $role);

        $check = mysqli_query($conn, "SELECT * FROM users WHERE ID = '$id'");

        if (mysqli_num_rows($check) > 0) {
            $error = "ID already registered.";
        } else {
            $insert = mysqli_query($conn, "INSERT INTO users (ID, Name, Password, Role) VALUES ('$id', '$name', '$password', '$role')");
            if ($insert) {
                if ($role == 'committee') {
                    $club_id = mysqli_real_escape_string($conn, $_POST['club_id']);
                    mysqli_query($conn, "INSERT INTO commitees (ID, ClubID, Role) VALUES ('$id', '$club_id', 'Member')");
                }
                header('Location: login.php');
                exit();
            } else {
                $error = "Something went wrong. Error: " . mysqli_error($conn);
            }
        }
    }
}

$clubs = mysqli_query($conn, "SELECT * FROM clubs");
$clubs_list = [];
while ($c = mysqli_fetch_assoc($clubs)) {
    $clubs_list[] = $c;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ClubMate Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh">
    <div class="card p-4 shadow" style="width:400px">
        <h4 class="text-center mb-1 fw-bold">ClubMate</h4>
        <p class="text-center text-muted mb-4">Create your account</p>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>".htmlspecialchars($error)."</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">ID</label>
                <input type="text" name="student_id" class="form-control" placeholder="Enter your ID" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Create a password" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <!-- FIX: removed 'admin' option — admins should not be self-registerable -->
                <select name="role" class="form-select" required id="roleSelect" onchange="toggleClub()">
                    <option value="" disabled selected>Select your role</option>
                    <option value="student">Student</option>
                    <option value="committee">Club Committee</option>
                </select>
            </div>
            <div class="mb-3" id="clubDiv" style="display:none">
                <label class="form-label">Select Club</label>
                <select name="club_id" class="form-select" id="clubSelect">
                    <option value="" disabled selected>Select your club</option>
                    <?php foreach ($clubs_list as $c) { ?>
                        <option value="<?php echo (int)$c['ClubID']; ?>"><?php echo htmlspecialchars($c['ClubName']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <p class="text-center mt-3 text-muted" style="font-size:13px">Already have an account? <a href="login.php">Login here</a></p>
        <p class="text-center mt-1 text-muted" style="font-size:13px"><a href="index.php">Back to home</a></p>
    </div>
</div>
<script>
function toggleClub() {
    var role = document.getElementById('roleSelect').value;
    var clubDiv = document.getElementById('clubDiv');
    var clubSelect = document.getElementById('clubSelect');
    if (role == 'committee') {
        clubDiv.style.display = 'block';
        clubSelect.required = true;
    } else {
        clubDiv.style.display = 'none';
        clubSelect.required = false;
    }
}
</script>
</body>
</html>