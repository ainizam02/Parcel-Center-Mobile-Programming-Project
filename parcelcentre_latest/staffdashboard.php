<?php
require_once 'db.php'; // Include your database connection

// Function to sanitize input
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

session_start();

// Add new parcel if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_parcel'])) {
    // Sanitize inputs
    $user_id = sanitize($_POST['user_id']);
    $arrival_date = sanitize($_POST['arrival_date']);
    $tracking_number = sanitize($_POST['tracking_number']);
    $status = sanitize($_POST['status']);

    // Prepare statement to insert parcel
    $sql = "INSERT INTO parcels (user_id, arrival_date, tracking_number, status) VALUES (:user_id, :arrival_date, :tracking_number, :status)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':arrival_date', $arrival_date);
    $stmt->bindParam(':tracking_number', $tracking_number);
    $stmt->bindParam(':status', $status);

    // Execute statement
    if ($stmt->execute()) {
        echo "New parcel added successfully.";
    } else {
        echo "Oops! Something went wrong.";
    }
}

// Delete parcel if requested
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_delete_parcel'])) {
    $parcel_id = sanitize($_POST['parcel_id']);
    
    // Prepare statement
    $sql = "DELETE FROM parcels WHERE parcel_id = :parcel_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':parcel_id', $parcel_id, PDO::PARAM_INT);
    
    // Execute statement
    if ($stmt->execute()) {
        $_SESSION['delete_success'] = true; // Set session variable for success
        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to avoid resubmission
        exit();
    } else {
        echo "Oops! Something went wrong.";
    }
}

// Update parcel status if requested
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $parcel_id = sanitize($_POST['parcel_id']);
    $status = sanitize($_POST['status']);
    
    // Prepare statement
    $sql = "UPDATE parcels SET status = :status WHERE parcel_id = :parcel_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':parcel_id', $parcel_id, PDO::PARAM_INT);
    $stmt->bindParam(':status', $status);
    
    // Execute statement
    if ($stmt->execute()) {
        $_SESSION['update_success'] = true; // Set session variable for success
        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to avoid resubmission
        exit();
    } else {
        echo "Oops! Something went wrong.";
    }
}

// Query to fetch all parcels
$sql = "SELECT parcel_id, user_id, arrival_date, tracking_number, status FROM parcels";
$stmt = $pdo->query($sql);
$parcels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query to fetch all students
$sql = "SELECT user_id, fullname FROM users WHERE role = 'student'";
$stmt = $pdo->query($sql);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        /* Custom Styles */
        body {
            background-color: #fbfcfa;
        }
        .navbar {
            background-color: #363535;
        }
        .navbar-brand,
        .navbar-nav .nav-link {
            color: #FF6600 !important; /* Bright font color */
        }
        .container-dashboard {
            background-color: #ffcba9; 
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Box shadow with a slight blur */
        }
        .container-header {
            background-color: #ff6600;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .btn-orange {
            background-color: #FF6600;
            color: white;
            border: none;
        }
        .btn-orange:hover {
            background-color: #e65c00;
        }
        .btn-dark-grey {
            background-color: #666;
            color: white;
            border: none;
        }
        .btn-dark-grey:hover {
            background-color: #555;
        }
        .table th,
        .table td {
            vertical-align: middle;
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
    <br><br>
    <div class="container">
        <div class="container-header">
            <h1>Welcome to SME Bank Parcel Centre</h1>
            <p>Manage your parcels efficiently and effectively.</p>
        </div>
    </div>

    <div class="container">
        <div class="container-dashboard">
            <div class="row">
                <div class="col-md-6">
                    <h2>Staff Dashboard</h2>
                </div>
                <div class="col-md-6 text-right">
                    <form action="loginindex.php"> <!-- Replace 'index.html' with your main page URL -->
                        <button type="submit" class="btn btn-dark-grey">Logout</button>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <!-- Form to add new parcel -->
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="user_id">Student:</label>
                            <select name="user_id" class="form-control">
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo $student['user_id']; ?>"><?php echo $student['user_id'] . ' - ' . $student['fullname']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="arrival_date">Arrival Date:</label>
                            <input type="date" name="arrival_date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="tracking_number">Tracking Number:</label>
                            <input type="text" name="tracking_number" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select name="status" class="form-control">
                                <option value="Picked Up">Picked Up</option>
                                <option value="Not Picked Up">Not Picked Up</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-orange mb-3" name="add_parcel">Add Parcel</button>
                    </form>
                    
                    <!-- Table to display parcels -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Parcel ID</th>
                                <th>User ID</th>
                                <th>Arrival Date</th>
                                <th>Tracking Number</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($parcels as $parcel): ?>
                            <tr>
                                <td><?php echo $parcel['parcel_id']; ?></td>
                                <td><?php echo $parcel['user_id']; ?></td>
                                <td><?php echo $parcel['arrival_date']; ?></td>
                                <td><?php echo $parcel['tracking_number']; ?></td>
                                <td>
                                    <form action="" method="post">
                                        <input type="hidden" name="parcel_id" value="<?php echo $parcel['parcel_id']; ?>">
                                        <select name="status" class="form-control" onchange="this.form.submit()">
                                            <option value="Picked Up" <?php echo $parcel['status'] == 'Picked Up' ? 'selected' : ''; ?>>Picked Up</option>
                                            <option value="Not Picked Up" <?php echo $parcel['status'] == 'Not Picked Up' ? 'selected' : ''; ?>>Not Picked Up</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td>
                                    <!-- Form for delete action -->
                                    <form action="" method="post" onsubmit="return confirm('Do you want to proceed deleting this parcel?');">
                                        <input type="hidden" name="parcel_id" value="<?php echo $parcel['parcel_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" name="confirm_delete_parcel">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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

    <!-- Modal for status update -->
    <div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusUpdateModalLabel">Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Parcel status updated successfully.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            <?php if (isset($_SESSION['update_success'])): ?>
                $('#statusUpdateModal').modal('show');
                <?php unset($_SESSION['update_success']); // Clear the session variable ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>
