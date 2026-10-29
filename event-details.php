<?php

require_once "config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$eventId = (int) $_GET["id"];

$stmt = $pdo->prepare(
    "SELECT
        e.id,
        e.title,
        e.description,
        e.category,
        e.event_date,
        e.start_time,
        e.end_time,
        e.venue,
        e.capacity,
        e.status,
        e.created_by,
        u.full_name AS organizer,
        (SELECT COUNT(*) 
         FROM registrations r 
         WHERE r.event_id = e.id AND r.status = 'registered') AS registered
    FROM events e
    LEFT JOIN users u ON e.created_by = u.id
    WHERE e.id = ?"
);

$stmt->execute([$eventId]);

$event = $stmt->fetch();

if (!$event) {
    header("Location: index.php");
    exit;
}

$registered = (int) $event["registered"];
$capacity = (int) $event["capacity"];

$remaining = max(
    0,
    $capacity - $registered
);

$percentage = $capacity > 0
    ? min(
        100,
        ($registered / $capacity) * 100
    )
    : 0;

$isRegistered = false;

if (isset($_SESSION["user_id"])) {

    $checkRegistration = $pdo->prepare(
        "SELECT id
         FROM registrations
         WHERE user_id = ?
         AND event_id = ?
         AND status = 'registered'"
    );

    $checkRegistration->execute([
        $_SESSION["user_id"],
        $eventId
    ]);

    $isRegistered = (bool) $checkRegistration->fetch();
}

require "includes/header.php";

?>

<?php if (isset($_SESSION["registration_message"])): ?>

    <div class="alert alert-<?php echo htmlspecialchars($_SESSION["registration_type"]); ?> alert-dismissible fade show mb-4">

        <?php
        echo htmlspecialchars(
            $_SESSION["registration_message"]
        );
        ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>

    <?php

    unset($_SESSION["registration_message"]);
    unset($_SESSION["registration_type"]);

    ?>

<?php endif; ?>

<div class="mb-4">

    <a
        href="index.php"
        class="btn btn-outline-secondary"
    >
        ← Back to Events
    </a>

</div>

<div
    class="rounded-4 p-4 p-md-5 mb-4"
    style="
        background:
            linear-gradient(
                135deg,
                #004d2b,
                #006b3c
            );
        color: white;
        position: relative;
        overflow: hidden;
    "
>

    <div
        style="
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(244,197,66,.12);
            right: -70px;
            top: -80px;
        "
    ></div>

    <div class="row align-items-center">

        <div class="col-lg-8">

            <span class="badge bg-warning text-dark mb-3 px-3 py-2">

                <?php
                echo htmlspecialchars(
                    $event["category"]
                );
                ?>

            </span>

            <h1
                class="text-white mb-3"
                style="
                    font-size: clamp(32px, 5vw, 52px);
                    font-weight: 800;
                "
            >

                <?php
                echo htmlspecialchars(
                    $event["title"]
                );
                ?>

            </h1>

            <p
                class="mb-0"
                style="
                    color: rgba(255,255,255,.88);
                    font-size: 18px;
                    max-width: 750px;
                "
            >
                Join the Pateros Technological College
                community and take part in this campus event.
            </p>

        </div>

        <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">

            <?php if (($event["status"] === "active" || $event["status"] === "upcoming") && $remaining > 0): ?>

                <span class="badge bg-success fs-6 px-3 py-2">
                    ● Registration Open
                </span>

            <?php elseif ($remaining <= 0): ?>

                <span class="badge bg-danger fs-6 px-3 py-2">
                    ● Event Full
                </span>

            <?php else: ?>

                <span class="badge bg-secondary fs-6 px-3 py-2">
                    ● Registration Closed
                </span>

            <?php endif; ?>

        </div>

    </div>

</div>

<div class="row g-4">

    <div class="col-lg-8">

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body p-4 p-md-5">

                <span
                    class="text-uppercase fw-bold small"
                    style="
                        color: #006b3c;
                        letter-spacing: 1px;
                    "
                >
                    About This Event
                </span>

                <h2 class="mt-2 mb-4">
                    Event Description
                </h2>

                <p
                    class="text-muted mb-0"
                    style="
                        font-size: 17px;
                        line-height: 1.8;
                    "
                >

                    <?php
                    echo nl2br(
                        htmlspecialchars(
                            $event["description"]
                        )
                    );
                    ?>

                </p>

            </div>

        </div>

        <div class="card border-0 shadow-sm">

            <div class="card-body p-4 p-md-5">

                <span
                    class="text-uppercase fw-bold small"
                    style="
                        color: #006b3c;
                        letter-spacing: 1px;
                    "
                >
                    Event Information
                </span>

                <h2 class="mt-2 mb-4">
                    Details
                </h2>

                <div class="row g-4">

                    <div class="col-md-6">

                        <div
                            class="p-3 rounded-3"
                            style="
                                background: #f6f8f7;
                                border: 1px solid #e4e9e6;
                            "
                        >

                            <small
                                class="text-muted d-block mb-1"
                            >
                                Date
                            </small>

                            <strong>

                                📅

                                <?php
                                echo date(
                                    "F d, Y",
                                    strtotime(
                                        $event["event_date"]
                                    )
                                );
                                ?>

                            </strong>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div
                            class="p-3 rounded-3"
                            style="
                                background: #f6f8f7;
                                border: 1px solid #e4e9e6;
                            "
                        >

                            <small
                                class="text-muted d-block mb-1"
                            >
                                Time
                            </small>

                            <strong>

                                🕐

                                <?php
                                echo date(
                                    "h:i A",
                                    strtotime(
                                        $event["start_time"]
                                    )
                                );
                                ?>

                                -

                                <?php
                                echo date(
                                    "h:i A",
                                    strtotime(
                                        $event["end_time"]
                                    )
                                );
                                ?>

                            </strong>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div
                            class="p-3 rounded-3"
                            style="
                                background: #f6f8f7;
                                border: 1px solid #e4e9e6;
                            "
                        >

                            <small
                                class="text-muted d-block mb-1"
                            >
                                Venue
                            </small>

                            <strong>

                                📍

                                <?php
                                echo htmlspecialchars(
                                    $event["venue"]
                                );
                                ?>

                            </strong>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div
                            class="p-3 rounded-3"
                            style="
                                background: #f6f8f7;
                                border: 1px solid #e4e9e6;
                            "
                        >

                            <small
                                class="text-muted d-block mb-1"
                            >
                                Organizer
                            </small>

                            <strong>

                                👤

                                <?php
                                echo htmlspecialchars(
                                    $event["organizer"]
                                    ?? "PTC"
                                );
                                ?>

                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div
            class="card border-0 shadow-sm mb-4"
            style="
                position: sticky;
                top: 20px;
            "
        >

            <div class="card-body p-4">

                <h4 class="mb-1">
                    Event Registration
                </h4>

                <p class="text-muted mb-4">
                    Reserve your spot for this event.
                </p>

                <div class="mb-4">

                    <div
                        class="d-flex justify-content-between
                               align-items-center mb-2"
                    >

                        <span class="text-muted">
                            Registration
                        </span>

                        <strong>
                            <?php echo $registered; ?>
                            /
                            <?php echo $capacity; ?>
                        </strong>

                    </div>

                    <div
                        class="progress"
                        style="height: 9px;"
                    >

                        <div
                            class="progress-bar bg-success"
                            style="width: <?php echo $percentage; ?>%;"
                        ></div>

                    </div>

                    <small class="text-muted">

                        <?php echo $remaining; ?>

                        <?php
                        echo $remaining === 1
                            ? "slot"
                            : "slots";
                        ?>

                        remaining

                    </small>

                </div>

                <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>

                    <div
                        class="p-3 rounded-3 mb-3"
                        style="
                            background: #eaf6ef;
                            border: 1px solid #cce7d5;
                        "
                    >

                        <strong class="text-success">
                            ⚙️ Administrator Mode
                        </strong>

                        <p class="text-muted small mb-0 mt-1">
                            You are logged in as an administrator. Event registration is restricted to student accounts.
                        </p>

                    </div>

                    <div class="d-grid">

                        <a
                            href="admin/dashboard.php"
                            class="btn btn-outline-success"
                        >
                            Manage Events in Dashboard
                        </a>

                    </div>

                <?php elseif (!isset($_SESSION["user_id"])): ?>

                    <div
                        class="p-3 rounded-3 mb-3"
                        style="
                            background: #eaf6ef;
                        "
                    >

                        <strong>
                            🎓 Student Account Required
                        </strong>

                        <p class="text-muted small mb-0 mt-1">
                            Please log in or create an account
                            to register for this event.
                        </p>

                    </div>

                    <div class="d-grid gap-2">

                        <a
                            href="login.php"
                            class="btn btn-primary btn-lg"
                        >
                            Login to Register
                        </a>

                        <a
                            href="register.php"
                            class="btn btn-outline-primary"
                        >
                            Create Account
                        </a>

                    </div>

                <?php elseif ($isRegistered): ?>

                    <div class="alert alert-success">

                        <strong>
                            ✓ You're Registered!
                        </strong>

                        <p class="mb-0 mt-1 small">
                            You already have a registration
                            for this event.
                        </p>

                    </div>

                    <div class="d-grid">

                        <a
                            href="my-registrations.php"
                            class="btn btn-outline-primary"
                        >
                            View My Registrations
                        </a>

                    </div>

                <?php elseif ($remaining <= 0): ?>

                    <div class="alert alert-danger">

                        <strong>
                            Event Full
                        </strong>

                        <p class="mb-0 mt-1 small">
                            This event has reached its
                            maximum capacity.
                        </p>

                    </div>

                <?php elseif ($event["status"] !== "active" && $event["status"] !== "upcoming"): ?>

                    <div class="alert alert-warning">

                        <strong>
                            Registration Closed
                        </strong>

                        <p class="mb-0 mt-1 small">
                            Registration for this event
                            is currently unavailable.
                        </p>

                    </div>

                <?php else: ?>

                    <div class="d-grid">

                        <button
                            type="button"
                            class="btn btn-primary btn-lg"
                            data-bs-toggle="modal"
                            data-bs-target="#registrationModal"
                        >
                            Register for This Event
                        </button>

                    </div>

                    <p
                        class="text-center text-muted small mt-3 mb-0"
                    >
                        You will be asked to confirm your
                        registration before it is submitted.
                    </p>

                <?php endif; ?>

            </div>

        </div>

        <div class="card border-0 shadow-sm">

            <div class="card-body p-4">

                <h5 class="mb-3">
                    Why Join?
                </h5>

                <div class="d-flex gap-3 mb-3">

                    <div style="font-size: 25px;">
                        🎓
                    </div>

                    <div>

                        <strong>
                            Learn
                        </strong>

                        <p class="text-muted small mb-0">
                            Gain new knowledge and skills.
                        </p>

                    </div>

                </div>

                <div class="d-flex gap-3 mb-3">

                    <div style="font-size: 25px;">
                        🤝
                    </div>

                    <div>

                        <strong>
                            Connect
                        </strong>

                        <p class="text-muted small mb-0">
                            Meet and collaborate with fellow
                            PTC students.
                        </p>

                    </div>

                </div>

                <div class="d-flex gap-3">

                    <div style="font-size: 25px;">
                        🌟
                    </div>

                    <div>

                        <strong>
                            Participate
                        </strong>

                        <p class="text-muted small mb-0">
                            Become an active part of campus life.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<div
    class="mt-5 p-4 rounded-4 text-center"
    style="
        background: #eaf6ef;
        border: 1px solid #d7ebdf;
    "
>

    <h4>
        Have questions about this event?
    </h4>

    <p class="text-muted mb-0">
        Please contact the event organizer or
        the appropriate PTC office for more information.
    </p>

</div>

<div
    class="modal fade"
    id="registrationModal"
    tabindex="-1"
    aria-labelledby="registrationModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="registrationModalLabel"
                >
                    Confirm Registration
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>

            <div class="modal-body">

                <p class="mb-3">
                    Are you sure you want to register for:
                </p>

                <h5 class="fw-bold mb-4">
                    <?php
                    echo htmlspecialchars(
                        $event["title"]
                    );
                    ?>
                </h5>

                <div
                    class="p-3 rounded-3"
                    style="
                        background: #f6f8f7;
                        border: 1px solid #e4e9e6;
                    "
                >

                    <div class="mb-3">

                        <small class="text-muted d-block mb-1">
                            Date
                        </small>

                        <strong>
                            📅
                            <?php
                            echo date(
                                "F d, Y",
                                strtotime(
                                    $event["event_date"]
                                )
                            );
                            ?>
                        </strong>

                    </div>

                    <div class="mb-3">

                        <small class="text-muted d-block mb-1">
                            Time
                        </small>

                        <strong>
                            🕐
                            <?php
                            echo date(
                                "h:i A",
                                strtotime(
                                    $event["start_time"]
                                )
                            );
                            ?>

                            -

                            <?php
                            echo date(
                                "h:i A",
                                strtotime(
                                    $event["end_time"]
                                )
                            );
                            ?>
                        </strong>

                    </div>

                    <div>

                        <small class="text-muted d-block mb-1">
                            Venue
                        </small>

                        <strong>
                            📍
                            <?php
                            echo htmlspecialchars(
                                $event["venue"]
                            );
                            ?>
                        </strong>

                    </div>

                </div>

                <p class="text-muted small mt-3 mb-0">
                    Please review the event details before
                    confirming your registration.
                </p>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>

                <form
                    method="POST"
                    action="register-event.php"
                    class="m-0"
                >

                    <input
                        type="hidden"
                        name="event_id"
                        value="<?php echo $eventId; ?>"
                    >

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Confirm Registration
                    </button>

                </form>

            </div>

        </div>

    </div>
</div>

<?php require "includes/footer.php"; ?> 