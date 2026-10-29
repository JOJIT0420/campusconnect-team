<?php

require_once "../config/database.php";
require_once "../includes/auth.php";

requireAdmin();

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $eventDate = $_POST["event_date"] ?? "";
    $startTime = $_POST["start_time"] ?? "";
    $endTime = $_POST["end_time"] ?? "";
    $venue = trim($_POST["venue"] ?? "");
    $capacity = (int) ($_POST["capacity"] ?? 0);
    $status = $_POST["status"] ?? "active";

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

    } elseif ($status !== "active" && $status !== "archived") {

        $message = "Invalid event status.";
        $messageType = "danger";

    } else {

        try {

            $stmt = $pdo->prepare(
                "INSERT INTO events
                (
                    title,
                    description,
                    category,
                    event_date,
                    start_time,
                    end_time,
                    venue,
                    capacity,
                    status,
                    created_by
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $title,
                $description,
                $category,
                $eventDate,
                $startTime,
                $endTime,
                $venue,
                $capacity,
                $status,
                $_SESSION["user_id"]
            ]);

            $_SESSION["event_message"] = "Event created successfully.";
            $_SESSION["event_message_type"] = "success";

            header("Location: dashboard.php");
            exit;

        } catch (PDOException $e) {

            $message = "Unable to create the event. Please try again.";
            $messageType = "danger";
        }
    }
}

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
                Create New Event
            </h1>

            <p
                class="mb-0"
                style="
                    color: rgba(255,255,255,.86);
                    font-size: 17px;
                    max-width: 680px;
                "
            >
                Add a new campus activity, seminar, workshop,
                competition, or student event to the PTC
                Campus Event Hub.
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
                        Event Details
                    </h2>

                    <p class="text-muted mb-0">
                        Complete the information below to add
                        an event for students.
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
                                placeholder="Example: IT Innovation Seminar"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["title"] ?? ""
                                );
                                ?>"
                                required
                            >

                            <small class="text-muted">
                                Enter the official name of the event.
                            </small>

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
                                placeholder="Describe the event, its purpose, activities, and important information..."
                                required
                            ><?php
                            echo htmlspecialchars(
                                $_POST["description"] ?? ""
                            );
                            ?></textarea>

                            <small class="text-muted">
                                Provide enough information so students
                                understand what the event is about.
                            </small>

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
                                placeholder="Example: AVR 100"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["venue"] ?? ""
                                );
                                ?>"
                                required
                            >

                            <small class="text-muted">
                                Enter the building, room, laboratory,
                                gym, or other event location.
                            </small>

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
                                placeholder="Example: 100"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["capacity"] ?? ""
                                );
                                ?>"
                                required
                            >

                            <small class="text-muted">
                                Maximum number of students.
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
                                Active events are published immediately, while Archived events are hidden from public view.
                            </small>

                        </div>

                        <div class="col-md-6">

                            <label
                                class="form-label"
                            >
                                Event Created By
                            </label>

                            <div
                                class="form-control"
                                style="
                                    background: #f6f8f7;
                                    display: flex;
                                    align-items: center;
                                "
                            >

                                👤

                                <span class="ms-2">

                                    <?php
                                    echo htmlspecialchars(
                                        $_SESSION["full_name"]
                                        ?? "Administrator"
                                    );
                                    ?>

                                </span>

                            </div>

                            <small class="text-muted">
                                This event will be associated with
                                your administrator account.
                            </small>

                        </div>

                    </div>

                    <hr class="my-5">

                    <div
                        class="d-flex flex-wrap gap-2
                               justify-content-between
                               align-items-center"
                    >

                        <a
                            href="dashboard.php"
                            class="btn btn-outline-secondary"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary btn-lg px-4"
                        >
                            Create Event
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
                Event Publishing Reminder
            </h5>

            <p class="text-muted mb-0">
                Make sure the event title, schedule, venue,
                capacity, and description are accurate before
                creating the event. Students will see active
                events on the Campus Event Hub.
            </p>

        </div>

    </div>

</div>

<?php require "../includes/footer.php"; ?>