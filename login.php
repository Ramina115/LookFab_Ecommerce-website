<?php include 'includes/header.php'; ?>

<section class="auth-section">
    <div class="container">
        <div class="auth-card">
            <h2 class="section-title" style="margin-bottom: 18px;">Login to Your Account</h2>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert error">
                    <?php 
                    $errors = [
                        'invalid' => 'Invalid username or password.',
                        'empty' => 'Please fill in all fields.',
                        'nouser' => 'User not found.'
                    ];
                    echo $errors[$_GET['error']] ?? 'An error occurred.';
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert success">
                    <?php 
                    $successMessages = [
                        'registered' => 'Registration successful! Please login.',
                        'logout' => 'You have been logged out successfully.'
                    ];
                    echo $successMessages[$_GET['success']] ?? 'Success!';
                    ?>
                </div>
            <?php endif; ?>
            
            <form action="php/login.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-options" style="display:flex;justify-content:space-between;align-items:center;">
                    <label>
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="forgot-password.php">Forgot password?</a>
                </div>
                <button type="submit" class="btn" style="width:100%;margin-top:12px;">Login</button>
            </form>
            
            <div class="auth-switch" style="text-align:center;margin-top:18px;">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>