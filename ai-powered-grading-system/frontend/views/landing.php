<?php
include '../components/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<!-- Header -->

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PLMUN Portal</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <!-- PLMUN Logo -->
  <link
    rel="apple-touch-icon"
    sizes="180x180"
    href="../assets/images/apple-touch-icon.png" />
  <link
    rel="icon"
    type="image/png"
    sizes="32x32"
    href="../assets/images/favicon-32x32.png" />
  <link
    rel="icon"
    type="image/png"
    sizes="16x16"
    href="../assets/images/favicon-16x16.png" />
  <link rel="manifest" href="../assets/images/site.webmanifest" />
</head>
    <!-- Hero -->
    <section class="hero">
      <div class="hero-content">
        <h1>
          Getting Easier Transparent and Efficient with
          <span class="plmun2">PLMUN</span>
          <span class="portal">Portal</span>
        </h1>
        <p>
          An AI-powered secure grading platform designed for schools and
          universities.
        </p>
        <a href="login.php" class="btn">Get Started</a>
      </div>
    </section>

    <!-- Features -->
    <section id="features" class="features">
      <h2>Key Features</h2>
      <div class="feature-list">
        <div class="feature-item">
          <h3>🔒 Security</h3>
          <p>
            Encrypted access and role-based controls for academic integrity.
          </p>
        </div>
        <div class="feature-item">
          <h3>📊 Analytics</h3>
          <p>AI-driven insights for professors, admins, and students.</p>
        </div>
        <div class="feature-item">
          <h3>⚡ Efficiency</h3>
          <p>Streamlined workflows from grading to performance monitoring.</p>
        </div>
      </div>
    </section>
<?php include '../components/footer.php'; ?>