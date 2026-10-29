<?php

require_once "config/database.php";
require_once "includes/auth.php";

requireLogin();

$userId = (int) $_SESSION["user_id"];

$stmt = $pdo->prepare(
    "SELECT
        r.id AS registration_id,
        r.status AS registration_status,
        r.registered_at,
        e.id AS event_id,
        e.title,
        e.description,
        e.category,
        e.event_date,
        e.start_time,
        e.end_time,
        e.venue,
        e.capacity
    FROM registrations r
    INNER JOIN events e
        ON r.event_id = e.id
    WHERE r.user_id = ?
    ORDER BY
        e.event_date ASC,
        e.start_time ASC"
);

$stmt->execute([$userId]);

$registrations = $stmt->fetchAll();

$registeredCount = 0;
$cancelledCount = 0;

foreach ($registrations as $registration) {

    if ($registration["registration_status"] === "registered") {
        $registeredCount++;
    }

    if ($registration["registration_status"] === "cancelled") {
        $cancelledCount++;
    }

}

require "includes/header.php";

?>

<?php if (isset($_SESSION["cancel_message"])): ?>

    <div
        class="alert alert-<?php echo htmlspecialchars($_SESSION["cancel_message_type"] ?? "info"); ?> alert-dismissible fade show shadow-sm"
        role="alert"
    >

        <div class="d-flex align-items-center gap-2">

            <?php if (($_SESSION["cancel_message_type"] ?? "") === "success"): ?>

                <span style="font-size: 20px;">✓</span>

            <?php elseif (($_SESSION["cancel_message_type"] ?? "") === "warning"): ?>

                <span style="font-size: 20px;">⚠️</span>

            <?php else: ?>

                <span style="font-size: 20px;">!</span>

            <?php endif; ?>

            <div>

                <?php
                echo htmlspecialchars(
                    $_SESSION["cancel_message"]
                );
                ?>

            </div>

        </div>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
        ></button>

    </div>

    <?php

    unset(
        $_SESSION["cancel_message"],
        $_SESSION["cancel_message_type"]
    );

    ?>

<?php endif; ?>


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
                STUDENT PORTAL
            </span>

            <h1
                class="text-white mb-2"
                style="
                    font-weight: 800;
                    font-size: clamp(34px, 5vw, 50px);
                "
            >
                My Registrations
            </h1>

            <p
                class="mb-0"
                style="
                    color: rgba(255,255,255,.86);
                    font-size: 17px;
                    max-width: 650px;
                "
            >
                Keep track of the campus events you have
                registered for and stay connected with
                the PTC community.
            </p>

        </div>


        <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">

            <div
                class="d-inline-block text-start p-3 rounded-3"
                style="
                    background: rgba(255,255,255,.10);
                    border: 1px solid rgba(255,255,255,.15);
                "
            >

                <small
                    style="color: rgba(255,255,255,.7);"
                >
                    Welcome
                </small>

                <div class="fw-bold fs-5">

                    <?php
                    echo htmlspecialchars(
                        $_SESSION["full_name"] ?? "Student"
                    );
                    ?>

                </div>

            </div>

        </div>

    </div>

</div>


<div class="row g-4 mb-5">

    <div class="col-md-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <div
                    class="d-flex justify-content-between
                           align-items-center"
                >

                    <div>

                        <small class="text-muted">
                            Total Registrations
                        </small>

                        <h2
                            class="mb-0 mt-1"
                            style="color: #006b3c;"
                        >
                            <?php
                            echo count($registrations);
                            ?>
                        </h2>

                    </div>

                    <div
                        style="
                            font-size: 35px;
                            background: #eaf6ef;
                            padding: 10px 15px;
                            border-radius: 12px;
                        "
                    >
                        🎟️
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <div
                    class="d-flex justify-content-between
                           align-items-center"
                >

                    <div>

                        <small class="text-muted">
                            Active Registrations
                        </small>

                        <h2
                            class="mb-0 mt-1 text-success"
                        >
                            <?php
                            echo $registeredCount;
                            ?>
                        </h2>

                    </div>

                    <div
                        style="
                            font-size: 35px;
                            background: #eaf6ef;
                            padding: 10px 15px;
                            border-radius: 12px;
                        "
                    >
                        ✓
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <div
                    class="d-flex justify-content-between
                           align-items-center"
                >

                    <div>

                        <small class="text-muted">
                            Cancelled
                        </small>

                        <h2
                            class="mb-0 mt-1 text-secondary"
                        >
                            <?php
                            echo $cancelledCount;
                            ?>
                        </h2>

                    </div>

                    <div
                        style="
                            font-size: 35px;
                            background: #f1f3f2;
                            padding: 10px 15px;
                            border-radius: 12px;
                        "
                    >
                        ↩
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<div
    class="d-flex flex-wrap justify-content-between
           align-items-end gap-3 mb-4"
>

    <div>

        <span
            class="text-uppercase fw-bold small"
            style="
                color: #006b3c;
                letter-spacing: 1px;
            "
        >
            Your Activities
        </span>

        <h2 class="mt-2 mb-1">
            Registered Events
        </h2>

        <p class="text-muted mb-0">
            View the events connected to your student account.
        </p>

    </div>

    <a
        href="index.php"
        class="btn btn-primary"
    >
        + Browse Events
    </a>

</div>


<?php if (count($registrations) > 0): ?>

    <div class="row g-4">

        <?php foreach ($registrations as $registration): ?>

            <?php

            $status =
                $registration["registration_status"];

            $eventStartTimestamp = strtotime(
                $registration["event_date"] . " " .
                $registration["start_time"]
            );

            $cancellationAllowed =
                $status === "registered" &&
                time() < $eventStartTimestamp;

            $uncancellationAllowed =
                $status === "cancelled" &&
                time() < $eventStartTimestamp;

            $eventIsPast =
                time() >= $eventStartTimestamp;

            ?>

            <div class="col-md-6 col-xl-4">

                <div
                    class="card event-card h-100"
                >

                    <div class="card-body d-flex flex-column">


                        <div
                            class="d-flex justify-content-between
                                   align-items-start mb-3"
                        >

                            <span
                                class="badge bg-primary"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $registration["category"]
                                );
                                ?>

                            </span>


                            <?php if ($status === "registered"): ?>

                                <span
                                    class="badge bg-success"
                                >
                                    Registered
                                </span>

                            <?php elseif ($status === "cancelled"): ?>

                                <span
                                    class="badge bg-secondary"
                                >
                                    Cancelled
                                </span>

                            <?php else: ?>

                                <span
                                    class="badge bg-warning text-dark"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        ucfirst($status)
                                    );
                                    ?>

                                </span>

                            <?php endif; ?>

                        </div>


                        <h4 class="card-title mb-3">

                            <?php
                            echo htmlspecialchars(
                                $registration["title"]
                            );
                            ?>

                        </h4>


                        <p class="text-muted">

                            <?php

                            $description =
                                $registration["description"];

                            if (
                                strlen($description) > 115
                            ) {

                                $description =
                                    substr(
                                        $description,
                                        0,
                                        115
                                    ) . "...";

                            }

                            echo htmlspecialchars(
                                $description
                            );

                            ?>

                        </p>


                        <div class="mt-2">

                            <p class="mb-2">

                                <span
                                    class="event-date"
                                >
                                    📅
                                </span>

                                <?php
                                echo date(
                                    "F d, Y",
                                    strtotime(
                                        $registration["event_date"]
                                    )
                                );
                                ?>

                            </p>


                            <p class="mb-2">

                                🕐

                                <?php
                                echo date(
                                    "h:i A",
                                    strtotime(
                                        $registration["start_time"]
                                    )
                                );
                                ?>

                                -

                                <?php
                                echo date(
                                    "h:i A",
                                    strtotime(
                                        $registration["end_time"]
                                    )
                                );
                                ?>

                            </p>


                            <p class="mb-2">

                                📍

                                <?php
                                echo htmlspecialchars(
                                    $registration["venue"]
                                );
                                ?>

                            </p>


                            <p class="mb-3">

                                📝 Registered:

                                <?php
                                echo date(
                                    "M d, Y",
                                    strtotime(
                                        $registration["registered_at"]
                                    )
                                );
                                ?>

                            </p>

                        </div>


                        <?php if ($eventIsPast): ?>

                            <div
                                class="alert alert-secondary py-2 small"
                            >
                                This event has already taken place.
                            </div>

                        <?php elseif ($status === "registered"): ?>

                            <div
                                class="alert alert-success py-2 small"
                            >
                                ✓ You are registered for this event.
                            </div>

                        <?php elseif ($status === "cancelled"): ?>

                            <div
                                class="alert alert-secondary py-2 small"
                            >
                                Your registration for this event
                                has been cancelled.
                            </div>

                        <?php endif; ?>


                        <div class="d-flex gap-2 mt-auto">

                            <a
                                href="event-details.php?id=<?php echo $registration["event_id"]; ?>"
                                class="btn btn-outline-primary flex-grow-1"
                            >
                                View Details
                            </a>


                            <?php if ($cancellationAllowed): ?>

                                <button
                                    type="button"
                                    class="btn btn-outline-danger flex-grow-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#cancelModal<?php echo $registration["registration_id"]; ?>"
                                >
                                    Cancel
                                </button>

                            <?php elseif ($uncancellationAllowed): ?>

                                <button
                                    type="button"
                                    class="btn btn-outline-success flex-grow-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#uncancelModal<?php echo $registration["registration_id"]; ?>"
                                >
                                    Register Again
                                </button>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>


            <?php if ($cancellationAllowed): ?>

                <div
                    class="modal fade"
                    id="cancelModal<?php echo $registration["registration_id"]; ?>"
                    tabindex="-1"
                    aria-labelledby="cancelModalLabel<?php echo $registration["registration_id"]; ?>"
                    aria-hidden="true"
                >

                    <div
                        class="modal-dialog modal-dialog-centered"
                    >

                        <div
                            class="modal-content border-0 shadow-lg rounded-4"
                        >

                            <div
                                class="modal-header border-0 px-4 pt-4"
                            >

                                <div>

                                    <span
                                        class="badge bg-danger mb-2"
                                    >
                                        CANCEL REGISTRATION
                                    </span>

                                    <h5
                                        class="modal-title fw-bold"
                                        id="cancelModalLabel<?php echo $registration["registration_id"]; ?>"
                                    >
                                        Cancel your registration?
                                    </h5>

                                </div>

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"
                                ></button>

                            </div>


                            <div class="modal-body px-4">

                                <p class="text-muted mb-3">
                                    Are you sure you want to cancel
                                    your registration for:
                                </p>

                                <div
                                    class="p-3 rounded-3"
                                    style="
                                        background: #eaf6ef;
                                        border-left: 4px solid #006b3c;
                                    "
                                >

                                    <div
                                        class="fw-bold"
                                        style="color: #004d2b;"
                                    >
                                        <?php
                                        echo htmlspecialchars(
                                            $registration["title"]
                                        );
                                        ?>
                                    </div>

                                    <small class="text-muted">

                                        📅

                                        <?php
                                        echo date(
                                            "F d, Y",
                                            strtotime(
                                                $registration["event_date"]
                                            )
                                        );
                                        ?>

                                    </small>

                                </div>

                                <p
                                    class="text-muted small mt-3 mb-0"
                                >
                                    Cancelling will free your
                                    registration slot for another
                                    student.
                                </p>

                            </div>


                            <div
                                class="modal-footer border-0 px-4 pb-4"
                            >

                                <button
                                    type="button"
                                    class="btn btn-light px-4"
                                    data-bs-dismiss="modal"
                                >
                                    Keep Registration
                                </button>


                                <form
                                    method="POST"
                                    action="cancel-registration.php"
                                >

                                    <input
                                        type="hidden"
                                        name="registration_id"
                                        value="<?php
                                            echo $registration["registration_id"];
                                        ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="cancel"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-danger px-4"
                                    >
                                        Yes, Cancel
                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endif; ?>


            <?php if ($uncancellationAllowed): ?>

                <div
                    class="modal fade"
                    id="uncancelModal<?php echo $registration["registration_id"]; ?>"
                    tabindex="-1"
                    aria-labelledby="uncancelModalLabel<?php echo $registration["registration_id"]; ?>"
                    aria-hidden="true"
                >

                    <div
                        class="modal-dialog modal-dialog-centered"
                    >

                        <div
                            class="modal-content border-0 shadow-lg rounded-4"
                        >

                            <div
                                class="modal-header border-0 px-4 pt-4"
                            >

                                <div>

                                    <span
                                        class="badge bg-success mb-2"
                                    >
                                        REGISTER AGAIN
                                    </span>

                                    <h5
                                        class="modal-title fw-bold"
                                        id="uncancelModalLabel<?php echo $registration["registration_id"]; ?>"
                                    >
                                        Register for this event again?
                                    </h5>

                                </div>

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"
                                ></button>

                            </div>


                            <div class="modal-body px-4">

                                <p class="text-muted mb-3">
                                    Your previous registration was
                                    cancelled. You can register again
                                    if there is still an available slot.
                                </p>

                                <div
                                    class="p-3 rounded-3"
                                    style="
                                        background: #eaf6ef;
                                        border-left: 4px solid #006b3c;
                                    "
                                >

                                    <div
                                        class="fw-bold"
                                        style="color: #004d2b;"
                                    >
                                        <?php
                                        echo htmlspecialchars(
                                            $registration["title"]
                                        );
                                        ?>
                                    </div>

                                    <small class="text-muted">

                                        📅

                                        <?php
                                        echo date(
                                            "F d, Y",
                                            strtotime(
                                                $registration["event_date"]
                                            )
                                        );
                                        ?>

                                    </small>

                                </div>

                                <p
                                    class="text-muted small mt-3 mb-0"
                                >
                                    Your registration will become
                                    active again if a slot is available.
                                </p>

                            </div>


                            <div
                                class="modal-footer border-0 px-4 pb-4"
                            >

                                <button
                                    type="button"
                                    class="btn btn-light px-4"
                                    data-bs-dismiss="modal"
                                >
                                    Not Now
                                </button>


                                <form
                                    method="POST"
                                    action="cancel-registration.php"
                                >

                                    <input
                                        type="hidden"
                                        name="registration_id"
                                        value="<?php
                                            echo $registration["registration_id"];
                                        ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="uncancel"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-success px-4"
                                    >
                                        Register Again
                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endif; ?>

        <?php endforeach; ?>

    </div>


<?php else: ?>


    <div class="card border-0 shadow-sm">

        <div class="card-body text-center py-5">

            <div
                style="
                    font-size: 65px;
                    margin-bottom: 15px;
                "
            >
                🎓
            </div>

            <h3>
                No Registrations Yet
            </h3>

            <p
                class="text-muted mx-auto mb-4"
                style="max-width: 550px;"
            >
                You haven't registered for any campus
                events yet. Explore the available events
                and find an activity that interests you.
            </p>

            <a
                href="index.php"
                class="btn btn-primary btn-lg"
            >
                Explore Campus Events
            </a>

        </div>

    </div>


<?php endif; ?>


<div
    class="mt-5 p-4 rounded-4"
    style="
        background: #eaf6ef;
        border: 1px solid #d7ebdf;
    "
>

    <div class="row align-items-center g-3">

        <div class="col-md-8">

            <h5 class="mb-1">
                Stay involved in campus life
            </h5>

            <p class="text-muted mb-0">
                Check the Campus Event Hub regularly for
                new seminars, workshops, competitions,
                and student activities.
            </p>

        </div>

        <div class="col-md-4 text-md-end">

            <a
                href="index.php"
                class="btn btn-outline-primary"
            >
                Discover More Events
            </a>

        </div>

    </div>

</div>


<?php require "includes/footer.php"; ?>