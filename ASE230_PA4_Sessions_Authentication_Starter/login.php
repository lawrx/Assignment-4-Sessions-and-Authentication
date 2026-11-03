<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $users = loadUsers();
    $user = findUserByEmail($users, $email);

    if ($user && password_verify($password, $user['password'])) {
        loginUser($user);

        $redirect = $_SESSION['intended_url'] ?? 'index.php';
        unset($_SESSION['intended_url']);

        header('Location: ' . $redirect);
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<main class="container narrow">
    <section class="panel">
        <h1>Support Desk Sign In</h1>
        <p>Sign in to access the support ticket application.</p>

        <?php if ($error !== ''): ?>
            <div class="error-box"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= e($email) ?>"
                autocomplete="username"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                required
            >

            <button type="submit">Sign In</button>
        </form>
    </section>
</main>
</body>
</html>