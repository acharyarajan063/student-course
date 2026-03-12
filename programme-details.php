<?php
include("config/db.php");

$id=$_GET['id'];

$sql="SELECT * FROM Programmes WHERE ProgrammeID=$id";
$programme=$conn->query($sql)->fetch_assoc();

echo "<h1>".$programme['ProgrammeName']."</h1>";

$sql2="
SELECT Modules.ModuleName, ProgrammeModules.Year
FROM ProgrammeModules
JOIN Modules ON ProgrammeModules.ModuleID=Modules.ModuleID
WHERE ProgrammeModules.ProgrammeID=$id
";

$result=$conn->query($sql2);

while($row=$result->fetch_assoc()){
echo "Year ".$row['Year']." - ".$row['ModuleName']."<br>";
}
?>