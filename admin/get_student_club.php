<?php

session_start();
include '../includes/db.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['Role']!='admin'){
    header("Location:../login.php");
    exit();
}


if(isset($_POST['student_id'])){


$id=mysqli_real_escape_string($conn,$_POST['student_id']);



$sql="
SELECT 
c.ClubID,
c.ClubName

FROM registrations r

JOIN clubs c 
ON r.ClubID=c.ClubID

WHERE r.ID='$id'
AND r.Status='Approved'
";



$result=mysqli_query($conn,$sql);



if(mysqli_num_rows($result)>0){


$row=mysqli_fetch_assoc($result);



echo json_encode([

"club_id"=>$row['ClubID'],

"club_name"=>$row['ClubName']

]);


}

else{


echo json_encode([

"club_id"=>"",

"club_name"=>"No registered club found"

]);


}



}

?>