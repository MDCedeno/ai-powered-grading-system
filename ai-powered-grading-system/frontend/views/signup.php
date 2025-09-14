<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up | PLMUN Portal</title>
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
        <p class="subtitle">Create your account</p>

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

        <form id="signupForm">
          <div class="form-group">
            <label for="name">Full Name</label>
            <input
              type="text"
              name="name"
              id="name"
              placeholder="Juan Dela Cruz"
              required
            />
          </div>

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
            <label for="role">Role</label>
            <select name="role" id="role" required>
              <option value="">Select Role</option>
              <option value="student">Student</option>
              <option value="professor">Professor</option>
            </select>
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

          <button type="submit" class="btn-primary">Sign Up</button>
          <p class="switch">
            Already have an account? <a href="login.php">Login</a>
          </p>
        </form>

        <script>
          document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const role = document.getElementById('role').value;
            const password = document.getElementById('password').value;

            fetch('../../backend/router.php/api/auth/register', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({ name, email, password, role })
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                alert('Account created successfully! Please login.');
                window.location.href = 'login.php';
              } else {
                alert(data.message || 'Signup failed');
              }
            })
            .catch(error => {
              console.error('Error:', error);
              alert('An error occurred. Please try again.');
            });
          });
        </script>
      </div>
    </div>
  </body>
</html>
