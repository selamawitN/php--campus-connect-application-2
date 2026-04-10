<?php
include "db.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Materials</title>
</head>
<body>

<h2>Shared Materials</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Name</th>
    <th>ID</th>
    <th>Year</th>
    <th>Type</th>
    <th>File</th>
</tr>

<?php
$result = $conn->query("SELECT * FROM materials ORDER BY upload_date DESC");

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row['student_name']."</td>";
    echo "<td>".$row['student_id']."</td>";
    echo "<td>".$row['year_level']."</td>";
    echo "<td>".$row['material_type']."</td>";
    echo "<td><a href='uploads/".$row['file_name']."' download>Download</a></td>";
    echo "</tr>";
}
?>

</table>

<br>
<a href="index.php">Back</a>

</body>
</html>
