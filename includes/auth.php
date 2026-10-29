<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin()
{
    if (!isset($_SESSION["user_id"])) {

        $isAdminPage = strpos(
            $_SERVER["PHP_SELF"],
            "/admin/"
        ) !== false;

        if ($isAdminPage) {

            header("Location: ../login.php");

        } else {

            header("Location: login.php");

        }

        exit;
    }
}

function requireAdmin()
{
    requireLogin();

    if (
        !isset($_SESSION["role"]) ||
        $_SESSION["role"] !== "admin"
    ) {

        $isAdminPage = strpos(
            $_SERVER["PHP_SELF"],
            "/admin/"
        ) !== false;

        if ($isAdminPage) {

            header("Location: ../index.php");

        } else {

            header("Location: index.php");

        }

        exit;
    }
}

?>