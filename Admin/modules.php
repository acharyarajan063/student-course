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
    $conn->query("INSERT INTO Modules (ModuleName) VALUES ('$name')");
    header('Location: modules.php');
    exit;
}

// handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM Modules WHERE ModuleID=$id");
    header('Location: modules.php');
    exit;
}

$modules = $conn->query('SELECT * FROM Modules');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Modules</title>
<link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="admin-container">
    <h1>Modules</h1>
    <a href="dashboard.php">← Back to dashboard</a>
    <h2>Add new module</h2>
    <form method="post" action="modules.php">
        <label>Name<br><input type="text" name="name" required></label><br>
        <button type="submit">Add</button>
    </form>

    <h2>Existing modules</h2>
    <table>
        <thead><tr><th>ID</th><th>Name</th><th>Action</th></tr></thead>
        <tbody>
        <?php while($row=$modules->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['ModuleID']; ?></td>
                <td><?php echo htmlspecialchars($row['ModuleName']); ?></td>
                <td><a href="modules.php?delete=<?php echo $row['ModuleID']; ?>" onclick="return confirm('Delete module?')">Delete</a></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
