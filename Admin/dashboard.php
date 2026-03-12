<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>

<link rel="stylesheet" href="../css/admin.css">

</head>

<body>

<div class="dashboard">

<!-- SIDEBAR -->

<aside class="sidebar">

<h2>Admin Panel</h2>

<ul>
<li><a href="dashboard.php">Dashboard</a></li>
<li><a href="students.php">Students</a></li>
<li><a href="programmes.php">Programmes</a></li>
<li><a href="modules.php">Modules</a></li>
<li><a href="export.php">Export Students</a></li>
<li><a href="logout.php" class="logout">Logout</a></li>
</ul>

</aside>

<!-- MAIN CONTENT -->

<div class="main-content">

<header>

<h1>Administrator Dashboard</h1>

</header>

<section class="cards">

<div class="card">
<h3>Students</h3>
<p>View registered students</p>
<a href="students.php">Manage</a>
</div>

<div class="card">
<h3>Programmes</h3>
<p>Add or update programmes</p>
<a href="programmes.php">Manage</a>
</div>

<div class="card">
<h3>Modules</h3>
<p>Manage modules</p>
<a href="modules.php">Manage</a>
</div>

<div class="card">
<h3>Export</h3>
<p>Download student data</p>
<a href="export.php">Export</a>
</div>

</section>

</div>

</div>

</body>
</html>