<?php
require_once "../config/database.php";

$stmt = $pdo->query("SELECT * FROM programmes");
$programmes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Course Hub</title>
</head>
<body>
    <h1>Available Programmes</h1>

    <?php foreach ($programmes as $programme): ?>
        <div>
            <h2><?php echo htmlspecialchars($programme['ProgrammeName']); ?></h2>
            <p><?php echo htmlspecialchars($programme['Description']); ?></p>
            <a href="programme.php?id=<?php echo htmlspecialchars($programme['ProgrammeID']); ?>">View Details</a>
        </div>
    <?php endforeach; ?>

</body>
</html>