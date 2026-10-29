<?php

require_once "../config/database.php";
require_once "../includes/auth.php";

requireAdmin();

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: dashboard.php");
    exit;
}

$eventId = (int) $_GET["id"];


/* GET EVENT INFORMATION */

$eventStmt = $pdo->prepare(
    "SELECT
        e.id,
        e.title,
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
        e.category,
        e.event_date,
        e.start_time,
        e.end_time,
        e.venue,
        e.capacity,
        e.status"
);

$eventStmt->execute([$eventId]);

$event = $eventStmt->fetch();

if (!$event) {
    header("Location: dashboard.php");
    exit;
}


/* EXPORT ATTENDEES TO CSV */

if (isset($_GET["export"]) && $_GET["export"] === "csv") {

    $csvStmt = $pdo->prepare(
        "SELECT
            u.full_name,
            u.email,
            r.registered_at,
            r.status
         FROM registrations r
         INNER JOIN users u
            ON r.user_id = u.id
         WHERE r.event_id = ?
         AND r.status = 'registered'
         ORDER BY r.registered_at ASC"
    );

    $csvStmt->execute([$eventId]);

    $csvRegistrations = $csvStmt->fetchAll();

    $fileName =
        "attendees_" .
        preg_replace(
            "/[^A-Za-z0-9_-]/",
            "_",
            $event["title"]
        ) .
        ".csv";

    header("Content-Type: text/csv; charset=utf-8");
    header(
        "Content-Disposition: attachment; filename=\"" .
        $fileName .
        "\""
    );

    $output = fopen("php://output", "w");

    fputcsv(
        $output,
        [
            "No.",
            "Student Name",
            "Email",
            "Registered At",
            "Status"
        ]
    );

    $number = 1;

    foreach ($csvRegistrations as $registration) {

        fputcsv(
            $output,
            [
                $number++,
                $registration["full_name"],
                $registration["email"],
                $registration["registered_at"],
                ucfirst($registration["status"])
            ]
        );
    }

    fclose($output);

    exit;
}


/* GET REGISTRATIONS */

$registrationStmt = $pdo->prepare(
    "SELECT
        r.id,
        r.status,
        r.registered_at,
        u.full_name,
        u.email
     FROM registrations r
     INNER JOIN users u
        ON r.user_id = u.id
     WHERE r.event_id = ?
     ORDER BY r.registered_at ASC"
);

$registrationStmt->execute([$eventId]);

$registrations = $registrationStmt->fetchAll();


/* CALCULATE NUMBERS */

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
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(244,197,66,.12);
            right: -80px;
            top: -100px;
        "
    ></div>

    <div class="row align-items-center">

        <div class="col-lg-8">

            <span
                class="badge bg-warning text-dark px-3 py-2 mb-3"
            >
                REGISTRATION MANAGEMENT
            </span>

            <h1
                class="text-white mb-2"
                style="
                    font-weight: 800;
                    font-size: clamp(32px, 5vw, 48px);
                "
            >
                Event Registrations
            </h1>

            <p
                class="mb-0"
                style="
                    color: rgba(255,255,255,.86);
                    font-size: 17px;
                    max-width: 700px;
                "
            >
                Monitor student registrations and check
                the attendance capacity for this campus event.
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


<!-- EVENT INFORMATION -->

<div class="card border-0 shadow-sm mb-4">

    <div class="card-body p-4 p-md-5">

        <div class="row align-items-center g-4">

            <div class="col-lg-8">

                <div class="mb-2">

                    <span
                        class="badge bg-primary px-3 py-2"
                    >
                        <?php
                        echo htmlspecialchars(
                            $event["category"]
                        );
                        ?>
                    </span>

                    <?php if ($event["status"] === "upcoming"): ?>

                        <span
                            class="badge bg-success px-3 py-2 ms-1"
                        >
                            Upcoming
                        </span>

                    <?php else: ?>

                        <span
                            class="badge bg-secondary px-3 py-2 ms-1"
                        >
                            Archived
                        </span>

                    <?php endif; ?>

                </div>

                <h2 class="mt-3 mb-2">

                    <?php
                    echo htmlspecialchars(
                        $event["title"]
                    );
                    ?>

                </h2>

                <div class="text-muted">

                    <span class="me-3">

                        📅

                        <?php
                        echo date(
                            "F d, Y",
                            strtotime(
                                $event["event_date"]
                            )
                        );
                        ?>

                    </span>

                    <span class="me-3">

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

                    </span>

                    <span>

                        📍

                        <?php
                        echo htmlspecialchars(
                            $event["venue"]
                        );
                        ?>

                    </span>

                </div>

            </div>


            <div class="col-lg-4 text-lg-end">

                <a
                    href="../event-details.php?id=<?php echo $eventId; ?>"
                    class="btn btn-outline-primary"
                >
                    View Public Event
                </a>

                <a
                    href="edit-event.php?id=<?php echo $eventId; ?>"
                    class="btn btn-primary mt-2 mt-lg-0"
                >
                    Edit Event
                </a>

            </div>

        </div>

    </div>

</div>


<!-- STATISTICS -->

<div class="row g-4 mb-4">


    <!-- REGISTERED -->

    <div class="col-md-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <div
                    class="d-flex
                           justify-content-between
                           align-items-start"
                >

                    <div>

                        <small class="text-muted">
                            Total Registrations
                        </small>

                        <h2
                            class="mt-2 mb-1"
                            style="color: #006b3c;"
                        >
                            <?php echo $registered; ?>
                        </h2>

                        <small class="text-muted">
                            Confirmed registrations
                        </small>

                    </div>

                    <div
                        style="
                            font-size: 28px;
                            background: #eaf6ef;
                            padding: 10px 12px;
                            border-radius: 12px;
                        "
                    >
                        👥
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- CAPACITY -->

    <div class="col-md-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <div
                    class="d-flex
                           justify-content-between
                           align-items-start"
                >

                    <div>

                        <small class="text-muted">
                            Event Capacity
                        </small>

                        <h2
                            class="mt-2 mb-1"
                            style="color: #006b3c;"
                        >
                            <?php echo $capacity; ?>
                        </h2>

                        <small class="text-muted">
                            Maximum allowed attendees
                        </small>

                    </div>

                    <div
                        style="
                            font-size: 28px;
                            background: #eaf6ef;
                            padding: 10px 12px;
                            border-radius: 12px;
                        "
                    >
                        🎟️
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- REMAINING -->

    <div class="col-md-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <div
                    class="d-flex
                           justify-content-between
                           align-items-start"
                >

                    <div>

                        <small class="text-muted">
                            Remaining Slots
                        </small>

                        <h2
                            class="mt-2 mb-1"
                            style="color: #006b3c;"
                        >
                            <?php echo $remaining; ?>
                        </h2>

                        <small class="text-muted">
                            Capacity minus registrations
                        </small>

                    </div>

                    <div
                        style="
                            font-size: 28px;
                            background: #eaf6ef;
                            padding: 10px 12px;
                            border-radius: 12px;
                        "
                    >
                        🪑
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- CAPACITY PROGRESS -->

<div class="card border-0 shadow-sm mb-4">

    <div class="card-body p-4">

        <div
            class="d-flex
                   justify-content-between
                   align-items-center
                   mb-2"
        >

            <div>

                <strong>
                    Registration Capacity
                </strong>

                <small class="text-muted ms-2">
                    <?php echo $registered; ?>
                    /
                    <?php echo $capacity; ?>
                </small>

            </div>

            <strong
                style="color: #006b3c;"
            >
                <?php
                echo number_format(
                    $percentage,
                    0
                );
                ?>%
            </strong>

        </div>

        <div
            class="progress"
            style="height: 10px;"
        >

            <div
                class="progress-bar bg-success"
                role="progressbar"
                style="width: <?php echo $percentage; ?>%;"
                aria-valuenow="<?php echo $percentage; ?>"
                aria-valuemin="0"
                aria-valuemax="100"
            ></div>

        </div>

        <?php if ($remaining <= 0): ?>

            <div
                class="alert alert-danger mt-3 mb-0"
            >
                This event has reached maximum capacity.
            </div>

        <?php elseif ($remaining <= 5): ?>

            <div
                class="alert alert-warning mt-3 mb-0"
            >
                Only
                <strong>
                    <?php echo $remaining; ?>
                </strong>
                slot(s) remaining.
            </div>

        <?php else: ?>

            <small class="text-muted d-block mt-2">
                There are
                <strong>
                    <?php echo $remaining; ?>
                </strong>
                slots remaining for this event.
            </small>

        <?php endif; ?>

    </div>

</div>


<!-- ATTENDEE LIST -->

<div class="card border-0 shadow-sm">

    <div class="card-body p-4 p-md-5">

        <div
            class="d-flex
                   flex-wrap
                   justify-content-between
                   align-items-end
                   gap-3
                   mb-4"
        >

            <div>

                <span
                    class="text-uppercase fw-bold small"
                    style="
                        color: #006b3c;
                        letter-spacing: 1px;
                    "
                >
                    Student Records
                </span>

                <h2 class="mt-2 mb-1">
                    Attendee List
                </h2>

                <p class="text-muted mb-0">
                    Students currently registered for this event.
                </p>

            </div>

            <div
                class="d-flex
                       align-items-center
                       gap-2"
            >

                <span
                    class="badge bg-primary fs-6 px-3 py-2"
                >
                    <?php echo $registered; ?>
                    Registered
                </span>

                <?php if ($registered > 0): ?>

                    <a
                        href="registrations.php?id=<?php echo $eventId; ?>&export=csv"
                        class="btn btn-success"
                    >
                        ↓ Export CSV
                    </a>

                <?php endif; ?>

            </div>

        </div>


        <?php if (count($registrations) > 0): ?>

            <div class="table-responsive">

                <table
                    class="table table-hover align-middle"
                >

                    <thead>

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Student
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Registered At
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php
                        $number = 1;
                        ?>

                        <?php foreach ($registrations as $registration): ?>

                            <tr>

                                <td>

                                    <span
                                        class="fw-bold"
                                        style="
                                            color: #006b3c;
                                        "
                                    >
                                        <?php
                                        echo $number++;
                                        ?>
                                    </span>

                                </td>


                                <td>

                                    <div
                                        class="d-flex
                                               align-items-center
                                               gap-3"
                                    >

                                        <div
                                            style="
                                                width: 42px;
                                                height: 42px;
                                                border-radius: 50%;
                                                background: #eaf6ef;
                                                color: #006b3c;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                font-weight: 700;
                                            "
                                        >

                                            <?php

                                            $name =
                                                $registration[
                                                    "full_name"
                                                ];

                                            echo htmlspecialchars(
                                                strtoupper(
                                                    substr(
                                                        $name,
                                                        0,
                                                        1
                                                    )
                                                )
                                            );

                                            ?>

                                        </div>

                                        <div>

                                            <strong
                                                class="d-block"
                                            >
                                                <?php
                                                echo htmlspecialchars(
                                                    $registration[
                                                        "full_name"
                                                    ]
                                                );
                                                ?>
                                            </strong>

                                            <small
                                                class="text-muted"
                                            >
                                                Student
                                            </small>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $registration["email"]
                                    );
                                    ?>

                                </td>


                                <td>

                                    <strong>

                                        <?php
                                        echo date(
                                            "M d, Y",
                                            strtotime(
                                                $registration[
                                                    "registered_at"
                                                ]
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
                                                $registration[
                                                    "registered_at"
                                                ]
                                            )
                                        );
                                        ?>

                                    </small>

                                </td>


                                <td>

                                    <?php if (
                                        $registration["status"]
                                        === "registered"
                                    ): ?>

                                        <span
                                            class="badge bg-success px-3 py-2"
                                        >
                                            ✓ Registered
                                        </span>

                                    <?php elseif (
                                        $registration["status"]
                                        === "cancelled"
                                    ): ?>

                                        <span
                                            class="badge bg-secondary px-3 py-2"
                                        >
                                            Cancelled
                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="badge bg-warning text-dark px-3 py-2"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                ucfirst(
                                                    $registration[
                                                        "status"
                                                    ]
                                                )
                                            );
                                            ?>

                                        </span>

                                    <?php endif; ?>

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
                    👥
                </div>

                <h3>
                    No Registrations Yet
                </h3>

                <p
                    class="text-muted mx-auto mb-4"
                    style="max-width: 500px;"
                >
                    No students have registered for this event.
                    Once students register, their information
                    will appear here.
                </p>

                <a
                    href="../event-details.php?id=<?php echo $eventId; ?>"
                    class="btn btn-primary"
                >
                    View Public Event
                </a>

            </div>

        <?php endif; ?>

    </div>

</div>


<!-- FOOTER NOTE -->

<div
    class="mt-4 p-4 rounded-4"
    style="
        background: #eaf6ef;
        border: 1px solid #d7ebdf;
    "
>

    <div class="d-flex gap-3 align-items-start">

        <div style="font-size: 28px;">
            📊
        </div>

        <div>

            <h5 class="mb-1">
                Registration Monitoring
            </h5>

            <p class="text-muted mb-0">
                Use this page to monitor participation,
                check remaining capacity, review attendees,
                and export the current attendee list to CSV.
            </p>

        </div>

    </div>

</div>


<?php require "../includes/footer.php"; ?>