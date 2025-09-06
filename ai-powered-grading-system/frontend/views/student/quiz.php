<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PLMUN Portal</title>
    <link rel="stylesheet" href="../../assets/css/student.css" />
    <!-- PLMUN Logo -->
    <link
        rel="apple-touch-icon"
        sizes="180x180"
        href="../../assets/images/apple-touch-icon.png" />
    <link
        rel="icon"
        type="image/png"
        sizes="32x32"
        href="../../assets/images/favicon-32x32.png" />
    <link
        rel="icon"
        type="image/png"
        sizes="16x16"
        href="../../assets/images/favicon-16x16.png" />
    <link rel="manifest" href="../../assets/images/site.webmanifest" />
</head>

<body>
    <div class="quiz-container">
        <header class="quiz-header">
            <h1>Midterm Review Quiz - IT 101</h1>
            <div class="user-profile">
                <span><?php echo $_SESSION['user_name'] ?? 'Student'; ?></span>
                <img src="../../assets/images/Thug.jpg" alt="User Avatar" />
            </div>
        </header>

        <form class="quiz-form">
            <!-- Question 1 -->
            <div class="quiz-question">
                <h3>1. What does HTML stand for?</h3>
                <label><input type="radio" name="q1" /> Hyper Text Markup Language</label>
                <label><input type="radio" name="q1" /> HighText Machine Language</label>
                <label><input type="radio" name="q1" /> Hyperlinks and Text Markup Language</label>
            </div>

            <!-- Question 2 -->
            <div class="quiz-question">
                <h3>2. Which of the following is a programming language?</h3>
                <label><input type="checkbox" name="q2[]" /> Python</label>
                <label><input type="checkbox" name="q2[]" /> CSS</label>
                <label><input type="checkbox" name="q2[]" /> Java</label>
            </div>

            <!-- Submit -->
            <div class="quiz-actions">
                <button type="submit" class="btn-primary">Submit Quiz</button>
            </div>
        </form>
    </div>
</body>

</html>