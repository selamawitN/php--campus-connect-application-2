<!DOCTYPE html>
<html>
<body>

<h2>Create Study Group</h2>

<form method="POST">
    Group Name: <input type="text" name="group_name"><br><br>
    Subject: <input type="text" name="subject"><br><br>
    Max Members: <input type="number" name="max_members"><br><br>
    <input type="submit" value="Create Group">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $groupName = $_POST['group_name'];
    $subject = $_POST['subject'];
    $maxMembers = $_POST['max_members'];

    if ($groupName != "" && $subject != "" && $maxMembers != "") {
        echo "<h3>Study Group Created!</h3>";
        echo "Group Name: " . $groupName . "<br>";
        echo "Subject: " . $subject . "<br>";
        echo "Max Members: " . $maxMembers . "<br>";
    } else {
        echo "<p style='color:red;'>Please fill all fields!</p>";
    }
}
?>

</body>
</html>
