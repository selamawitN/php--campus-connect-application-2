<!DOCTYPE html>
<html>
<body>

<h2>Leave Study Group</h2>

<?php
$nameErr = "";
$studentName = "";
$selectedGroup = "";

// function (from slides)
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$groups = array("Math Group", "Programming Group", "Physics Group");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["student_name"])) {
        $nameErr = "Name is required";
    } else {
        $studentName = test_input($_POST["student_name"]);

        // only letters and spaces
        if (!preg_match("/^[a-zA-Z ]*$/", $studentName)) {
            $nameErr = "Only letters allowed";
        }
    }

    $selectedGroup = test_input($_POST["group"]);

    if ($nameErr == "") {
        echo "<h3>You have successfully left the group!</h3>";
        echo "Name: $studentName <br>";
        echo "Group: $selectedGroup <br>";
    }
}
?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

Your Name:
<input type="text" name="student_name" value="<?php echo $studentName;?>">
<span style="color:red;">* <?php echo $nameErr;?></span>
<br><br>

Select Group:
<select name="group">
<?php
foreach ($groups as $g) {
    if ($g == $selectedGroup) {
        echo "<option selected>$g</option>";
    } else {
        echo "<option>$g</option>";
    }
}
?>
</select>

<br><br>

<input type="submit" value="Leave Group">

</form>

</body>
</html>
