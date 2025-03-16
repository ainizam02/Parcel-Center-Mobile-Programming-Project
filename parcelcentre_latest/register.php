<?php
require_once 'db.php'; // Include your database connection

$fullname = $role = $username = $email = $password = $confirmPassword = '';
$fullname_err = $role_err = $username_err = $email_err = $password_err = $confirm_password_err = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Full Name
    if (empty(trim($_POST["fullName"]))) {
        $fullname_err = "Please enter a full name.";
    } else {
        $fullname = trim($_POST["fullName"]);
    }

    // Validate Role
    if (empty(trim($_POST["role"]))) {
        $role_err = "Please select a role.";
    } else {
        $role = trim($_POST["role"]);
    }

    // Validate Username
    if (empty(trim($_POST["userName"]))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["userName"]);
    }

    // Validate Email
    if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter an email.";
    } else {
    $email = trim($_POST["email"]);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email.";
    } else {
        // Check if email already exists
        $sql = "SELECT user_id FROM users WHERE email = :email";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $param_email = $email;
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $email_err = "This email is already taken.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }
}

    // Validate Password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate Confirm Password
    if (empty(trim($_POST["confirmPassword"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirmPassword = trim($_POST["confirmPassword"]);
        if (empty($password_err) && ($password != $confirmPassword)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting into database
    if (empty($fullname_err) && empty($role_err) && empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO users (fullname, role, username, email, password_hash) VALUES (:fullname, :role, :username, :email, :password)";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":fullname", $param_fullname, PDO::PARAM_STR);
            $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);

            // Set parameters
            $param_fullname = $fullname;
            $param_role = $role;
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Registration successful
                echo '<script>
                          setTimeout(function() {
                              alert("Registered successfully");
                              window.location.href = "loginindex.php";
                          }, 2000);
                      </script>';
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        unset($stmt);
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
    <title>Register - SME Bank Parcel Centre</title>
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
        .form-section {
            background-color: #ffcba9; /* Soft orange background */
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
        .footer {
            background-color: #363535;
            padding: 20px 0;
            text-align: center;
            color: #FF6600;
            margin-top: 40px;
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
        <br>
        <br>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="form-section"> <!-- Updated class for soft orange background -->
                    <h2>Register</h2>
                    <form action="register.php" method="post">
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <input type="text" name="fullName" class="form-control <?php echo (!empty($fullname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $fullname; ?>">
                            <span class="invalid-feedback"><?php echo $fullname_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" class="form-control <?php echo (!empty($role_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Select Role</option>
                                <option value="Student" <?php echo ($role == 'Student') ? 'selected' : ''; ?>>Student</option>
                                <option value="Staff" <?php echo ($role == 'Staff') ? 'selected' : ''; ?>>Staff</option>
                            </select>
                            <span class="invalid-feedback"><?php echo $role_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="userName">Username</label>
                            <input type="text" name="userName" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                            <span class="invalid-feedback"><?php echo $username_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                            <span class="invalid-feedback"><?php echo $email_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                            <span class="invalid-feedback"><?php echo $password_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password</label>
                            <input type="password" name="confirmPassword" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirmPassword; ?>">
                            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Register">
                        </div>
                        <p>Already have an account? <a href="loginindex.php">Login here</a>.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer mt-5">
        <p>&copy; 2024 Parcel Centre. All Rights Reserved.</p>
        <p>Contact us at <a href="mailto:info@parcelcentre.com" class="link">info@parcelcentre.com</a></p>
    </footer>
    <!-- Bootstrap JS and jQuery (Optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

