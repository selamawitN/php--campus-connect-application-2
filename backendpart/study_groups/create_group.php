<!DOCTYPE html>
<html>
<body>

<h2>Create Study Group</h2>

<?php
// error variables
$nameErr = $subjectErr = $maxErr = "";
$groupName = $subject = $maxMembers = "";

// clean input function (from slides)
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Group Name
    if (empty($_POST["group_name"])) {
        $nameErr = "Group name is required";
    } else {
        $groupName = test_input($_POST["group_name"]);

        // only letters and space
        if (!preg_match("/^[a-zA-Z ]*$/", $groupName)) {
            $nameErr = "Only letters allowed";
        }
    }

    // Subject
    if (empty($_POST["subject"])) {
        $subjectErr = "Subject is required";
    } else {
        $subject = test_input($_POST["subject"]);
    }

    // Max Members
    if (empty($_POST["max_members"])) {
        $maxErr = "Required";
    } else {
        $maxMembers = test_input($_POST["max_members"]);
    }

    // if no errors
    if ($nameErr == "" && $subjectErr == "" && $maxErr == "") {
        echo "<h3>Study Group Created Successfully!</h3>";
        echo "Group: $groupName <br>";
        echo "Subject: $subject <br>";
        echo "Max Members: $maxMembers <br>";
    }
}
?>

<!-- FORM -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

Group Name:
<input type="text" name="group_name" value="<?php echo $groupName;?>">
<span style="color:red;">* <?php echo $nameErr;?></span>
<br><br>

Subject:
<input type="text" name="subject" value="<?php echo $subject;?>">
<span style="color:red;">* <?php echo $subjectErr;?></span>
<br><br>

Max Members:
<input type="number" name="max_members" value="<?php echo $maxMembers;?>">
<span style="color:red;">* <?php echo $maxErr;?></span>
<br><br>

<input type="submit" value="Create Group">

</form>

</body>
</html>
