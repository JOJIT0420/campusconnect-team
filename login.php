<?php

require_once "config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {

        $message = "Please enter your email and password.";
        $messageType = "danger";

    } else {

        $stmt = $pdo->prepare(
            "SELECT id, full_name, email, password_hash, role, status
             FROM users
             WHERE email = ?"
        );

        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if (!$user) {

            $message = "Invalid email or password.";
            $messageType = "danger";

        } elseif (!password_verify($password, $user["password_hash"])) {

            $message = "Invalid email or password.";
            $messageType = "danger";

        } elseif ($user["status"] === "pending") {

            $message = "Your account is still waiting for administrator approval.";
            $messageType = "warning";

        } elseif ($user["status"] === "rejected") {

            $message = "Your account registration has been rejected.";
            $messageType = "danger";

        } elseif ($user["status"] !== "active") {

            $message = "Your account is not active.";
            $messageType = "danger";

        } else {

            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];

            if ($user["role"] === "admin") {

                header("Location: admin/dashboard.php");
                exit;

            } else {

                header("Location: index.php");
                exit;
            }
        }
    }
}

require "includes/header.php";
?>

<div class="form-card">

    <h2 class="text-center mb-4">
        Welcome Back
    </h2>

    <p class="text-center text-muted mb-4">
        Login to your Campus Event Hub account
    </p>

    <?php if ($message !== ""): ?>

        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <form method="POST" action="">

        <div class="mb-3">

            <label class="form-label">
                Email Address
            </label>

            <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Enter your email"
                required
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Password
            </label>

            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Enter your password"
                required
            >

        </div>

        <button
            type="submit"
            class="btn btn-primary w-100"
        >
            Login
        </button>

    </form>

    <div class="text-center mt-4">

        <p class="mb-0">
            Don't have an account?

            <a href="register.php">
                Create Account
            </a>
        </p>

    </div>

</div>

<?php require "includes/footer.php"; ?>