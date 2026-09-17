<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] != 'student') {
    header('Location: login.php');
    exit();
}

$user  = $_SESSION['user'];
$clubs = mysqli_query($conn, "SELECT * FROM clubs");
$uid = mysqli_real_escape_string($conn, $user['ID']);

// Check if the student already has an approved registration
$approved = mysqli_query($conn,
"SELECT * FROM registrations
WHERE ID='$uid'
AND Status='Approved'");

$hasApproved = mysqli_num_rows($approved) > 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>ClubMate - Student Dashboard</title>
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
    <h5 class="fw-bold mb-4">Available Clubs</h5>
    <div class="row g-3">
        <?php
        $uid = mysqli_real_escape_string($conn, $user['ID']);
        while ($club = mysqli_fetch_assoc($clubs)) {
            $cid = (int)$club['ClubID'];
        ?>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold"><?php echo htmlspecialchars($club['ClubName']); ?></h6>
                    <p class="text-muted mb-1" style="font-size:13px"><?php echo htmlspecialchars($club['Category']); ?></p>
                    <p style="font-size:13px"><?php echo htmlspecialchars($club['Description']); ?></p>
                    <span class="badge <?php echo $club['Availability'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                        <?php echo $club['Availability'] > 0 ? (int)$club['Availability'].' slots left' : 'Full'; ?>
                    </span>
                </div>
                <div class="card-footer bg-white border-0">
                    <?php

$check = mysqli_query($conn,
"SELECT * FROM registrations
WHERE ID='$uid'
AND ClubID='$cid'");

if(mysqli_num_rows($check)>0){

    $reg=mysqli_fetch_assoc($check);

    echo "<span class='badge bg-secondary'>
    Already Applied - ".$reg['Status']."
    </span>";

}
elseif($hasApproved){

    echo "<button class='btn btn-success btn-sm' disabled>
    Already Joined a Club
    </button>";

}
elseif($club['Availability']<=0){

    echo "<button class='btn btn-secondary btn-sm' disabled>
    Full
    </button>";

}
else{

    echo "<a href='apply.php?club_id=$cid'
    class='btn btn-primary btn-sm'>
    Register
    </a>";

}

?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <div class="mt-4">
        <a href="status.php" class="btn btn-outline-primary">Check My Registration Status</a>
    </div>
</div>
</body>
</html>