<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/db.php';

/* CSV HEADERS */

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename=interested_students.csv');

/* OPEN OUTPUT STREAM */

$output = fopen('php://output', 'w');

/* CSV HEADER ROW */

fputcsv($output, ['ID','Name','Email','Programme']);

/* FETCH DATA */

$query = "
SELECT 
InterestedStudents.InterestID,
InterestedStudents.StudentName,
InterestedStudents.Email,
Programmes.ProgrammeName
FROM InterestedStudents
LEFT JOIN Programmes
ON InterestedStudents.ProgrammeID = Programmes.ProgrammeID
";

$result = $conn->query($query);

/* EXPORT ROWS */

while ($row = $result->fetch_assoc()) {

    fputcsv($output, [
        $row['InterestID'],
        $row['StudentName'],
        $row['Email'],
        $row['ProgrammeName']
    ]);

}

fclose($output);
exit;
?>