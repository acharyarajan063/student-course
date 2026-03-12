<form method="POST">

<input type="text" name="name" placeholder="Name">

<input type="email" name="email" placeholder="Email">

<select name="programme">
<option value="1">Computer Science</option>
<option value="4">Cyber Security</option>
</select>

<button type="submit">Register</button>

</form>
<?php

include("config/db.php");

if(isset($_POST['name'])){

$name=$_POST['name'];
$email=$_POST['email'];
$programme=$_POST['programme'];

$sql="INSERT INTO InterestedStudents
(ProgrammeID,StudentName,Email)
VALUES
('$programme','$name','$email')";

$conn->query($sql);

echo "Interest Registered";

}

?>