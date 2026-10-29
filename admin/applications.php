<?php

require_once "../config/database.php";
require_once "../includes/auth.php";

requireAdmin();

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $userId = $_POST["user_id"] ?? "";
    $action = $_POST["action"] ?? "";

    if (is_numeric($userId) && in_array($action, ["approve", "reject"], true)) {

        if ($action === "approve") {

            $status = "active";
            $message = "Account approved successfully.";
            $messageType = "success";

        } else {

            $status = "rejected";
            $message = "Account rejected.";
            $messageType = "warning";
        }

        $stmt = $pdo->prepare(
            "UPDATE users
             SET status = ?
             WHERE id = ?
             AND role IN ('student', 'admin')"
        );

        $stmt->execute([
            $status,
            $userId
        ]);
    }
}

$stmt = $pdo->query(
    "SELECT id, full_name, email, role, status, id_file
     FROM users
     WHERE status = 'pending'
     AND role IN ('student', 'admin')
     ORDER BY id DESC"
);

$applications = $stmt->fetchAll();

require "../includes/header.php";
?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="mb-1">
                Account Applications
            </h2>

            <p class="text-muted mb-0">
                Review and approve new account registrations.
            </p>

        </div>

        <a
            href="dashboard.php"
            class="btn btn-outline-secondary"
        >
            Back to Dashboard
        </a>

    </div>

    <?php if ($message !== ""): ?>

        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <?php if (count($applications) === 0): ?>

        <div class="card shadow-sm border-0">

            <div class="card-body text-center py-5">

                <h5>
                    No Pending Applications
                </h5>

                <p class="text-muted mb-0">
                    There are currently no accounts waiting for approval.
                </p>

            </div>

        </div>

    <?php else: ?>

        <div class="card shadow-sm border-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead>

                        <tr>

                            <th>Name</th>
                            <th>Email</th>
                            <th>Account Type</th>
                            <th>School ID</th>
                            <th class="text-center">Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($applications as $application): ?>

                            <tr>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $application["full_name"]
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $application["email"]
                                    );
                                    ?>
                                </td>

                                <td>

                                    <?php if ($application["role"] === "admin"): ?>

                                        <span class="badge bg-danger">
                                            Administrator
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-primary">
                                            Student
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?php if (!empty($application["id_file"])): ?>

                                        <a
                                            href="../<?php echo htmlspecialchars($application["id_file"]); ?>"
                                            target="_blank"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            View ID
                                        </a>

                                    <?php else: ?>

                                        <span class="text-muted">
                                            No ID
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <div class="d-flex justify-content-center gap-2">

                                        <form
                                            method="POST"
                                            action=""
                                        >

                                            <input
                                                type="hidden"
                                                name="user_id"
                                                value="<?php echo $application["id"]; ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="approve"
                                            >

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-success"
                                            >
                                                Approve
                                            </button>

                                        </form>

                                        <form
                                            method="POST"
                                            action=""
                                        >

                                            <input
                                                type="hidden"
                                                name="user_id"
                                                value="<?php echo $application["id"]; ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="reject"
                                            >

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger"
                                            >
                                                Reject
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    <?php endif; ?>

</div>

<?php require "../includes/footer.php"; ?>