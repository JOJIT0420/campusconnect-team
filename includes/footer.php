</main>


<!-- FOOTER -->

<footer
    class="mt-5"
    style="
        background: #004d2b;
        color: #ffffff;
        border-top: 4px solid #f4c542;
    "
>

    <div class="container">

        <div class="row g-4 py-5">


            <!-- BRAND -->

            <div class="col-lg-5">

                <div
                    class="d-flex
                           align-items-center
                           gap-3
                           mb-3"
                >

                    <!-- PTC LOGO -->

                    <div
                        style="
                            width: 52px;
                            height: 52px;
                            background: #ffffff;
                            border-radius: 13px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            padding: 4px;
                            flex-shrink: 0;
                        "
                    >

                        <img
                            src="<?php
                                echo strpos(
                                    $_SERVER["PHP_SELF"],
                                    "/admin/"
                                ) !== false
                                    ? "../assets/images/ptc-logo.png"
                                    : "assets/images/ptc-logo.png";
                            ?>"
                            alt="Pateros Technological College Logo"
                            style="
                                width: 44px;
                                height: 44px;
                                object-fit: contain;
                            "
                        >

                    </div>


                    <!-- BRAND TEXT -->

                    <div>

                        <h4
                            class="mb-0"
                            style="
                                color: #ffffff;
                                font-weight: 800;
                            "
                        >
                            Campus Event Hub
                        </h4>

                        <small
                            style="
                                color: rgba(255,255,255,.7);
                                font-weight: 600;
                            "
                        >
                            PATEROS TECHNOLOGICAL COLLEGE
                        </small>

                    </div>

                </div>


                <p
                    class="mb-0"
                    style="
                        color: rgba(255,255,255,.78);
                        max-width: 520px;
                        line-height: 1.7;
                    "
                >
                    A campus event platform designed to help
                    students discover activities, register for
                    events, and stay connected with campus
                    programs and organizations.
                </p>

            </div>


            <!-- QUICK LINKS -->

            <div class="col-6 col-lg-3">

                <h6
                    class="text-uppercase mb-3"
                    style="
                        color: #f4c542;
                        font-weight: 800;
                        letter-spacing: .7px;
                    "
                >
                    Quick Links
                </h6>


                <ul
                    class="list-unstyled mb-0"
                >

                    <!-- HOME -->

                    <li class="mb-2">

                        <a
                            href="<?php
                                echo strpos(
                                    $_SERVER["PHP_SELF"],
                                    "/admin/"
                                ) !== false
                                    ? "../index.php"
                                    : "index.php";
                            ?>"
                            style="
                                color: rgba(255,255,255,.78);
                                text-decoration: none;
                            "
                        >
                            Home
                        </a>

                    </li>


                    <?php if (isset($_SESSION["user_id"])): ?>


                        <!-- MY REGISTRATIONS -->

                        <li class="mb-2">

                            <a
                                href="<?php
                                    echo strpos(
                                        $_SERVER["PHP_SELF"],
                                        "/admin/"
                                    ) !== false
                                        ? "../my-registrations.php"
                                        : "my-registrations.php";
                                ?>"
                                style="
                                    color: rgba(255,255,255,.78);
                                    text-decoration: none;
                                "
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

                            <!-- ADMIN -->

                            <li class="mb-2">

                                <a
                                    href="<?php
                                        echo strpos(
                                            $_SERVER["PHP_SELF"],
                                            "/admin/"
                                        ) !== false
                                            ? "dashboard.php"
                                            : "admin/dashboard.php";
                                    ?>"
                                    style="
                                        color: rgba(255,255,255,.78);
                                        text-decoration: none;
                                    "
                                >
                                    Admin Dashboard
                                </a>

                            </li>

                        <?php endif; ?>


                    <?php else: ?>


                        <!-- LOGIN -->

                        <li class="mb-2">

                            <a
                                href="<?php
                                    echo strpos(
                                        $_SERVER["PHP_SELF"],
                                        "/admin/"
                                    ) !== false
                                        ? "../login.php"
                                        : "login.php";
                                ?>"
                                style="
                                    color: rgba(255,255,255,.78);
                                    text-decoration: none;
                                "
                            >
                                Login
                            </a>

                        </li>


                        <!-- CREATE ACCOUNT -->

                        <li class="mb-2">

                            <a
                                href="<?php
                                    echo strpos(
                                        $_SERVER["PHP_SELF"],
                                        "/admin/"
                                    ) !== false
                                        ? "../register.php"
                                        : "register.php";
                                ?>"
                                style="
                                    color: rgba(255,255,255,.78);
                                    text-decoration: none;
                                "
                            >
                                Create Account
                            </a>

                        </li>

                    <?php endif; ?>

                </ul>

            </div>


            <!-- SYSTEM INFORMATION -->

            <div class="col-6 col-lg-4">

                <h6
                    class="text-uppercase mb-3"
                    style="
                        color: #f4c542;
                        font-weight: 800;
                        letter-spacing: .7px;
                    "
                >
                    Campus Event Hub
                </h6>


                <p
                    class="mb-2"
                    style="
                        color: rgba(255,255,255,.78);
                    "
                >
                    📅 Discover campus events
                </p>


                <p
                    class="mb-2"
                    style="
                        color: rgba(255,255,255,.78);
                    "
                >
                    🎟️ Register for activities
                </p>


                <p
                    class="mb-0"
                    style="
                        color: rgba(255,255,255,.78);
                    "
                >
                    🎓 Stay connected with campus life
                </p>

            </div>

        </div>


        <!-- DIVIDER -->

        <hr
            style="
                border-color: rgba(255,255,255,.16);
                margin: 0;
            "
        >


        <!-- FOOTER BOTTOM -->

        <div
            class="d-flex
                   flex-column
                   flex-md-row
                   justify-content-between
                   align-items-center
                   gap-2
                   py-4"
        >

            <p
                class="mb-0"
                style="
                    color: rgba(255,255,255,.68);
                    font-size: 14px;
                "
            >
                © <?php echo date("Y"); ?>
                Mini Campus Event Hub
            </p>


            <p
                class="mb-0"
                style="
                    color: rgba(255,255,255,.68);
                    font-size: 14px;
                "
            >
                Pateros Technological College
            </p>

        </div>

    </div>

</footer>


<!-- BOOTSTRAP JAVASCRIPT -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


<!-- CUSTOM JAVASCRIPT -->

<?php if (
    strpos(
        $_SERVER["PHP_SELF"],
        "/admin/"
    ) !== false
): ?>

    <script
        src="../assets/js/script.js"
    ></script>

<?php else: ?>

    <script
        src="assets/js/script.js"
    ></script>

<?php endif; ?>


</body>

</html>