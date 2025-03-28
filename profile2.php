<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nv_errands";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
        }
        .sidebar {
            width: 80px;
            background-color: #fff;
            height: 100vh;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }
        .sidebar i {
            font-size: 24px;
            margin: 20px 0;
            color: #b0b0b0;
            cursor: pointer;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header .welcome {
            font-size: 24px;
            font-weight: bold;
        }
        .header .date {
            color: #b0b0b0;
        }
        .header .search {
            display: flex;
            align-items: center;
        }
        .header .search input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            margin-right: 10px;
        }
        .header .search i {
            color: #b0b0b0;
        }
        .header .profile {
            display: flex;
            align-items: center;
        }
        .header .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-left: 10px;
        }
        .profile-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .profile-card .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .profile-card .header .title {
            font-size: 18px;
            font-weight: bold;
        }
        .profile-card .header .edit-btn {
            background-color: #00c4ff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
        }
        .profile-card .profile-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
        }
        .profile-card .profile-info img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 20px;
            cursor: pointer;
        }
        .profile-card .profile-info .info {
            font-size: 16px;
        }
        .profile-card .profile-info .info .name {
            font-weight: bold;
        }
        .profile-card .profile-info .info .email {
            color: #b0b0b0;
        }
        .profile-card .form {
            display: flex;
            flex-wrap: wrap;
        }
        .profile-card .form .form-group {
            width: 48%;
            margin-right: 4%;
            margin-bottom: 20px;
        }
        .profile-card .form .form-group:nth-child(2n) {
            margin-right: 0;
        }
        .profile-card .form .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #b0b0b0;
        }
        .profile-card .form .form-group input,
        .profile-card .form .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .profile-card .email-section {
            margin-top: 20px;
        }
        .profile-card .email-section .email-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .profile-card .email-section .email-item i {
            font-size: 24px;
            color: #00c4ff;
            margin-right: 10px;
        }
        .profile-card .email-section .email-item .email-info {
            font-size: 16px;
        }
        .profile-card .email-section .email-item .email-info .email {
            font-weight: bold;
        }
        .profile-card .email-section .email-item .email-info .date {
            color: #b0b0b0;
        }
        .profile-card .email-section .add-email-btn {
            background-color: #00c4ff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            display: inline-block;
            margin-top: 10px;
        }
        .profile-card .profile-info input[type="file"] {
            display: none;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                flex-direction: row;
                justify-content: space-around;
                padding: 10px 0;
            }
            .main-content {
                padding: 10px;
            }
            .profile-card .form .form-group {
                width: 100%;
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <i class="fas fa-home" onclick="goHome()"></i>
            <i class="fas fa-th-large"></i>
            <i class="fas fa-user"></i>
            <i class="fas fa-bell"></i>
            <i class="fas fa-cog"></i>
        </div>
        <div class="main-content">
            <div class="header">
                <div>
                    <div class="welcome">
                        Welcome, <?php echo htmlspecialchars($user['email']); ?>
                    </div>
                    <div class="date">
                        <?php echo date('D, d M Y'); ?>
                    </div>
                </div>
                <div class="search">
                    <input placeholder="Search" type="text"/>
                    <i class="fas fa-search"></i>
                </div>
                <div class="profile">
                    <i class="fas fa-bell"></i>
                    <img alt="User profile picture" height="40" src="https://storage.googleapis.com/a1aa/image/lrHJxGwLAeUQdieTHmvCC0FrU3zeqim5hgTkhLXbfMtRhTlOB.jpg" width="40"/>
                </div>
            </div>
            <div class="profile-card">
                <div class="header">
                    <div class="title">
                        <?php echo htmlspecialchars($user['email']); ?>
                    </div>
                    <button class="edit-btn">
                        Edit
                    </button>
                </div>
                <div class="profile-info">
                    <label for="profile-pic">
                        <img alt="User profile picture" height="60" id="profile-pic-img" src="https://storage.googleapis.com/a1aa/image/KiYfEfQBiEvkskLZyAnISKEjcle6QpuKv6unJgGsmi2kwpSnA.jpg" width="60"/>
                    </label>
                    <input id="profile-pic" onchange="updateProfilePic(event)" type="file"/>
                    <div class="info">
                        <div class="name">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </div>
                        <div class="email">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </div>
                    </div>
                </div>
                <div class="form">
                    <div class="form-group">
                        <label for="full-name">Full Name</label>
                        <input id="full-name" placeholder="Your Full Name" type="text" value="<?php echo htmlspecialchars($user['email']); ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input id="location" placeholder="Your location" type="text" value="<?php echo htmlspecialchars($user['location']); ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender">
                            <option><?php echo htmlspecialchars($user['gender']); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" placeholder="Your Password" type="password"/>
                    </div>
                    <div class="form-group">
                        <label for="phone-number">Phone number</label>
                        <input id="phone-number" placeholder="Your Phone Number" type="text" value="<?php echo htmlspecialchars($user['phone']); ?>"/>
                    </div>
                </div>
                <div class="email-section">
                    <div class="email-item">
                        <i class="fas fa-envelope"></i>
                        <div class="email-info">
                            <div class="email">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </div>
                            <div class="date">
                                1 month ago
                            </div>
                        </div>
                    </div>
                    <button class="add-email-btn" onclick="addEmail()">
                        +Add Email Address
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function addEmail() {
            const emailSection = document.querySelector('.email-section');
            const newEmailItem = document.createElement('div');
            newEmailItem.classList.add('email-item');
            newEmailItem.innerHTML = `
                <i class="fas fa-envelope"></i>
                <div class="email-info">
                    <div class="email">newemail@example.com</div>
                    <div class="date">just now</div>
                </div>
            `;
            emailSection.insertBefore(newEmailItem, emailSection.querySelector('.add-email-btn'));
        }

        function goHome() {
            alert('Home button clicked!');
        }

        function updateProfilePic(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const img = document.getElementById('profile-pic-img');
                img.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>