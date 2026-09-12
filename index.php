<?php include('allhead.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Student Management System</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>

<link rel="stylesheet" href="css/style.css">

</head>

<body>

<!-- Hero -->

<section class="hero">

<div class="container">

<div class="row align-items-center">

<div class="col-lg-7">

<h1>Student Management System</h1>

<p class="mt-3">

A modern web-based platform for managing students, teachers, attendance, results, courses, and academic records efficiently.

</p>

<a href="#login" class="btn btn-light btn-lg mt-3">

Get Started

</a>

</div>

<div class="col-lg-5 text-center">

<img src="img/student.png" class="img-fluid" style="max-height:350px;">

</div>

</div>

</div>

</section>

<!-- Login -->

<section class="py-5" id="login">

<div class="container">

<div class="text-center mb-5">

<h2 class="fw-bold">

Login Portal

</h2>

<p class="text-muted">

Choose your role to access the system.

</p>

</div>

<div class="row g-4">

<!-- Admin -->

<div class="col-md-4">

<div class="card login-card p-4 text-center">

<div class="login-icon admin">

<i class="fas fa-user-shield"></i>

</div>

<h3 class="mt-4">

Administrator

</h3>

<p class="text-muted">

Manage students, teachers, courses, reports and system settings.

</p>

<a href="admin/adminLogin.php" class="btn btn-primary">

<i class="fas fa-right-to-bracket"></i>

Admin Login

</a>

</div>

</div>

<!-- Teacher -->

<div class="col-md-4">

<div class="card login-card p-4 text-center">

<div class="login-icon teacher">

<i class="fas fa-chalkboard-user"></i>

</div>

<h3 class="mt-4">

Teacher

</h3>

<p class="text-muted">

Manage attendance, marks, student progress and classroom activities.

</p>

<a href="teacherlogin.php" class="btn btn-success">

<i class="fas fa-right-to-bracket"></i>

Teacher Login

</a>

</div>

</div>

<!-- Student -->

<div class="col-md-4">

<div class="card login-card p-4 text-center">

<div class="login-icon student">

<i class="fas fa-user-graduate"></i>

</div>

<h3 class="mt-4">

User

</h3>

<p class="text-muted">

View profile, attendance, grades, timetable and announcements.

</p>

<a href="userlogin.php" class="btn btn-warning text-white">

<i class="fas fa-right-to-bracket"></i>

User Login

</a>

</div>

</div>

</div>

</div>

</section>

<!-- Features -->

<section class="pb-5" id="features">

<div class="container">

<div class="text-center mb-5">

<h2 class="fw-bold">

System Features

</h2>

</div>

<div class="row g-4">

<div class="col-md-3">

<div class="feature-box text-center">

<i class="fas fa-user-graduate fa-3x text-primary mb-3"></i>

<h5>

Student Records

</h5>

<p>

Manage student information efficiently.

</p>

</div>

</div>

<div class="col-md-3">

<div class="feature-box text-center">

<i class="fas fa-book fa-3x text-success mb-3"></i>

<h5>

Course Management

</h5>

<p>

Organize subjects and academic courses.

</p>

</div>

</div>

<div class="col-md-3">

<div class="feature-box text-center">

<i class="fas fa-chart-line fa-3x text-warning mb-3"></i>

<h5>

Performance

</h5>

<p>

Track marks and academic performance.

</p>

</div>

</div>

<div class="col-md-3">

<div class="feature-box text-center">

<i class="fas fa-calendar-check fa-3x text-danger mb-3"></i>

<h5>

Attendance

</h5>

<p>

Monitor daily attendance records.

</p>

</div>

</div>

</div>

</div>

</section>

<footer class="text-center">

<div class="container">

© <?php echo date('Y'); ?> Student Management System

<br>

Developed using PHP, MySQL & Bootstrap 5

</div>

</footer>



</body>

</html>

<?php include('allfoot.php'); ?>