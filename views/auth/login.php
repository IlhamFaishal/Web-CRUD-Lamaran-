<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - POS Besi & Kayu</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/../assets/css/style.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <h2 class="login-title">LOGIN POS</h2>
        
        <?php $flash = getFlashMessage(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] == 'error' ? 'error' : 'success' ?>">
                <?= $flash['message'] ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/auth/authenticate" method="POST">
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" required placeholder="admin">
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="admin123">
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <p style="text-align: center; margin-top:10px; font-size: 0.8rem; color: #888;">
            Default: admin / admin123
        </p>
    </div>
</div>

</body>
</html>
