<?php

session_start();
include '../includes/db.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['Role']!='admin'){
    header("Location:../login.php");
    exit();
}

if(isset($_GET['delete'])){

    $id=(int)$_GET['delete'];

    mysqli_query($conn,"DELETE FROM clubs WHERE ClubID='$id'");

    header("Location:all_clubs.php");
    exit();
}

?>

<!DOCTYPE html>
<html>

<head>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<?php include "menu.php"; ?>

<div class="container mt-4">

<h3>All Clubs</h3>

<table class="table table-bordered shadow">

<tr class="table-primary">

<th>Club ID</th>
<th>Name</th>
<th>Category</th>
<th>Description</th>
<th>Available Slots</th>
<th>Action</th>

</tr>

<?php

$result=mysqli_query($conn,"SELECT * FROM clubs");

while($c=mysqli_fetch_assoc($result)){

?>

<tr>

<td><?=$c['ClubID']?></td>

<td><?=$c['ClubName']?></td>

<td><?=$c['Category']?></td>

<td><?=$c['Description']?></td>

<td><?=$c['Availability']?></td>

<td>

<a href="edit_club.php?id=<?=$c['ClubID']?>"
class="btn btn-warning btn-sm">

Edit

</a>

<a href="?delete=<?=$c['ClubID']?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this club?')">

Delete

</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>