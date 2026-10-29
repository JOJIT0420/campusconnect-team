<?php

require_once "../config/database.php";
require_once "../includes/auth.php";

requireAdmin();

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: dashboard.php");
    exit;
}

$eventId = (int) $_GET["id"];

$stmt = $pdo->prepare(
    "SELECT *
     FROM events
     WHERE id = ?"
);

$stmt->execute([$eventId]);

$event = $stmt->fetch();

if (!$event) {
    header("Location: dashboard.php");
    exit;
}

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    $_POST["title"] = $event["title"];
    $_POST["description"] = $event["description"];
    $_POST["category"] = $event["category"];
    $_POST["event_date"] = $event["event_date"];
    $_POST["start_time"] = $event["start_time"];
    $_POST["end_time"] = $event["end_time"];
    $_POST["venue"] = $event["venue"];
    $_POST["capacity"] = $event["capacity"];
    $_POST["status"] = $event["status"];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $eventDate = $_POST["event_date"] ?? "";
    $startTime = $_POST["start_time"] ?? "";
    $endTime = $_POST["end_time"] ?? "";
    $venue = trim($_POST["venue"] ?? "");
    $capacity = (int) ($_POST["capacity"] ?? 0);
    $status = $_POST["status"] ?? "";

    if (
        $title === "" ||
        $description === "" ||
        $category === "" ||
        $eventDate === "" ||
        $startTime === "" ||
        $endTime === "" ||
        $venue === "" ||
        $capacity <= 0
    ) {

        $message = "Please fill in all required fields correctly.";
        $messageType = "danger";

    } elseif ($startTime >= $endTime) {

        $message = "End time must be later than the start time.";
        $messageType = "danger";

    } elseif ($eventDate < date("Y-m-d")) {

        $message = "The event date cannot be in the past.";
        $messageType = "danger";

    } elseif (
        $status !== "active" &&
        $status !== "cancelled" &&
        $status !== "completed" &&
        $status !== "archived"
    ) {

        $message = "Invalid event status.";
        $messageType = "danger";

    } else {

        try {

            $registeredStmt = $pdo->prepare(
                "SELECT COUNT(*) AS total
                 FROM registrations
                 WHERE event_id = ?
                 AND status = 'registered'"
            );

            $registeredStmt->execute([$eventId]);

            $registeredCount =
                (int) $registeredStmt->fetch()["total"];

            if ($capacity < $registeredCount) {

                $message =
                    "Capacity cannot be lower than the current "
                    . "number of registered students ("
                    . $registeredCount
                    . ").";

                $messageType = "danger";

            } else {

                $updateStmt = $pdo->prepare(
                    "UPDATE events
                     SET
                        title = ?,
                        description = ?,
                        category = ?,
                        event_date = ?,
                        start_time = ?,
                        end_time = ?,
                        venue = ?,
                        capacity = ?,
                        status = ?
                     WHERE id = ?"
                );

                $updateStmt->execute([
                    $title,
                    $description,
                    $category,
                    $eventDate,
                    $startTime,
                    $endTime,
                    $venue,
                    $capacity,
                    $status,
                    $eventId
                ]);

                $_SESSION["event_message"] =
                    "Event details updated successfully.";

                $_SESSION["event_message_type"] =
                    "success";

                header("Location: dashboard.php");
                exit;
            }

        } catch (PDOException $e) {

            $message =
                "Unable to update the event. Please try again.";

            $messageType = "danger";
        }
    }
}

$currentRegisteredStmt = $pdo->prepare(
    "SELECT COUNT(*) AS total
     FROM registrations
     WHERE event_id = ?
     AND status = 'registered'"
);

$currentRegisteredStmt->execute([$eventId]);

$currentRegistered =
    (int) $currentRegisteredStmt->fetch()["total"];

require "../includes/header.php";

?>

<div
    class="rounded-4 p-4 p-md-5 mb-5"
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
            width: 230px;
            height: 230px;
            border-radius: 50%;
            background: rgba(244,197,66,.12);
            right: -80px;
            top: -90px;
        "
    ></div>

    <div class="row align-items-center">

        <div class="col-lg-8">

            <span
                class="badge bg-warning text-dark px-3 py-2 mb-3"
            >
                EVENT ADMINISTRATION
            </span>

            <h1
                class="text-white mb-2"
                style="
                    font-weight: 800;
                    font-size: clamp(34px, 5vw, 50px);
                "
            >
                Edit Event
            </h1>

            <p
                class="mb-0"
                style="
                    color: rgba(255,255,255,.86);
                    font-size: 17px;
                    max-width: 680px;
                "
            >
                Update the information, schedule, venue,
                capacity, or status of this campus event.
            </p>

        </div>

        <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">

            <a
                href="dashboard.php"
                class="btn btn-light"
            >
                ← Back to Dashboard
            </a>

        </div>

    </div>

</div>

<div class="card border-0 shadow-sm mb-4">

    <div class="card-body p-4">

        <div class="row align-items-center">

            <div class="col-md-8">

                <small
                    class="text-uppercase fw-bold"
                    style="
                        color: #006b3c;
                        letter-spacing: 1px;
                    "
                >
                    Currently Editing
                </small>

                <h3 class="mt-2 mb-1">

                    <?php
                    echo htmlspecialchars(
                        $event["title"]
                    );
                    ?>

                </h3>

                <p class="text-muted mb-0">

                    Event ID:

                    <strong>
                        #<?php echo $eventId; ?>
                    </strong>

                </p>

            </div>

            <div class="col-md-4 text-md-end mt-3 mt-md-0">

                <?php if ($event["status"] === "active"): ?>

                    <span
                        class="badge bg-warning text-dark fs-6 px-3 py-2"
                    >
                        Active
                    </span>

                <?php elseif ($event["status"] === "completed"): ?>

                    <span
                        class="badge bg-success fs-6 px-3 py-2"
                    >
                        Completed
                    </span>

                <?php elseif ($event["status"] === "cancelled"): ?>

                    <span
                        class="badge bg-danger fs-6 px-3 py-2"
                    >
                        Cancelled
                    </span>

                <?php elseif ($event["status"] === "archived"): ?>

                    <span
                        class="badge bg-secondary fs-6 px-3 py-2"
                    >
                        Archived
                    </span>

                <?php else: ?>

                    <span
                        class="badge bg-secondary fs-6 px-3 py-2"
                    >
                        <?php echo htmlspecialchars(ucfirst($event["status"])); ?>
                    </span>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<div class="row justify-content-center">

    <div class="col-xl-10">

        <div class="card border-0 shadow-sm">

            <div class="card-body p-4 p-md-5">

                <div class="mb-4">

                    <span
                        class="text-uppercase fw-bold small"
                        style="
                            color: #006b3c;
                            letter-spacing: 1px;
                        "
                    >
                        Event Information
                    </span>

                    <h2 class="mt-2 mb-1">
                        Update Event Details
                    </h2>

                    <p class="text-muted mb-0">
                        Make the necessary changes and save
                        them when you're finished.
                    </p>

                </div>

                <?php if ($message !== ""): ?>

                    <div
                        class="alert alert-<?php echo htmlspecialchars($messageType); ?> mb-4"
                    >
                        <?php
                        echo htmlspecialchars($message);
                        ?>
                    </div>

                <?php endif; ?>

                <form
                    method="POST"
                    action=""
                >

                    <div class="row g-4">

                        <div class="col-md-8">

                            <label
                                for="title"
                                class="form-label"
                            >
                                Event Title
                            </label>

                            <input
                                type="text"
                                id="title"
                                name="title"
                                class="form-control"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["title"] ?? ""
                                );
                                ?>"
                                required
                            >

                        </div>

                        <div class="col-md-4">

                            <label
                                for="category"
                                class="form-label"
                            >
                                Category
                            </label>

                            <select
                                id="category"
                                name="category"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Select Category
                                </option>

                                <?php

                                $categoryOptions = [
                                    "Seminar",
                                    "Workshop",
                                    "Competition",
                                    "Sports",
                                    "Student Activity",
                                    "Organization",
                                    "Meeting",
                                    "Academic",
                                    "School Celebration",
                                    "Other"
                                ];

                                foreach (
                                    $categoryOptions as $option
                                ):

                                ?>

                                    <option
                                        value="<?php
                                        echo htmlspecialchars($option);
                                        ?>"
                                        <?php
                                        echo (
                                            ($_POST["category"] ?? "")
                                            === $option
                                        )
                                            ? "selected"
                                            : "";
                                        ?>
                                    >
                                        <?php
                                        echo htmlspecialchars($option);
                                        ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="col-12">

                            <label
                                for="description"
                                class="form-label"
                            >
                                Event Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                class="form-control"
                                rows="6"
                                required
                            ><?php
                            echo htmlspecialchars(
                                $_POST["description"] ?? ""
                            );
                            ?></textarea>

                        </div>

                        <div class="col-md-4">

                            <label
                                for="event_date"
                                class="form-label"
                            >
                                Event Date
                            </label>

                            <input
                                type="date"
                                id="event_date"
                                name="event_date"
                                class="form-control"
                                min="<?php echo date("Y-m-d"); ?>"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["event_date"] ?? ""
                                );
                                ?>"
                                required
                            >

                        </div>

                        <div class="col-md-4">

                            <label
                                for="start_time"
                                class="form-label"
                            >
                                Start Time
                            </label>

                            <input
                                type="time"
                                id="start_time"
                                name="start_time"
                                class="form-control"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["start_time"] ?? ""
                                );
                                ?>"
                                required
                            >

                        </div>

                        <div class="col-md-4">

                            <label
                                for="end_time"
                                class="form-label"
                            >
                                End Time
                            </label>

                            <input
                                type="time"
                                id="end_time"
                                name="end_time"
                                class="form-control"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["end_time"] ?? ""
                                );
                                ?>"
                                required
                            >

                        </div>

                        <div class="col-md-8">

                            <label
                                for="venue"
                                class="form-label"
                            >
                                Venue
                            </label>

                            <input
                                type="text"
                                id="venue"
                                name="venue"
                                class="form-control"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["venue"] ?? ""
                                );
                                ?>"
                                required
                            >

                        </div>

                        <div class="col-md-4">

                            <label
                                for="capacity"
                                class="form-label"
                            >
                                Maximum Capacity
                            </label>

                            <input
                                type="number"
                                id="capacity"
                                name="capacity"
                                class="form-control"
                                min="1"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["capacity"] ?? ""
                                );
                                ?>"
                                required
                            >

                            <small class="text-muted">

                                Currently registered:

                                <strong>
                                    <?php
                                    echo $currentRegistered;
                                    ?>
                                </strong>

                            </small>

                        </div>

                        <div class="col-md-6">

                            <label
                                for="status"
                                class="form-label"
                            >
                                Event Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                class="form-select"
                                required
                            >

                                <option
                                    value="active"
                                    <?php
                                    echo (
                                        ($_POST["status"] ?? "active")
                                        === "active"
                                    )
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Active (Open for Registration)
                                </option>

                                <option
                                    value="cancelled"
                                    <?php
                                    echo (
                                        ($_POST["status"] ?? "")
                                        === "cancelled"
                                    )
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Cancelled
                                </option>

                                <option
                                    value="completed"
                                    <?php
                                    echo (
                                        ($_POST["status"] ?? "")
                                        === "completed"
                                    )
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Completed
                                </option>

                                <option
                                    value="archived"
                                    <?php
                                    echo (
                                        ($_POST["status"] ?? "")
                                        === "archived"
                                    )
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Archived
                                </option>

                            </select>

                            <small class="text-muted">
                                Active events are available for students to view and register.
                            </small>

                        </div>

                        <div class="col-md-6">

                            <label
                                class="form-label"
                            >
                                Event Reference
                            </label>

                            <div
                                class="form-control"
                                style="
                                    background: #f6f8f7;
                                    display: flex;
                                    align-items: center;
                                "
                            >

                                📅

                                <span class="ms-2">
                                    Event #<?php echo $eventId; ?>
                                </span>

                            </div>

                        </div>

                    </div>

                    <hr class="my-5">

                    <div
                        class="d-flex flex-wrap gap-2
                               justify-content-between
                               align-items-center"
                    >

                        <div class="d-flex gap-2">

                            <a
                                href="dashboard.php"
                                class="btn btn-outline-secondary"
                            >
                                Cancel
                            </a>

                            <a
                                href="../event-details.php?id=<?php echo $eventId; ?>"
                                class="btn btn-outline-primary"
                            >
                                View Event
                            </a>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary btn-lg px-4"
                        >
                            Save Changes
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<div
    class="mt-4 p-4 rounded-4"
    style="
        background: #eaf6ef;
        border: 1px solid #d7ebdf;
    "
>

    <div class="d-flex gap-3 align-items-start">

        <div style="font-size: 28px;">
            💡
        </div>

        <div>

            <h5 class="mb-1">
                Capacity Reminder
            </h5>

            <p class="text-muted mb-0">
                The event capacity cannot be set below the
                number of students who are currently registered.
                This protects existing registrations.
            </p>

        </div>

    </div>

</div>

<?php require "../includes/footer.php"; ?>