<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Post Internship</title>
</head>
<body>
    <h2>Post New Internship</h2>
    
    <div id="msg"></div>
    
    <form id="myForm">
        <p>Title*: <input type="text" name="title" required></p>
        <p>Company*: <input type="text" name="company" required></p>
        <p>Description: <textarea name="description"></textarea></p>
        <p>Location: <input type="text" name="location"></p>
        <p>Stipend: <input type="text" name="stipend"></p>
        <p>Duration: <input type="text" name="duration"></p>
        <p>Deadline*: <input type="date" name="deadline" required></p>
        <p>Requirements: <textarea name="requirements"></textarea></p>
        <p>Year: 
            <select name="year_requirement">
                <option value="">Any</option>
                <option value="1st">1st</option>
                <option value="2nd">2nd</option>
                <option value="3rd">3rd</option>
                <option value="4th">4th</option>
            </select>
        </p>
        <p>Work Type: 
            <select name="work_type">
                <option value="Remote">Remote</option>
                <option value="On-site">On-site</option>
                <option value="Hybrid">Hybrid</option>
            </select>
        </p>
        <p>
            <button type="submit">Post</button>
            <a href="my_internships.php">Cancel</a>
        </p>
    </form>
    
    <script>
    document.getElementById('myForm').onsubmit = async function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let res = await fetch('post_internship.php', { method: 'POST', body: formData });
        let data = await res.json();
        if (data.status == 'success') {
            document.getElementById('msg').innerHTML = '<p style="color:green">Posted! Redirecting...</p>';
            setTimeout(() => location.href = 'my_internships.php', 1000);
        } else {
            document.getElementById('msg').innerHTML = '<p style="color:red">Error: ' + data.message + '</p>';
        }
    }
    </script>
</body>
</html>
