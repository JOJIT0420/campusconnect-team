<?php

require_once "config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullName = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";
    $role = $_POST["role"] ?? "";

    if (
        $fullName === "" ||
        $email === "" ||
        $password === "" ||
        $confirmPassword === "" ||
        $role === ""
    ) {

        $message = "Please complete all fields.";
        $messageType = "danger";

    } elseif (!in_array($role, ["student", "admin"], true)) {

        $message = "Please select a valid account type.";
        $messageType = "danger";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $messageType = "danger";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";
        $messageType = "danger";

    } elseif ($password !== $confirmPassword) {

        $message = "Passwords do not match.";
        $messageType = "danger";

    } elseif (
        !isset($_FILES["id_file"]) ||
        $_FILES["id_file"]["error"] !== UPLOAD_ERR_OK
    ) {

        $message = "Please upload your school ID.";
        $messageType = "danger";

    } else {

        $check = $pdo->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->execute([$email]);

        if ($check->fetch()) {

            $message = "An account with this email already exists.";
            $messageType = "danger";

        } else {

            $file = $_FILES["id_file"];

            $allowedExtensions = [
                "jpg",
                "jpeg",
                "png",
                "webp",
                "pdf"
            ];

            $maxFileSize = 5 * 1024 * 1024; // 5 MB in bytes

            $extension = strtolower(
                pathinfo($file["name"], PATHINFO_EXTENSION)
            );

            if (!in_array($extension, $allowedExtensions, true)) {

                $message = "Invalid ID file type.";
                $messageType = "danger";

            } elseif ($file["size"] > $maxFileSize) {

                $message = "School ID must not exceed 5 MB.";
                $messageType = "danger";

            } else {

                $uploadDirectory = "assets/uploads/ids/";

                if (!is_dir($uploadDirectory)) {
                    mkdir($uploadDirectory, 0777, true);
                }

                $fileName = uniqid("id_", true) . "." . $extension;
                $filePath = $uploadDirectory . $fileName;

                if (move_uploaded_file(
                    $file["tmp_name"],
                    $filePath
                )) {

                    $passwordHash = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    $stmt = $pdo->prepare(
                        "INSERT INTO users
                        (full_name, email, password_hash, role, status, id_file)
                        VALUES (?, ?, ?, ?, 'pending', ?)"
                    );

                    $stmt->execute([
                        $fullName,
                        $email,
                        $passwordHash,
                        $role,
                        $filePath
                    ]);

                    $message =
                        "Registration submitted. Please wait for approval.";

                    $messageType = "success";

                } else {

                    $message = "Failed to upload your school ID.";
                    $messageType = "danger";
                }
            }
        }
    }
}

require "includes/header.php";
?>

<div class="form-card register-form">

    <div class="text-center mb-4">

        <h2 class="mb-2">
            Create Account
        </h2>

        <p class="text-muted mb-0">
            Join the Mini Campus Event Hub
        </p>

    </div>

    <?php if ($message !== ""): ?>

        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <form
        method="POST"
        action=""
        enctype="multipart/form-data"
    >

        <div class="mb-3">

            <label class="form-label">
                Full Name
            </label>

            <input
                type="text"
                name="full_name"
                class="form-control"
                placeholder="Enter your full name"
                required
            >

        </div>

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
                Account Type
            </label>

            <select
                name="role"
                class="form-select"
                required
            >

                <option value="">
                    Select account type
                </option>

                <option value="student">
                    Student
                </option>

                <option value="admin">
                    Administrator
                </option>

            </select>

        </div>

        <div class="row">

            <div class="col-md-6 mb-3">

                <label class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Create password"
                    required
                >

            </div>

            <div class="col-md-6 mb-3">

                <label class="form-label">
                    Confirm Password
                </label>

                <input
                    type="password"
                    name="confirm_password"
                    class="form-control"
                    placeholder="Confirm password"
                    required
                >

            </div>

        </div>

        <div class="mb-3">

            <label class="form-label">
                School ID
            </label>

            <input
                type="file"
                name="id_file"
                class="form-control"
                accept=".jpg,.jpeg,.png,.webp,.pdf"
                required
            >

            <small class="text-muted">
                JPG, PNG, WEBP or PDF • Maximum 5 MB
            </small>

        </div>

        <div class="small text-muted text-center mb-3">
            Your account requires administrator approval before login.
        </div>

        <button
            type="submit"
            class="btn btn-primary w-100"
        >
            Submit Registration
        </button>

    </form>

    <div class="text-center mt-3">

        <small>
            Already have an account?
            <a href="login.php">
                Login
            </a>
        </small>

    </div>

</div>

<?php require "includes/footer.php"; ?>