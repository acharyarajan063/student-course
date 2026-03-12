<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once '../config/db.php';

// handle create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $level = $conn->real_escape_string($_POST['level']);
    $desc = $conn->real_escape_string($_POST['description']);
    $conn->query("INSERT INTO Programmes (ProgrammeName, ProgrammeLevel, Description) VALUES ('$name','$level','$desc')");
    header('Location: programmes.php');
    exit;
}

// handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM Programmes WHERE ProgrammeID=$id");
    header('Location: programmes.php');
    exit;
}

$programmes = $conn->query('SELECT * FROM Programmes');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Programmes</title>
<link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="admin-container">
    <h1>Programmes</h1>
    <a href="dashboard.php">← Back to dashboard</a>
    <h2>Add new programme</h2>
    <form method="post" action="programmes.php">
        <label>Name<br><input type="text" name="name" required></label><br>
        <label>Level<br>
            <select name="level">
                <option value="Undergraduate">Undergraduate</option>
                <option value="Postgraduate">Postgraduate</option>
            </select>
        </label><br>
        <label>Description<br><textarea name="description"></textarea></label><br>
        <button type="submit">Add</button>
    </form>

    <h2>Existing programmes</h2>
    <table>
        <thead><tr><th>ID</th><th>Name</th><th>Level</th><th>Action</th></tr></thead>
        <tbody>
        <?php while($row=$programmes->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['ProgrammeID']; ?></td>
                <td><?php echo htmlspecialchars($row['ProgrammeName']); ?></td>
                <td><?php echo htmlspecialchars($row['ProgrammeLevel']); ?></td>
                <td><a href="programmes.php?delete=<?php echo $row['ProgrammeID']; ?>" onclick="return confirm('Delete programme?')">Delete</a></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
