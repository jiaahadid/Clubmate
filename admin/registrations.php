<?php

session_start();

include '../includes/db.php';


if(!isset($_SESSION['user']) || $_SESSION['user']['Role']!='admin'){
header("Location:../login.php");
exit();
}


$search="";


if(isset($_GET['search'])){

$search=mysqli_real_escape_string($conn,$_GET['search']);

}



$sql="
SELECT r.*,u.Name,c.ClubName
FROM registrations r
JOIN users u 
ON r.ID=u.ID
JOIN clubs c
ON r.ClubID=c.ClubID
WHERE 
u.Name LIKE '%$search%'
OR c.ClubName LIKE '%$search%'
OR r.Status LIKE '%$search%'
";



$result=mysqli_query($conn,$sql);


?>


<!DOCTYPE html>

<html>

<head>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


</head>



<body>



<?php include "menu.php"; ?>



<div class="container mt-4">



<h3>
All Registration
</h3>



<!-- SEARCH -->


<form method="GET" class="d-flex mb-3">


<input 
class="form-control me-2"
name="search"
placeholder="Search student, club or status..."
value="<?=$search?>">


<button class="btn btn-primary">

Search

</button>


<a href="registrations.php"
class="btn btn-secondary ms-2">

Reset

</a>


</form>






<table class="table table-bordered shadow">



<tr class="table-primary">


<th>ID</th>

<th>Student</th>

<th>Club</th>

<th>Date</th>

<th>Status</th>


</tr>




<?php


if(mysqli_num_rows($result)>0){


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

<?=$row['Date']?>

</td>



<td>


<?php

if($row['Status']=="Approved")
echo "<span class='badge bg-success'>Approved</span>";

elseif($row['Status']=="Declined")
echo "<span class='badge bg-danger'>Declined</span>";

else
echo "<span class='badge bg-warning text-dark'>Pending</span>";

?>


</td>



</tr>


<?php

}

}

else{


echo "
<tr>
<td colspan='5' class='text-center'>
No registration found
</td>
</tr>";

}


?>



</table>



</div>


</body>

</html>