<?php

session_start();
include '../includes/db.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['Role']!='admin'){
    header("Location:../login.php");
    exit();
}

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    header("Location:all_clubs.php");
    exit();
}

$id=(int)$_GET['id'];

$result=mysqli_query($conn,"SELECT * FROM clubs WHERE ClubID='$id'");
$club=mysqli_fetch_assoc($result);

if(!$club){
    header("Location:all_clubs.php");
    exit();
}

$message="";

if(isset($_POST['update'])){

    $name=mysqli_real_escape_string($conn,$_POST['club_name']);
    $category=mysqli_real_escape_string($conn,$_POST['category']);
    $description=mysqli_real_escape_string($conn,$_POST['description']);
    $slots=(int)$_POST['slots'];

    // Check duplicate club name (excluding this club)
    $check=mysqli_query($conn,
    "SELECT * FROM clubs
     WHERE ClubName='$name'
     AND ClubID!='$id'");

    if(mysqli_num_rows($check)>0){

        $message="<div class='alert alert-danger'>
        Club name already exists.
        </div>";

    }else{

        mysqli_query($conn,
        "UPDATE clubs SET

        ClubName='$name',
        Category='$category',
        Description='$description',
        Availability='$slots'

        WHERE ClubID='$id'");

        header("Location:all_clubs.php");
        exit();
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

<h3>Edit Club</h3>

<?=$message?>

<div class="card shadow p-4">

<form method="POST">

<label class="form-label">Club Name</label>

<input
type="text"
name="club_name"
class="form-control mb-3"
value="<?=$club['ClubName']?>"
required>

<label class="form-label">Category</label>

<input
type="text"
name="category"
class="form-control mb-3"
value="<?=$club['Category']?>"
required>

<label class="form-label">Description</label>

<textarea
name="description"
class="form-control mb-3"
rows="4"
required><?=$club['Description']?></textarea>

<label class="form-label">Available Slots</label>

<input
type="number"
name="slots"
class="form-control mb-3"
value="<?=$club['Availability']?>"
min="0"
required>

<button
class="btn btn-success"
name="update">

Update Club

</button>

<a href="all_clubs.php"
class="btn btn-secondary">

Cancel

</a>

</form>

</div>

</div>

</body>
</html>