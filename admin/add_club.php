<?php

session_start();
include '../includes/db.php';


if(!isset($_SESSION['user']) || $_SESSION['user']['Role']!='admin'){
header("Location:../login.php");
exit();
}



$message="";


if(isset($_POST['add_club'])){


$name=mysqli_real_escape_string($conn,$_POST['club_name']);
$cat=mysqli_real_escape_string($conn,$_POST['category']);
$desc=mysqli_real_escape_string($conn,$_POST['description']);
$slots=(int)$_POST['slots'];


// CHECK DUPLICATE CLUB

$check=mysqli_query($conn,
"SELECT * FROM clubs WHERE ClubName='$name'");


if(mysqli_num_rows($check)>0){


$message="
<div class='alert alert-danger'>
Club already exists! Please add a different club.
</div>
";


}

else{


mysqli_query($conn,
"INSERT INTO clubs
(ClubName,Category,Description,Slots)
VALUES
('$name','$cat','$desc','$slots')");



$message="
<div class='alert alert-success'>
New club added successfully.
</div>
";


}


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


<h3>Add New Club</h3>


<?=$message?>



<div class="card shadow p-4">


<form method="POST">


<label>
Club Name
</label>

<input 
class="form-control mb-3"
name="club_name"
required>



<label>
Category
</label>

<input 
class="form-control mb-3"
name="category"
required>



<label>
Description
</label>

<textarea
class="form-control mb-3"
name="description">
</textarea>




<label>
Slots
</label>


<input
type="number"
class="form-control mb-3"
name="slots"
required>




<button 
class="btn btn-primary"
name="add_club">

Add Club

</button>


</form>


</div>


</div>


</body>

</html>