<?php include 'includes/header.php'; ?>

<section class="auth-section">
    <div class="container">
        <div class="auth-card">
            <h2 class="section-title" style="margin-bottom: 18px;">Create an Account</h2>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert error">
                    <?php 
                    $errors = [
                        'username' => 'Username already taken.',
                        'email' => 'Email already registered.',
                        'password' => 'Passwords do not match.',
                        'empty' => 'Please fill in all fields.',
                        'pwlength' => 'Password must be at least 4 characters.',
                        'pwspecial' => 'Password must contain at least one special character.',
                        'emptyusername' => 'Username cannot be empty.'
                    ];
                    echo $errors[$_GET['error']] ?? 'An error occurred.';
                    ?>
                </div>
            <?php endif; ?>
            
            <form action="php/register.php" method="POST" class="auth-form" id="registerForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <div id="pw-helper" style="font-size:13px;color:#607d8b;margin-top:4px;display:none;">Password must be at least 4 characters and contain at least one special character.</div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div id="register-error" style="color:#b71c1c; margin-bottom:10px; display:none;"></div>
                <button type="submit" class="btn" style="width:100%;margin-top:12px;">Register</button>
            </form>
            
            <div class="auth-switch" style="text-align:center;margin-top:18px;">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    var username = document.getElementById('username').value.trim();
    var password = document.getElementById('password').value;
    var errorDiv = document.getElementById('register-error');
    var specialChar = /[^A-Za-z0-9]/;
    var pwHelper = document.getElementById('pw-helper');
    errorDiv.style.display = 'none';
    errorDiv.textContent = '';
    pwHelper.style.display = 'none';
    if (username === '') {
        errorDiv.textContent = 'Username cannot be empty.';
        errorDiv.style.display = 'block';
        e.preventDefault();
        return;
    }
    if (password.length < 4 || !specialChar.test(password)) {
        pwHelper.style.display = 'block';
        errorDiv.textContent = '';
        e.preventDefault();
        return;
    }
});

document.getElementById('password').addEventListener('input', function() {
    var password = this.value;
    var specialChar = /[^A-Za-z0-9]/;
    var pwHelper = document.getElementById('pw-helper');
    if (password.length > 0 && (password.length < 4 || !specialChar.test(password))) {
        pwHelper.style.display = 'block';
    } else {
        pwHelper.style.display = 'none';
    }
});
</script>

<?php include 'includes/footer.php'; ?>