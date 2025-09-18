<!DOCTYPE html>
<html lang="en">
<!-- Header -->

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PLMUN Portal</title>
  <?php
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
  $role = strtolower(str_replace(' ', '', $_SESSION['role'] ?? ''));
  ?>
  <link rel="stylesheet" href="../../assets/css/style.css" />
  <?php if ($role === 'superadmin'): ?>
    <link rel="stylesheet" href="../../assets/css/superadmin.css" />
  <?php elseif ($role === 'student'): ?>
    <link rel="stylesheet" href="../../assets/css/student.css" />
  <?php elseif ($role === 'professor'): ?>
    <link rel="stylesheet" href="../../assets/css/professor.css" />
  <?php elseif ($role === 'misadmin'): ?>
    <link rel="stylesheet" href="../../assets/css/misadmin.css" />
  <?php endif; ?>

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