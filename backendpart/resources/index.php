<!DOCTYPE html>
<html>
<head>
    <title>Material Sharing System</title>
</head>
<body>

<h2>Upload Study Material</h2>

<form action="upload.php" method="POST" enctype="multipart/form-data">

    Name: <br>
    <input type="text" name="name" required><br><br>

    Student ID: <br>
    <input type="text" name="student_id" required><br><br>

    Year: <br>
    <select name="year" required>
        <option value="">Select Year</option>
        <option value="Year 2">Year 2</option>
        <option value="Year 3">Year 3</option>
        <option value="Year 4">Year 4</option>
        <option value="Year 5">Year 5</option>
    </select><br><br>

    Material Type: <br>
    <select name="type" required>
        <option value="">Select Type</option>
        <option value="Mid">Mid</option>
        <option value="Assignment">Assignment</option>
        <option value="Lab Report">Lab Report</option>
        <option value="Final Exam">Final Exam</option>
    </select><br><br>

    Upload File: <br>
    <input type="file" name="file" required><br><br>

    <button type="submit">Upload</button>

</form>

<br>
<a href="view.php">View Materials</a>

</body>
</html>
