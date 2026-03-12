<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/db.php';

/* DELETE STUDENT */

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM InterestedStudents WHERE InterestID=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: students.php");
    exit;
}

/* FETCH STUDENTS */

$query = "
SELECT 
InterestedStudents.InterestID,
InterestedStudents.StudentName,
InterestedStudents.Email,
Programmes.ProgrammeName
FROM InterestedStudents
JOIN Programmes
ON InterestedStudents.ProgrammeID = Programmes.ProgrammeID
ORDER BY InterestedStudents.InterestID DESC
";

$students = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Students</title>

<link rel="stylesheet" href="../css/admin.css">

<style>

/* PAGE HEADER */

.page-header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;
}

/* SEARCH */

.search-box{
padding:8px 12px;
border-radius:6px;
border:1px solid #ccc;
}

/* TABLE */

.table-container{
overflow-x:auto;
}

table{
width:100%;
border-collapse:collapse;
background:white;
box-shadow:0 4px 12px rgba(0,0,0,0.08);
border-radius:8px;
overflow:hidden;
}

th,td{
padding:12px;
text-align:left;
border-bottom:1px solid #eee;
}

th{
background:#1e293b;
color:white;
}

tr:hover{
background:#f9fafb;
}

/* DELETE BUTTON */

.delete-btn{
background:#ef4444;
color:white;
padding:6px 12px;
border-radius:6px;
text-decoration:none;
font-size:14px;
}

.delete-btn:hover{
background:#dc2626;
}

/* BACK BUTTON */

.back-btn{
background:#2563eb;
color:white;
padding:8px 14px;
border-radius:6px;
text-decoration:none;
}

.back-btn:hover{
background:#1d4ed8;
}

</style>

<script>

/* SEARCH FUNCTION */

function searchStudents(){

let input = document.getElementById("search").value.toLowerCase();
let rows = document.querySelectorAll("tbody tr");

rows.forEach(row => {

let text = row.innerText.toLowerCase();

row.style.display = text.includes(input) ? "" : "none";

});

}

</script>

</head>

<body>

<div class="admin-container">

<div class="page-header">

<h1>Interested Students</h1>

<a href="dashboard.php" class="back-btn">Back to Dashboard</a>

</div>

<input 
type="text"
id="search"
placeholder="Search students..."
class="search-box"
onkeyup="searchStudents()"
>

<br><br>

<div class="table-container">

<table>

<thead>

<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Programme</th>
<th>Action</th>
</tr>

</thead>

<tbody>

<?php while ($row = $students->fetch_assoc()): ?>

<tr>

<td><?= $row['InterestID'] ?></td>

<td><?= htmlspecialchars($row['StudentName']) ?></td>

<td><?= htmlspecialchars($row['Email']) ?></td>

<td><?= htmlspecialchars($row['ProgrammeName']) ?></td>

<td>

<a 
class="delete-btn"
href="students.php?delete=<?= $row['InterestID'] ?>"
onclick="return confirm('Delete this student?')"
>
Delete
</a>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</div>

</body>
</html>