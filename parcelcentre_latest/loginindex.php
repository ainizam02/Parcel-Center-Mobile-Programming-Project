<?php
session_start();
require_once 'db.php';

$username = $password = $role = '';
$username_err = $password_err = $role_err = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate Password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate Role
    $role = isset($_POST["role"]) ? trim($_POST["role"]) : '';
    if (empty($role)) {
        $role_err = "Please select your role.";
    } elseif (!in_array($role, ['Student', 'Staff'])) {
        $role_err = "Invalid role selected. Please select either Student or Staff.";
    }

    // Check input errors before proceeding to login
    if (empty($username_err) && empty($password_err) && empty($role_err)) {
        // Prepare a select statement
        $sql = "SELECT user_id, username, password_hash, role FROM users WHERE username = :username";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Check if username exists, then verify password
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $user_id = $row['user_id'];
                        $hashed_password = $row['password_hash'];
                        $db_role = $row['role'];

                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, check role
                            if ($db_role != $role) {
                                // Display an error message if role is incorrect
                                $role_err = "You do not have access with the selected role.";
                            } else {
                                // Start a new session
                                session_start();

                                // Store data in session variables
                                $_SESSION["user_id"] = $user_id;
                                $_SESSION["username"] = $username;
                                $_SESSION["role"] = $db_role;

                                // Redirect to dashboard or home page after login based on role
                                if ($db_role == 'Student') {
                                    header("Location: studentdashboard.php");
                                } elseif ($db_role == 'Staff') {
                                    header("Location: staffdashboard.php");
                                } else {
                                    // Handle other roles if needed
                                    header("Location: loginindex.php");
                                }
                                exit();
                            }
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "The password you entered is not valid.";
                        }
                    }
                } else {
                    // Display an error message if username does not exist
                    $username_err = "No account found with that username.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }

    // Close connection
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SME Bank Parcel Centre</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom Styles */
        body {
            background-color: #ffffff;
        }
        .navbar {
            background-color: #363535;
        }
        .navbar-brand,
        .navbar-nav .nav-link {
            color: #FF6600 !important; /* Bright font color */
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
        }
        .header {
            background-color: #FF6600;
            color: #FFFFFF;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5rem;
        }
        .login-section {
            background-color: #f8f8f8;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .footer {
            background-color: #363535;
            padding: 10px 0;
            text-align: center;
            margin-top: 40px;
            color: #FF6600;
        }
        .footer .footer-content {
            color: #FF6600;
        }
        .btn-primary {
            background-color: #FF6600;
            border: none;
        }
        .btn-primary:hover {
            background-color: #e65c00;
        }
        .link {
            color: #FF6600;
            text-decoration: none;
        }
        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="#" style="color: #F8F3EA;">SME Bank Parcel Centre</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="loginindex.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.html">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.html">Contact Us</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="header">
            <h1>Welcome to SME Bank Parcel Centre</h1>
            <p>Manage your parcels efficiently and effectively.</p>
        </div>
        <div class="mt-4">
            <p>Our Parcel Centre provides the best solutions for managing your deliveries. Whether you're sending or receiving parcels, our service is designed to ensure your packages are handled with care and precision. Explore our services or get in touch with us to learn more.</p>
            <a href="services.html" class="btn btn-primary btn-lg mt-3">Explore Services</a>
        </div>
        <div class="login-section mt-5">
            <h3>Login</h3>
            <form action="loginindex.php" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?php echo $username; ?>" required>
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" id="password" name="password" required>
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                <label for="role">Role</label>
                <select class="form-control <?php echo (!empty($role_err)) ? 'is-invalid' : ''; ?>" id="role" name="role" required>
                    <option value="" <?php echo (empty($role) || !in_array($role, ['Student', 'Staff'])) ? 'selected' : ''; ?>>Select Role</option>
                    <option value="Student" <?php echo ($role == 'Student') ? 'selected' : ''; ?>>Student</option>
                    <option value="Staff" <?php echo ($role == 'Staff') ? 'selected' : ''; ?>>Staff</option>
                </select>
                    <span class="invalid-feedback"><?php echo $role_err; ?></span>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            <div class="mt-3">
                <p>Need an account? <a href="register.php" class="link">Sign Up here</a></p>
            </div>
        </div>
    </div>
    <footer class="footer mt-5">
        <div class="footer-content">
            <p>&copy; 2024 Parcel Centre. All Rights Reserved.</p>
            <p>Contact us at <a href="mailto:info@parcelcentre.com" class="link">info@parcelcentre.com</a></p>
        </div>
    </footer>
    <!-- Bootstrap JS and jQuery (Optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
