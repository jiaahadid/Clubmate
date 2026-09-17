<?php

session_start();
include '../includes/db.php';


if(!isset($_SESSION['user']) || $_SESSION['user']['Role']!='admin'){
header("Location:../login.php");
exit();
}


$message="";


if(isset($_POST['assign'])){


$id=mysqli_real_escape_string($conn,$_POST['member_id']);

$club=(int)$_POST['club_id'];

$role=mysqli_real_escape_string($conn,$_POST['role']);



$check = mysqli_query($conn,
"SELECT *
FROM commitees
WHERE ID='$id'
AND ClubID='$club'");



if(mysqli_num_rows($check)>0){


$message="
<div class='alert alert-danger'>
This student already has a role.
</div>";

}


else{


mysqli_query($conn,
"INSERT INTO commitees
(ID,ClubID,Role)
VALUES
('$id','$club','$role')");


$message="
<div class='alert alert-success'>
Role assigned successfully.
</div>";

}


}


?>



<!DOCTYPE html>
<html>


<head>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
rel="stylesheet">


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

</head>



<body>


<?php include "menu.php"; ?>



<div class="container mt-4">


<h3>Assign Committee Role</h3>


<?=$message?>



<div class="card shadow p-4">



<form method="POST">



<label>
Student ID
</label>


<input 
type="text"
name="member_id"
id="studentID"
class="form-control mb-3"
placeholder="Type Student ID manually"
required>



<label>
Registered Club
</label>


<input 
type="text"
id="clubName"
class="form-control mb-3"
placeholder="Club will appear automatically"
readonly>



<!-- hidden value sent to database -->

<input 
type="hidden"
name="club_id"
id="clubID">





<label>
Role
</label>


<select 
name="role"
class="form-select mb-3">


<option value="President">
President
</option>


<option value="Vice President">
Vice President
</option>


<option value="Secretary">
Secretary
</option>


</select>



<button 
class="btn btn-primary"
name="assign">

Assign Role

</button>


</form>


</div>


</div>





<script>


$("#studentID").keyup(function(){


let id=$(this).val();



if(id!=""){



$.ajax({


url:"get_student_club.php",

method:"POST",

data:{
student_id:id
},


success:function(data){


let result=JSON.parse(data);



$("#clubName").val(result.club_name);

$("#clubID").val(result.club_id);



}


});



}


});



</script>



</body>

</html>