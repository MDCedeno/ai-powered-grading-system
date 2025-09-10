<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | PLMUN Portal</title>
    <link rel="stylesheet" href="../assets/css/auth.css" />
    <!-- PLMUN Logo -->
    <link
      rel="apple-touch-icon"
      sizes="180x180"
      href="../assets/images/apple-touch-icon.png"
    />
    <link
      rel="icon"
      type="image/png"
      sizes="32x32"
      href="../assets/images/favicon-32x32.png"
    />
    <link
      rel="icon"
      type="image/png"
      sizes="16x16"
      href="../assets/images/favicon-16x16.png"
    />
    <link rel="manifest" href="../assets/images/site.webmanifest" />
  </head>
  <body>
    <div class="auth-container">
      <div class="auth-card">
        <h1>
          <span class="plmun">PLMUN</span> <span class="portal">Portal</span>
        </h1>
        <p class="subtitle">Login to continue</p>

        <?php
        session_start();
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<p style="color: green;">' . $_SESSION['success'] . '</p>';
            unset($_SESSION['success']);
        }
        ?>

        <form method="POST" action="../../backend/controllers/authController.php?action=login">
          <div class="form-group">
            <label for="email">Email Address</label>
            <input
              type="email"
              name="email"
              id="email"
              placeholder="you@example.com"
              required
            />
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input
              type="password"
              name="password"
              id="password"
              placeholder="********"
              required
            />
          </div>

          <button type="submit" class="btn-primary">Login</button>
          <p class="switch">
            Don’t have an account? <a href="signup.php">Sign Up</a>
          </p>
        </form>
      </div>
    </div>
  </body>
</html>
