<?php

session_start();

include '../includes/db.php';


if(!isset($_SESSION['user']) || $_SESSION['user']['Role']!='admin'){
header("Location:../login.php");
exit();
}



$message="";



// DELETE COMMITTEE

if(isset($_GET['delete'])){


$id=(int)$_GET['delete'];


mysqli_query($conn,
"DELETE FROM commitees 
WHERE CommiteesID='$id'");


header("Location:committee.php");
exit();

}




// UPDATE ROLE

if(isset($_POST['update'])){


$cid=(int)$_POST['committee_id'];

$role=mysqli_real_escape_string($conn,$_POST['role']);



mysqli_query($conn,
"UPDATE commitees 
SET Role='$role'
WHERE CommiteesID='$cid'");


header("Location:committee.php");
exit();

}



?>



<!DOCTYPE html>

<html>


<head>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
rel="stylesheet">


</head>



<body>



<?php include "menu.php"; ?>



<div class="container mt-4">



<h3>
Current Committee Members
</h3>





<table class="table table-bordered shadow">


<tr class="table-warning">

<th>ID</th>

<th>Name</th>

<th>Club</th>

<th>Role</th>

<th>Action</th>

</tr>



<?php


$sql="
SELECT 
cm.*,
u.Name,
c.ClubName

FROM commitees cm

JOIN users u 
ON cm.ID=u.ID

JOIN clubs c
ON cm.ClubID=c.ClubID
";



$result=mysqli_query($conn,$sql);



while($row=mysqli_fetch_assoc($result)){


?>



<tr>


<td>
<?=$row['ID']?>
</td>


<td>
<?=$row['Name']?>
</td>


<td>
<?=$row['ClubName']?>
</td>


<td>
<?=$row['Role']?>
</td>



<td>


<!-- EDIT BUTTON -->


<button 
class="btn btn-warning btn-sm"
data-bs-toggle="modal"
data-bs-target="#edit<?=$row['CommiteesID']?>">

Edit

</button>




<a 
href="?delete=<?=$row['CommiteesID']?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Remove this committee member?')">

Delete

</a>



</td>


</tr>






<!-- EDIT MODAL -->


<div class="modal fade"
id="edit<?=$row['CommiteesID']?>">



<div class="modal-dialog">


<div class="modal-content p-3">



<h5>Edit Role</h5>



<form method="POST">



<input type="hidden"
name="committee_id"
value="<?=$row['CommiteesID']?>">





<select 
class="form-select mb-3"
name="role">



<option 
<?=$row['Role']=="President"?"selected":""?>>

President

</option>


<option
<?=$row['Role']=="Vice President"?"selected":""?>>

Vice President

</option>


<option
<?=$row['Role']=="Secretary"?"selected":""?>>

Secretary

</option>



</select>



<button 
class="btn btn-primary"
name="update">

Save Changes

</button>



</form>



</div>

</div>


</div>





<?php } ?>



</table>



</div>





<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>


</html>