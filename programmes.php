<?php

require_once __DIR__ . "/config/db.php";

/* QUERY PROGRAMMES */

$sql = "SELECT * FROM Programmes";
$result = $conn->query($sql);

/* CHECK QUERY */

if(!$result){
    die("Database error: " . $conn->error);
}

/* CHECK DATA */

if($result->num_rows > 0){

while($row = $result->fetch_assoc()){
?>

<div class="programme-card">

<h3><?php echo htmlspecialchars($row['ProgrammeName']); ?></h3>

<p><?php echo htmlspecialchars($row['Description']); ?></p>

<a href="programme-details.php?id=<?php echo $row['ProgrammeID']; ?>" class="view-btn">
View Details
</a>

</div>

<?php
}

}else{

echo "<p>No programmes available.</p>";

}
?>