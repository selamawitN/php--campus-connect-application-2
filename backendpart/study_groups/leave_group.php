<!DOCTYPE html>
<html>
<body>

<h2>Leave Study Group</h2>

<form method="POST">
    Your Name: <input type="text" name="student_name"><br><br>

    Select Group:
    <select name="group">
        <option>Math Group</option>
        <option>Programming Group</option>
        <option>Physics Group</option>
    </select><br><br>

    <input type="submit" value="Leave Group">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $studentName = $_POST['student_name'];
    $group = $_POST['group'];

    if ($studentName != "") {
        echo "<h3>You have left the group!</h3>";
        echo "Student: " . $studentName . "<br>";
        echo "Group: " . $group . "<br>";
    } else {
        echo "<p style='color:red;'>Enter your name!</p>";
    }
}
?>

</body>
</html>
