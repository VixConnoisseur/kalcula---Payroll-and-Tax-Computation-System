<?php
// backend/helpers/auth.php

function ensureLoggedIn(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        // Adjust this path if your login file lives somewhere else.
        header('Location: /bsit3a_guasis/Kalcula/frontend/auth/login.php');
        exit;
    }
}
