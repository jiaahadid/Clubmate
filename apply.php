<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] != 'student') {
    header('Location: login.php');
    exit();
}

// FIX: validate club_id exists before using it
if (!isset($_GET['club_id']) || !is_numeric($_GET['club_id'])) {
    header('Location: dashboard.php');
    exit();
}

$user    = $_SESSION['user'];
$club_id = (int)$_GET['club_id'];
$club    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM clubs WHERE ClubID = '$club_id'"));

if (!$club) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $uid = mysqli_real_escape_string($conn, $user['ID']);

    // Check if the student already has an approved club
    $approved = mysqli_query($conn,
    "SELECT * FROM registrations
     WHERE ID='$uid'
     AND Status='Approved'");

    if(mysqli_num_rows($approved) > 0){

        $error = "You are already an approved member of a club.";

    }

    // Check if already applied to this club
    else{

        $check = mysqli_query($conn,
        "SELECT * FROM registrations
         WHERE ID='$uid'
         AND ClubID='$club_id'");

        if(mysqli_num_rows($check) > 0){

            $error = "You have already applied for this club.";

        }

        // Check if the club is full
        else if($club['Availability'] <= 0){

            $error = "This club is already full.";

        }

        else{

            $date = date('Y-m-d');

            mysqli_query($conn,
            "INSERT INTO registrations
            (ID, ClubID, Status, Date)
            VALUES
            ('$uid','$club_id','Pending','$date')");

            header("Location: status.php");
            exit();

        }

    }

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ClubMate - Apply</title>
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
    <div class="card shadow-sm p-4" style="max-width:500px">
        <h5 class="fw-bold mb-1">Register for Club</h5>
        <p class="text-muted mb-4"><?php echo htmlspecialchars($club['ClubName']); ?></p>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>".htmlspecialchars($error)."</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">ID</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['ID']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['Name']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Club</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($club['ClubName']); ?>" disabled>
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit Registration</button>
            <a href="dashboard.php" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>