<?php

require_once "../config/database.php";
require_once "../includes/auth.php";

requireAdmin();

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: dashboard.php");
    exit;
}

$eventId = (int) $_GET["id"];


/* GET EVENT */

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
        COUNT(
            CASE
                WHEN r.status = 'registered'
                THEN r.id
            END
        ) AS registered
     FROM events e
     LEFT JOIN registrations r
        ON e.id = r.event_id
     WHERE e.id = ?
     GROUP BY
        e.id,
        e.title,
        e.description,
        e.category,
        e.event_date,
        e.start_time,
        e.end_time,
        e.venue,
        e.capacity,
        e.status"
);

$stmt->execute([$eventId]);

$event = $stmt->fetch();

if (!$event) {
    header("Location: dashboard.php");
    exit;
}


/* DELETE ONLY AFTER CONFIRMATION */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        !isset($_POST["confirm_delete"]) ||
        $_POST["confirm_delete"] !== "yes"
    ) {
        header("Location: dashboard.php");
        exit;
    }

    try {

        $pdo->beginTransaction();


        /* DELETE REGISTRATIONS FIRST */

        $registrationStmt = $pdo->prepare(
            "DELETE FROM registrations
             WHERE event_id = ?"
        );

        $registrationStmt->execute([
            $eventId
        ]);


        /* DELETE EVENT */

        $eventStmt = $pdo->prepare(
            "DELETE FROM events
             WHERE id = ?"
        );

        $eventStmt->execute([
            $eventId
        ]);


        $pdo->commit();


        $_SESSION["event_message"] =
            "Event deleted successfully.";

        $_SESSION["event_message_type"] =
            "success";

        header("Location: dashboard.php");
        exit;

    } catch (PDOException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $errorMessage =
            "Unable to delete the event. "
            . "Please try again.";

    }

}

require "../includes/header.php";

?>


<!-- PAGE HEADER -->

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
                Delete Event
            </h1>

            <p
                class="mb-0"
                style="
                    color: rgba(255,255,255,.86);
                    font-size: 17px;
                    max-width: 680px;
                "
            >
                Review the event information carefully before
                permanently deleting this event.
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


<?php if (isset($errorMessage)): ?>

    <div class="alert alert-danger mb-4">

        <?php
        echo htmlspecialchars($errorMessage);
        ?>

    </div>

<?php endif; ?>


<!-- CONFIRMATION -->

<div class="row justify-content-center">

    <div class="col-xl-8">

        <div class="card border-0 shadow-sm">

            <div class="card-body p-4 p-md-5 text-center">

                <div
                    style="
                        width: 80px;
                        height: 80px;
                        margin: 0 auto 20px;
                        border-radius: 50%;
                        background: #fff1f1;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 38px;
                    "
                >
                    ⚠️
                </div>

                <span
                    class="badge bg-danger px-3 py-2 mb-3"
                >
                    PERMANENT ACTION
                </span>

                <h2 class="mb-3">
                    Are you sure you want to delete this event?
                </h2>

                <p class="text-muted mb-4">
                    This action cannot be undone. The event and
                    its student registration records will be
                    permanently removed.
                </p>


                <!-- EVENT DETAILS -->

                <div
                    class="text-start rounded-4 p-4 mb-4"
                    style="
                        background: #f6f8f7;
                        border: 1px solid #e4e9e6;
                    "
                >

                    <div class="row g-3">

                        <div class="col-md-8">

                            <small class="text-muted d-block">
                                Event
                            </small>

                            <h4 class="mb-0">

                                <?php
                                echo htmlspecialchars(
                                    $event["title"]
                                );
                                ?>

                            </h4>

                        </div>

                        <div class="col-md-4">

                            <small class="text-muted d-block">
                                Category
                            </small>

                            <span
                                class="badge bg-primary mt-1"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $event["category"]
                                );
                                ?>

                            </span>

                        </div>

                        <div class="col-md-6">

                            <small class="text-muted d-block">
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

                        <div class="col-md-6">

                            <small class="text-muted d-block">
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

                        <div class="col-md-6">

                            <small class="text-muted d-block">
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

                        <div class="col-md-6">

                            <small class="text-muted d-block">
                                Registrations
                            </small>

                            <strong>

                                👥

                                <?php
                                echo (int) $event["registered"];
                                ?>

                                students registered

                            </strong>

                        </div>

                    </div>

                </div>


                <!-- WARNING -->

                <div
                    class="alert alert-warning text-start"
                >

                    <strong>
                        Important:
                    </strong>

                    Deleting this event will also delete all
                    student registrations associated with it.

                </div>


                <!-- FORM -->

                <form
                    method="POST"
                    action=""
                >

                    <input
                        type="hidden"
                        name="confirm_delete"
                        value="yes"
                    >

                    <div
                        class="d-flex flex-wrap
                               justify-content-center
                               gap-2 mt-4"
                    >

                        <a
                            href="dashboard.php"
                            class="btn btn-outline-secondary btn-lg px-4"
                        >
                            Cancel
                        </a>

                        <a
                            href="../event-details.php?id=<?php echo $eventId; ?>"
                            class="btn btn-outline-primary btn-lg px-4"
                        >
                            View Event
                        </a>

                        <button
                            type="submit"
                            class="btn btn-danger btn-lg px-4"
                        >
                            Delete Permanently
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>


<!-- NOTICE -->

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
                Alternative: Archive the Event
            </h5>

            <p class="text-muted mb-0">
                If you do not want to permanently remove the
                event and its records, you can edit the event
                and change its status to
                <strong>Archived</strong> instead.
            </p>

        </div>

    </div>

</div>


<?php require "../includes/footer.php"; ?>