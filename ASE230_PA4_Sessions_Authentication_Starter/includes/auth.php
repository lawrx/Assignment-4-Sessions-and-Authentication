<?php

/*
 * This file is included by pages that need session/authentication behavior.
 *
 * IMPORTANT:
 * session_start() must happen BEFORE any HTML is sent to the browser.
 * PHP uses the session cookie to reconnect this request with server-side
 * session data from an earlier request.
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/*
 * Return the currently authenticated user stored in the session.
 *
 * We store only the information the application needs. We DO NOT store
 * the user's password or password hash in the session.
 */
function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function loginUser(array $user): void
{
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role']
    ];
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

function requireRole(string $requiredRole): void
{
    requireLogin();

    $user = currentUser();

    if (!$user || ($user['role'] ?? '') !== $requiredRole) {
        http_response_code(403);
        exit('Forbidden');
    }
}

function setFlash(string $message): void
{
    $_SESSION['flash'] = $message;
}

function getFlash(): ?string
{
    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $message;
}

function logoutUser(): void
{
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}