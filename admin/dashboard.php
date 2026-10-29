<?php

require_once "../config/database.php";
require_once "../includes/auth.php";

requireAdmin();

$stmt = $pdo->query(
    "SELECT
        e.id,
        e.title,
        e.category,
        e.event_date,
        e.start_time,
        e.venue,
        e.capacity,
        e.status,
        COUNT(r.id) AS registered
    FROM events e
    LEFT JOIN registrations r
        ON e.id = r.event_id
        AND r.status = 'registered'
    GROUP BY
        e.id,
        e.title,
        e.category,
        e.event_date,
        e.start_time,
        e.venue,
        e.capacity,
        e.status
    ORDER BY
        e.event_date ASC,
        e.start_time ASC"
);

$events = $stmt->fetchAll();

$totalEvents = count($events);
$totalRegistrations = 0;
$upcomingEvents = 0;
$archivedEvents = 0;

$today = date("Y-m-d");

foreach ($events as $event) {

    $totalRegistrations += (int) $event["registered"];

    if (
        $event["status"] === "upcoming" &&
        $event["event_date"] >= $today
    ) {
        $upcomingEvents++;
    }

    if ($event["status"] === "archived") {
        $archivedEvents++;
    }
}

$pendingStmt = $pdo->query(
    "SELECT COUNT(*) 
     FROM users
     WHERE status = 'pending'
     AND role IN ('student', 'admin')"
);

$pendingApplications = (int) $pendingStmt->fetchColumn();

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
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(244,197,66,.12);
            right: -90px;
            top: -110px;
        "
    ></div>

    <div class="row align-items-center">

        <div class="col-lg-8">

            <span
                class="badge bg-warning text-dark px-3 py-2 mb-3"
            >
                ADMINISTRATION
            </span>

            <h1
                class="text-white mb-2"
                style="
                    font-weight: 800;
                    font-size: clamp(34px, 5vw, 50px);
                "
            >
                Event Management
            </h1>

            <p
                class="mb-0"
                style="
                    color: rgba(255,255,255,.86);
                    font-size: 17px;
                    max-width: 680px;
                "
            >
                Manage PTC campus events, review account applications,
                and monitor student registrations.
            </p>

        </div>

        <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">

            <a
                href="add-event.php"
                class="btn btn-light btn-lg"
            >
                + Create New Event
            </a>

        </div>

    </div>

</div>


<div class="row g-4 mb-5">

    <div class="col-md-6 col-xl-3">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <small class="text-muted">
                    Total Events
                </small>

                <h2
                    class="mb-1 mt-2"
                    style="color: #006b3c;"
                >
                    <?php echo $totalEvents; ?>
                </h2>

                <small class="text-muted">
                    All created events
                </small>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-xl-3">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <small class="text-muted">
                    Upcoming Events
                </small>

                <h2
                    class="mb-1 mt-2"
                    style="color: #006b3c;"
                >
                    <?php echo $upcomingEvents; ?>
                </h2>

                <small class="text-muted">
                    Future activities
                </small>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-xl-3">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <small class="text-muted">
                    Registrations
                </small>

                <h2
                    class="mb-1 mt-2"
                    style="color: #006b3c;"
                >
                    <?php echo $totalRegistrations; ?>
                </h2>

                <small class="text-muted">
                    Student registrations
                </small>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-xl-3">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <small class="text-muted">
                    Pending Applications
                </small>

                <h2
                    class="mb-1 mt-2"
                    style="color: #d39e00;"
                >
                    <?php echo $pendingApplications; ?>
                </h2>

                <small class="text-muted">
                    Waiting for approval
                </small>

            </div>

        </div>

    </div>

</div>


<div class="card border-0 shadow-sm mb-5">

    <div class="card-body p-4 p-md-5">

        <div
            class="d-flex flex-wrap justify-content-between
                   align-items-center gap-3"
        >

            <div>

                <span
                    class="text-uppercase fw-bold small"
                    style="
                        color: #006b3c;
                        letter-spacing: 1px;
                    "
                >
                    Account Approval
                </span>

                <h2 class="mt-2 mb-1">
                    Pending Applications
                </h2>

                <p class="text-muted mb-0">
                    Review student and administrator account registrations.
                </p>

            </div>

            <a
                href="applications.php"
                class="btn btn-primary"
            >
                Review Applications
            </a>

        </div>

        <?php if ($pendingApplications > 0): ?>

            <div
                class="mt-4 p-3 rounded-3"
                style="
                    background: #fff8e1;
                    border: 1px solid #ffe69c;
                "
            >

                <strong>
                    <?php echo $pendingApplications; ?>
                    account<?php echo $pendingApplications > 1 ? "s" : ""; ?>
                </strong>

                waiting for administrator approval.

            </div>

        <?php else: ?>

            <div
                class="mt-4 p-3 rounded-3"
                style="
                    background: #eaf6ef;
                    border: 1px solid #d7ebdf;
                "
            >

                No pending account applications.

            </div>

        <?php endif; ?>

    </div>

</div>


<div class="card border-0 shadow-sm">

    <div class="card-body p-4 p-md-5">

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
                    Administration
                </span>

                <h2 class="mt-2 mb-1">
                    Event Management
                </h2>

                <p class="text-muted mb-0">
                    Create, update, monitor, or remove campus events.
                </p>

            </div>

            <a
                href="add-event.php"
                class="btn btn-primary"
            >
                + Add Event
            </a>

        </div>


        <?php if (count($events) > 0): ?>

            <div class="table-responsive">

                <table
                    class="table table-hover align-middle"
                >

                    <thead>

                        <tr>

                            <th>
                                Event
                            </th>

                            <th>
                                Category
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Venue
                            </th>

                            <th>
                                Capacity
                            </th>

                            <th>
                                Registrations
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($events as $event): ?>

                            <?php

                            $registered =
                                (int) $event["registered"];

                            $capacity =
                                (int) $event["capacity"];

                            $remaining =
                                max(
                                    0,
                                    $capacity - $registered
                                );

                            $percentage =
                                $capacity > 0
                                    ? min(
                                        100,
                                        (
                                            $registered /
                                            $capacity
                                        ) * 100
                                    )
                                    : 0;

                            ?>

                            <tr>

                                <td>

                                    <div
                                        style="
                                            min-width: 200px;
                                        "
                                    >

                                        <strong
                                            class="d-block"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $event["title"]
                                            );
                                            ?>

                                        </strong>

                                        <small
                                            class="text-muted"
                                        >

                                            <?php echo $remaining; ?>

                                            slots remaining

                                        </small>

                                    </div>

                                </td>


                                <td>

                                    <span
                                        class="badge bg-primary"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $event["category"]
                                        );
                                        ?>

                                    </span>

                                </td>


                                <td>

                                    <strong>

                                        <?php
                                        echo date(
                                            "M d, Y",
                                            strtotime(
                                                $event["event_date"]
                                            )
                                        );
                                        ?>

                                    </strong>

                                    <small
                                        class="text-muted d-block"
                                    >

                                        <?php
                                        echo date(
                                            "h:i A",
                                            strtotime(
                                                $event["start_time"]
                                            )
                                        );
                                        ?>

                                    </small>

                                </td>


                                <td>

                                    <span
                                        style="
                                            min-width: 130px;
                                            display: block;
                                        "
                                    >

                                        📍

                                        <?php
                                        echo htmlspecialchars(
                                            $event["venue"]
                                        );
                                        ?>

                                    </span>

                                </td>


                                <td>

                                    <?php echo $capacity; ?>

                                </td>


                                <td>

                                    <div
                                        style="
                                            min-width: 130px;
                                        "
                                    >

                                        <div
                                            class="d-flex
                                                   justify-content-between
                                                   small mb-1"
                                        >

                                            <span>
                                                <?php echo $registered; ?>
                                            </span>

                                            <span
                                                class="text-muted"
                                            >
                                                <?php echo $capacity; ?>
                                            </span>

                                        </div>

                                        <div
                                            class="progress"
                                            style="
                                                height: 6px;
                                            "
                                        >

                                            <div
                                                class="progress-bar bg-success"
                                                style="width: <?php echo $percentage; ?>%;"
                                            ></div>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <?php if ($event["status"] === "upcoming"): ?>

                                        <span
                                            class="badge bg-success"
                                        >
                                            Upcoming
                                        </span>

                                    <?php elseif ($event["status"] === "archived"): ?>

                                        <span
                                            class="badge bg-secondary"
                                        >
                                            Archived
                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="badge bg-warning text-dark"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                ucfirst(
                                                    $event["status"]
                                                )
                                            );
                                            ?>

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <div
                                        class="d-flex gap-1
                                               flex-wrap"
                                        style="
                                            min-width: 280px;
                                        "
                                    >

                                        <a
                                            href="../event-details.php?id=<?php echo $event["id"]; ?>"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            View
                                        </a>

                                        <a
                                            href="edit-event.php?id=<?php echo $event["id"]; ?>"
                                            class="btn btn-sm btn-outline-secondary"
                                        >
                                            Edit
                                        </a>

                                        <a
                                            href="registrations.php?id=<?php echo $event["id"]; ?>"
                                            class="btn btn-sm btn-outline-success"
                                        >
                                            Registrations
                                        </a>

                                        <a
                                            href="delete-event.php?id=<?php echo $event["id"]; ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="
                                                return confirm(
                                                    'Are you sure you want to delete this event?'
                                                );
                                            "
                                        >
                                            Delete
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="text-center py-5">

                <div
                    style="
                        font-size: 60px;
                        margin-bottom: 15px;
                    "
                >
                    📅
                </div>

                <h3>
                    No Events Yet
                </h3>

                <p
                    class="text-muted mx-auto mb-4"
                    style="max-width: 500px;"
                >
                    There are currently no campus events
                    in the system. Create your first event
                    to get started.
                </p>

                <a
                    href="add-event.php"
                    class="btn btn-primary btn-lg"
                >
                    + Create First Event
                </a>

            </div>

        <?php endif; ?>

    </div>

</div>


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
                PTC Campus Event Administration
            </h5>

            <p class="text-muted mb-0">
                Use the dashboard to manage events,
                approve accounts, and monitor student participation.
            </p>

        </div>

        <div class="col-md-4 text-md-end">

            <a
                href="../index.php"
                class="btn btn-outline-primary"
            >
                View Public Website
            </a>

        </div>

    </div>

</div>


<?php require "../includes/footer.php"; ?>