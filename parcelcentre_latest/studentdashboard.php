<?php
require_once 'db.php'; // Include your database connection

// Function to sanitize input
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Start session (if not already started)
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or handle unauthorized access
    header("Location: loginindex.php");
    exit;
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Initialize the query and parameters
$sql = "SELECT parcel_id, arrival_date, tracking_number, status FROM parcels WHERE user_id = :user_id";
$params = [':user_id' => $user_id];

// Check if search is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_parcel'])) {
    $tracking_number = sanitize($_POST['tracking_number']);
    if (!empty($tracking_number)) {
        $sql .= " AND tracking_number = :tracking_number";
        $params[':tracking_number'] = $tracking_number;
    }
}

// Display all parcels
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['display_all'])) {
    // No additional filter needed for all parcels owned by the user
}

// Prepare and execute the query
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$parcels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
            color: #FF6600 !important;
        }
        .container-dashboard {
            background-color: #ffcba9;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
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
    <div class="container-header">
        <h1>Welcome to SME Bank Parcel Centre</h1>
        <p>Manage your parcels efficiently and effectively.</p>
    </div>
    <div class="container-dashboard">
        <h2>Student Dashboard</h2>
        <div class="row mb-4">
            <div class="col-md-8">
                <!-- Display All and Search Forms -->
                <form action="" method="post" class="form-inline mb-4">
                    <button type="submit" name="display_all" class="btn btn-primary mb-2 mr-2">Display All</button>
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="tracking_number" class="sr-only">Tracking Number</label>
                        <input type="text" name="tracking_number" class="form-control" placeholder="Enter Tracking Number">
                    </div>
                    <button type="submit" name="search_parcel" class="btn btn-orange mb-2">Search</button>
                </form>
                
                <!-- Notification for no parcel found -->
                <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_parcel']) && empty($parcels)): ?>
                    <div class="alert alert-warning" role="alert">
                        Oops! There is no parcel with the tracking number "<?php echo $tracking_number; ?>".
                    </div>
                <?php endif; ?>
                
                <!-- Table to display parcels -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Parcel ID</th>
                            <th>Arrival Date</th>
                            <th>Tracking Number</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($parcels as $parcel): ?>
                        <tr>
                            <td><?php echo $parcel['parcel_id']; ?></td>
                            <td><?php echo $parcel['arrival_date']; ?></td>
                            <td><?php echo $parcel['tracking_number']; ?></td>
                            <td><?php echo $parcel['status']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Logout button -->
                <form action="loginindex.php">
                    <button type="submit" class="btn btn-secondary mt-3 float-right">Logout</button>
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
