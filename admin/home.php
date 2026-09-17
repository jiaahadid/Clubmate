<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

include '../includes/db.php';


if(!isset($_SESSION['user']) || $_SESSION['user']['Role']!='admin')
{
    header("Location:../login.php");
    exit();
}


$user=$_SESSION['user'];



$total_students = mysqli_num_rows(
    mysqli_query($conn,"SELECT * FROM users WHERE Role='student'")
);


$total_clubs = mysqli_num_rows(
    mysqli_query($conn,"SELECT * FROM clubs")
);


$total_pending = mysqli_num_rows(
    mysqli_query($conn,"SELECT * FROM registrations WHERE Status='Pending'")
);



?>


<!DOCTYPE html>

<html>

<head>

<title>ClubMate Admin</title>


<link 
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
rel="stylesheet">


</head>



<body class="bg-light">



<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4">


<a class="navbar-brand fw-bold"
href="home.php">

ClubMate Admin Panel

</a>



<button class="navbar-toggler"
data-bs-toggle="collapse"
data-bs-target="#menu">

<span class="navbar-toggler-icon"></span>

</button>



<div class="collapse navbar-collapse"
id="menu">



<ul class="navbar-nav me-auto">


<li class="nav-item">

<a class="nav-link active"
href="home.php">

Dashboard

</a>

</li>



<li class="nav-item">

<a class="nav-link"
href="add_club.php">

Add Club

</a>

</li>



<li class="nav-item">

<a class="nav-link"
href="all_clubs.php">

All Clubs

</a>

</li>




<li class="nav-item">

<a class="nav-link"
href="registrations.php">

All Registration

</a>

</li>




<li class="nav-item">

<a class="nav-link"
href="assign_roles.php">

Assign Roles

</a>

</li>




<li class="nav-item">

<a class="nav-link"
href="committee.php">

Current Committee

</a>

</li>


</ul>





<span class="text-white me-3">

Welcome,
<?php echo htmlspecialchars($user['Name']); ?>

</span>



<a href="../logout.php"
class="btn btn-outline-light btn-sm">

Logout

</a>



</div>


</nav>






<div class="container mt-5">



<h3 class="fw-bold mb-4">

Admin Dashboard

</h3>





<div class="row g-4">



<div class="col-md-4">


<div class="card shadow text-center p-4">


<h1 class="text-primary">

<?php echo $total_students; ?>

</h1>


<h5>

Total Students

</h5>


</div>


</div>





<div class="col-md-4">


<div class="card shadow text-center p-4">


<h1 class="text-success">

<?php echo $total_clubs; ?>

</h1>


<h5>

Total Clubs

</h5>


</div>


</div>






<div class="col-md-4">


<div class="card shadow text-center p-4">


<h1 class="text-warning">

<?php echo $total_pending; ?>

</h1>


<h5>

Pending Registration

</h5>


</div>


</div>




</div>







<div class="row mt-5 g-3">



<div class="col-md-3">

<a href="add_club.php"
class="btn btn-primary w-100 p-3">

+ Add Club

</a>

</div>




<div class="col-md-3">

<a href="all_clubs.php"
class="btn btn-success w-100 p-3">

View Clubs

</a>

</div>





<div class="col-md-3">

<a href="registrations.php"
class="btn btn-warning w-100 p-3">

Registrations

</a>

</div>





<div class="col-md-3">

<a href="committee.php"
class="btn btn-dark w-100 p-3">

Committee

</a>

</div>




</div>





</div>







<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">

</script>


</body>

</html>