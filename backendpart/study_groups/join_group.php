<!DOCTYPE html>
<html>
<body>

<h2>Join Study Group</h2>

<?php
// Example groups (since you didn't learn DB yet)
$groups = array(
    "Math Group",
    "Programming Group",
    "Physics Group"
);
?>

<form method="POST">
    Your Name: <input type="text" name="student_name"><br><br>

    Select Group:
    <select name="group">
        <?php
        foreach ($groups as $g) {
            echo "<option value='$g'>$g</option>";
        }
        ?>
    </select><br><br>

    <input type="submit" value="Join Group">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $studentName = $_POST['student_name'];
    $group = $_POST['group'];

    if ($studentName != "") {
        echo "<h3>Successfully Joined!</h3>";
        echo "Student: " . $studentName . "<br>";
        echo "Group: " . $group . "<br>";
    } else {
        echo "<p style='color:red;'>Enter your name!</p>";
    }
}
?>

</body>
</html>
