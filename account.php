<?php 
session_start();
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'config/database.php';
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
?>

<section class="account-section">
    <div class="container">
        <h2 class="section-title">My Account</h2>
        <div class="account-flex">
            <div class="account-sidebar">
                <div class="account-user">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-info">
                        <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>
                <ul class="account-menu">
                    <li class="active"><a href="account.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                    <li><a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a></li>
                    <li><a href="addresses.php"><i class="fas fa-map-marker-alt"></i> Addresses</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            <div class="account-main">
                <div class="account-section-card">
                    <h3>Profile Information</h3>
                    <form action="php/update_profile.php" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                        <button type="submit" class="btn">Update Profile</button>
                    </form>
                </div>
                <div class="account-section-card">
                    <h3>Change Password</h3>
                    <form action="php/change_password.php" method="POST">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>