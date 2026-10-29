<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isAdminPage = strpos(
    $_SERVER["PHP_SELF"],
    "/admin/"
) !== false;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Mini Campus Event Hub | Pateros Technological College
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <?php if ($isAdminPage): ?>

        <link
            rel="stylesheet"
            href="../assets/css/style.css"
        >

    <?php else: ?>

        <link
            rel="stylesheet"
            href="assets/css/style.css"
        >

    <?php endif; ?>

</head>


<body>


<nav
    class="navbar navbar-expand-lg sticky-top"
>

    <div class="container-fluid px-4 px-lg-5">


        <a
            class="navbar-brand d-flex align-items-center gap-3"
            href="<?php
                echo $isAdminPage
                    ? "../index.php"
                    : "index.php";
            ?>"
        >

            <div
                style="
                    width: 70px;
                    height: 70px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-shrink: 0;
                "
            >

                <img
                    src="<?php
                        echo $isAdminPage
                            ? "../assets/images/ptc-logo.png"
                            : "assets/images/ptc-logo.png";
                    ?>"
                    alt="Pateros Technological College Logo"
                    style="
                        width: 70px;
                        height: 70px;
                        object-fit: contain;
                    "
                >

            </div>


            <div>

                <span
                    style="
                        display: block;
                        color: #ffffff;
                        font-size: 22px;
                        font-weight: 800;
                        line-height: 1.1;
                        letter-spacing: -0.3px;
                    "
                >
                    Campus Event Hub
                </span>

                <small
                    style="
                        display: block;
                        margin-top: 4px;
                        color: rgba(255,255,255,0.85);
                        font-size: 12px;
                        font-weight: 600;
                        letter-spacing: .7px;
                    "
                >
                    PATEROS TECHNOLOGICAL COLLEGE
                </small>

            </div>

        </a>


        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >

            <span class="navbar-toggler-icon"></span>

        </button>


        <div
            class="collapse navbar-collapse"
            id="navbarNav"
        >

            <ul
                class="navbar-nav ms-auto align-items-lg-center"
            >


                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="<?php
                            echo $isAdminPage
                                ? "../index.php"
                                : "index.php";
                        ?>"
                    >
                        Home
                    </a>

                </li>


                <?php if (isset($_SESSION["user_id"])): ?>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="<?php
                                echo $isAdminPage
                                    ? "../my-registrations.php"
                                    : "my-registrations.php";
                            ?>"
                        >
                            My Registrations
                        </a>

                    </li>


                    <?php
                    if (
                        isset($_SESSION["role"]) &&
                        $_SESSION["role"] === "admin"
                    ):
                    ?>

                        <li class="nav-item">

                            <a
                                class="nav-link"
                                href="<?php
                                    echo $isAdminPage
                                        ? "dashboard.php"
                                        : "admin/dashboard.php";
                                ?>"
                            >
                                Admin Dashboard
                            </a>

                        </li>

                    <?php endif; ?>


                    <li
                        class="nav-item dropdown ms-lg-2"
                    >

                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            style="
                                background: rgba(255,255,255,0.15);
                                color: #ffffff !important;
                                border-radius: 20px;
                                padding-left: 15px !important;
                                padding-right: 15px !important;
                            "
                        >

                            👤

                            <?php
                            echo htmlspecialchars(
                                $_SESSION["full_name"]
                                ?? "User"
                            );
                            ?>

                        </a>


                        <ul
                            class="dropdown-menu
                                   dropdown-menu-end
                                   border-0
                                   shadow-sm"
                        >

                            <li>

                                <span
                                    class="dropdown-item-text"
                                    style="
                                        font-size: 13px;
                                        color: #6b7280;
                                    "
                                >

                                    Signed in as

                                    <strong
                                        class="d-block"
                                        style="
                                            color: #17352a;
                                        "
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $_SESSION["email"]
                                            ?? ""
                                        );
                                        ?>

                                    </strong>

                                </span>

                            </li>


                            <li>

                                <hr
                                    class="dropdown-divider"
                                >

                            </li>


                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?php
                                        echo $isAdminPage
                                            ? "../my-registrations.php"
                                            : "my-registrations.php";
                                    ?>"
                                >
                                    📋 My Registrations
                                </a>

                            </li>


                            <?php
                            if (
                                isset($_SESSION["role"]) &&
                                $_SESSION["role"] === "admin"
                            ):
                            ?>

                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="<?php
                                            echo $isAdminPage
                                                ? "dashboard.php"
                                                : "admin/dashboard.php";
                                        ?>"
                                    >
                                        ⚙️ Admin Dashboard
                                    </a>

                                </li>

                            <?php endif; ?>


                            <li>

                                <hr
                                    class="dropdown-divider"
                                >

                            </li>


                            <li>

                                <a
                                    class="dropdown-item text-danger"
                                    href="<?php
                                        echo $isAdminPage
                                            ? "../logout.php"
                                            : "logout.php";
                                    ?>"
                                >
                                    🚪 Logout
                                </a>

                            </li>

                        </ul>

                    </li>


                <?php else: ?>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="<?php
                                echo $isAdminPage
                                    ? "../login.php"
                                    : "login.php";
                            ?>"
                        >
                            Login
                        </a>

                    </li>


                    <li
                        class="nav-item ms-lg-2"
                    >

                        <a
                            class="btn btn-light px-4 fw-bold"
                            href="<?php
                                echo $isAdminPage
                                    ? "../register.php"
                                    : "register.php";
                            ?>"
                        >
                            Create Account
                        </a>

                    </li>

                <?php endif; ?>


            </ul>

        </div>

    </div>

</nav>


<main class="container-fluid py-4 px-3 px-lg-4">