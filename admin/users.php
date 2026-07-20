<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}
require_once '../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - LookFab Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .admin-section-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(26,34,56,0.10);
            padding: 36px 32px 28px 32px;
            margin: 40px auto;
            max-width: 1100px;
        }
        .admin-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(26,34,56,0.10);
            overflow: hidden;
        }
        .admin-table th, .admin-table td {
            padding: 14px 10px;
            text-align: center;
        }
        .admin-table th {
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: 1.08em;
        }
        .admin-table tr {
            background: #f9f9fb;
        }
        .admin-table tr:not(:last-child) {
            border-bottom: 1px solid #eee;
        }
        .admin-action-btn {
            background: var(--secondary);
            color: var(--primary);
            border: none;
            border-radius: 8px;
            padding: 6px 16px;
            font-size: 1em;
            cursor: pointer;
            margin: 0 4px;
            transition: background 0.2s, color 0.2s;
        }
        .admin-action-btn.edit {
            background: #3a86ff;
            color: #fff;
        }
        .admin-action-btn.delete {
            background: #e74c3c;
            color: #fff;
        }
        .admin-action-btn:hover {
            opacity: 0.85;
        }
        .modal {
            display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.3); align-items: center; justify-content: center;
        }
        .modal-content {
            background: #fff; padding: 32px; border-radius: 16px; min-width: 320px; max-width: 90vw; box-shadow: 0 4px 24px rgba(26,34,56,0.10);
        }
        .modal-close { float: right; cursor: pointer; font-size: 1.3em; color: #e74c3c; }
        .modal-actions { margin-top: 18px; text-align: right; }
        @media (max-width: 900px) {
            .admin-section-card { padding: 18px 4px; }
            .admin-table th, .admin-table td { padding: 8px 2px; font-size: 0.98em; }
        }
        .admin-header {
            position: sticky;
            top: 0;
            z-index: 1001;
            background: var(--primary);
            color: #fff;
            padding: 24px 0 12px 0;
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 2em;
            letter-spacing: 2px;
        }
        .admin-nav {
            position: sticky;
            top: 64px; /* height of header */
            z-index: 1000;
            background: var(--primary);
            display: flex;
            justify-content: center;
            gap: 32px;
            padding: 12px 0;
        }
    </style>
</head>
<body>
    <div class="admin-header">LookFab Admin</div>
    <nav class="admin-nav">
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="manage_products.php"><i class="fas fa-box"></i> Products</a>
        <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="users.php" class="active"><i class="fas fa-users"></i> Users</a>
        <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
    <section class="admin-section-card">
        <h2 style="margin-bottom: 24px; color: var(--primary); font-family: 'Playfair Display', serif;">Manage Users</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
                while ($user = mysqli_fetch_assoc($result)): ?>
                    <tr data-user-id="<?= $user['id'] ?>">
                        <td><?= $user['id'] ?></td>
                        <td class="user-username"><?= htmlspecialchars($user['username']) ?></td>
                        <td class="user-email"><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['created_at'] ?? '-') ?></td>
                        <td>
                            <button class="admin-action-btn edit" onclick="openEditUserModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>', '<?= htmlspecialchars($user['email']) ?>')"><i class="fas fa-edit"></i> Edit</button>
                            <button class="admin-action-btn delete" onclick="deleteUser(<?= $user['id'] ?>)"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
    <div class="modal" id="editUserModal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeEditUserModal()">&times;</span>
            <h3>Edit User</h3>
            <form id="editUserForm">
                <input type="hidden" name="user_id" id="modalUserId">
                <label for="modalUsername">Username:</label>
                <input type="text" name="username" id="modalUsername" required>
                <label for="modalEmail">Email:</label>
                <input type="email" name="email" id="modalEmail" required>
                <div class="modal-actions">
                    <button type="submit" class="admin-action-btn edit">Save</button>
                </div>
            </form>
        </div>
    </div>
    <script>
    function openEditUserModal(userId, username, email) {
        document.getElementById('editUserModal').style.display = 'flex';
        document.getElementById('modalUserId').value = userId;
        document.getElementById('modalUsername').value = username;
        document.getElementById('modalEmail').value = email;
    }
    function closeEditUserModal() {
        document.getElementById('editUserModal').style.display = 'none';
    }
    document.getElementById('editUserForm').onsubmit = function(e) {
        e.preventDefault();
        var userId = document.getElementById('modalUserId').value;
        var username = document.getElementById('modalUsername').value;
        var email = document.getElementById('modalEmail').value;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_user.php');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200 && JSON.parse(xhr.responseText).success) {
                var row = document.querySelector('tr[data-user-id="'+userId+'"]');
                row.querySelector('.user-username').textContent = username;
                row.querySelector('.user-email').textContent = email;
                closeEditUserModal();
            } else {
                alert('Failed to update user.');
            }
        };
        xhr.send('user_id='+userId+'&username='+encodeURIComponent(username)+'&email='+encodeURIComponent(email));
    };
    function deleteUser(userId) {
        if (!confirm('Delete this user?')) return;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete_user.php');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200 && JSON.parse(xhr.responseText).success) {
                document.querySelector('tr[data-user-id="'+userId+'"]')?.remove();
            } else {
                alert('Failed to delete user.');
            }
        };
        xhr.send('user_id='+userId);
    }
    </script>
</body>
</html> 