<?php

session_start();

include '../includes/db.php';


if(!isset($_SESSION['user']) || $_SESSION['user']['Role']!='committee'){
    header("Location:../login.php");
    exit();
}


$user=$_SESSION['user'];




// FIND COMMITTEE CLUB

$clubQuery=mysqli_query($conn,
"SELECT ClubID 
FROM commitees 
WHERE ID='".$user['ID']."'");


$clubData=mysqli_fetch_assoc($clubQuery);

$club_id=$clubData ? $clubData['ClubID'] : 0;





// APPROVE / REJECT


if(isset($_POST['action'])){


$reg_id=(int)$_POST['reg_id'];

$student_id=mysqli_real_escape_string($conn,$_POST['student_id']);

$action=$_POST['action'];



if($action=="approve"){



mysqli_query($conn,
"UPDATE registrations
SET Status='Approved'
WHERE RegistrationID='$reg_id'
AND ClubID='$club_id'");

mysqli_query($conn,
"UPDATE clubs
SET Slots = Slots - 1
WHERE ClubID='$club_id'
AND Slots > 0");




// reject other pending registrations

mysqli_query($conn,

"UPDATE registrations

SET Status='Declined'

WHERE ID='$student_id'

AND RegistrationID!='$reg_id'

AND Status='Pending'");





// auto add committee member


$check=mysqli_query($conn,

"SELECT *

FROM commitees

WHERE ID='$student_id'

AND ClubID='$club_id'");



if(mysqli_num_rows($check)==0){



// Generate next COM ID
$getLast = mysqli_query($conn,
"SELECT MemberID
FROM commitees
ORDER BY CommiteesID DESC
LIMIT 1");

if(mysqli_num_rows($getLast) > 0){

    $last = mysqli_fetch_assoc($getLast);

    $num = (int)substr($last['MemberID'],3);

    $num++;

}else{

    $num = 1;

}

$memberID = "COM".str_pad($num,3,"0",STR_PAD_LEFT);

mysqli_query($conn,
"INSERT INTO commitees
(MemberID,ID,ClubID,Role)
VALUES
('$memberID','$student_id','$club_id','Member')");


}



}




if($action=="decline"){


mysqli_query($conn,

"UPDATE registrations

SET Status='Declined'

WHERE RegistrationID='$reg_id'

AND ClubID='$club_id'");


}



header("Location:home.php");

exit();

}






// DELETE CURRENT COMMITTEE MEMBER


if(isset($_GET['delete'])){


$delete_id=(int)$_GET['delete'];



mysqli_query($conn,

"DELETE FROM commitees

WHERE CommiteesID='$delete_id'

AND ClubID='$club_id'");



header("Location:home.php");

exit();

}







// SEARCH


$requestSearch="";

$memberSearch="";



if(isset($_GET['request_search'])){

$requestSearch=mysqli_real_escape_string($conn,$_GET['request_search']);

}



if(isset($_GET['member_search'])){

$memberSearch=mysqli_real_escape_string($conn,$_GET['member_search']);

}








// PENDING REQUESTS


$requests=mysqli_query($conn,

"SELECT 

r.*,

u.Name,

c.ClubName


FROM registrations r


JOIN users u

ON r.ID=u.ID


JOIN clubs c

ON r.ClubID=c.ClubID



WHERE r.Status='Pending'

AND r.ClubID='$club_id'


AND

(

u.Name LIKE '%$requestSearch%'

OR r.ID LIKE '%$requestSearch%'

)

");







// APPROVED MEMBERS


$members=mysqli_query($conn,

"SELECT 

r.*,

u.Name,

c.ClubName


FROM registrations r


JOIN users u

ON r.ID=u.ID


JOIN clubs c

ON r.ClubID=c.ClubID



WHERE r.Status='Approved'

AND r.ClubID='$club_id'


AND

(

u.Name LIKE '%$memberSearch%'

OR r.ID LIKE '%$memberSearch%'

)

");






// CURRENT COMMITTEE


$committee_members=mysqli_query($conn,

"SELECT

cm.*,

u.Name,

c.ClubName


FROM commitees cm


JOIN users u

ON cm.ID=u.ID


JOIN clubs c

ON cm.ClubID=c.ClubID


WHERE cm.ClubID='$club_id'

");





?>



<!DOCTYPE html>

<html>


<head>


<title>Committee Panel</title>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
rel="stylesheet">


</head>



<body class="bg-light">



<nav class="navbar navbar-dark bg-primary px-4">


<span class="navbar-brand fw-bold">

ClubMate Committee

</span>



<div>

<span class="text-white me-3">

<?=$user['Name']?>

</span>


<a href="../logout.php"
class="btn btn-light btn-sm">

Logout

</a>


</div>


</nav>





<div class="container mt-4">







<!-- REQUEST -->


<h4>

Incoming Registration Requests

</h4>



<form method="GET"
class="d-flex mb-3">


<input

class="form-control me-2"

name="request_search"

placeholder="Search ID or name"

value="<?=$requestSearch?>">



<button class="btn btn-primary">

Search

</button>


</form>





<table class="table table-bordered">


<tr class="table-primary">

<th>ID</th>

<th>Name</th>

<th>Club</th>

<th>Date</th>

<th>Action</th>


</tr>




<?php


$count=0;


while($r=mysqli_fetch_assoc($requests)){


$count++;

?>


<tr>


<td><?=$r['ID']?></td>


<td><?=$r['Name']?></td>


<td><?=$r['ClubName']?></td>


<td><?=$r['Date']?></td>



<td>



<form method="POST"
class="d-inline">


<input type="hidden"
name="reg_id"
value="<?=$r['RegistrationID']?>">


<input type="hidden"
name="student_id"
value="<?=$r['ID']?>">



<button

name="action"

value="approve"

class="btn btn-success btn-sm">

Approve

</button>


</form>





<form method="POST"
class="d-inline">


<input type="hidden"
name="reg_id"
value="<?=$r['RegistrationID']?>">


<input type="hidden"
name="student_id"
value="<?=$r['ID']?>">



<button

name="action"

value="decline"

class="btn btn-danger btn-sm">

Reject

</button>



</form>


</td>


</tr>



<?php } ?>



<?php

if($count==0)

echo "

<tr>
<td colspan='5'
class='text-center'>

No pending request

</td>
</tr>";

?>



</table>









<!-- APPROVED MEMBERS -->


<h4 class="mt-5">

Approved Members

</h4>




<form method="GET"
class="d-flex mb-3">


<input

class="form-control me-2"

name="member_search"

placeholder="Search approved ID or name"

value="<?=$memberSearch?>">


<button class="btn btn-success">

Search

</button>


</form>





<table class="table table-bordered">


<tr class="table-success">


<th>ID</th>

<th>Name</th>

<th>Club</th>

<th>Date</th>


</tr>




<?php


while($m=mysqli_fetch_assoc($members)){


?>


<tr>


<td><?=$m['ID']?></td>


<td><?=$m['Name']?></td>


<td><?=$m['ClubName']?></td>


<td><?=$m['Date']?></td>


</tr>


<?php } ?>


</table>








<!-- COMMITTEE -->


<h4 class="mt-5">

Current Members

</h4>



<table class="table table-bordered">


<tr class="table-warning">


<th>ID</th>

<th>Name</th>

<th>Club</th>

<th>Role</th>

<th>Action</th>


</tr>





<?php


while($cm=mysqli_fetch_assoc($committee_members)){


?>


<tr>


<td><?=$cm['ID']?></td>


<td><?=$cm['Name']?></td>


<td><?=$cm['ClubName']?></td>


<td><?=$cm['Role']?></td>



<td>


<a

href="?delete=<?=$cm['CommiteesID']?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this committee member?')">


Delete


</a>


</td>



</tr>


<?php } ?>



</table>






</div>





</body>

</html>