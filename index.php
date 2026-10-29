<?php

require_once "config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$search = trim($_GET["search"] ?? "");
$category = trim($_GET["category"] ?? "");

$sql = "SELECT e.*, 
               u.full_name AS organizer,
               (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id AND r.status = 'registered') AS registered_count
        FROM events e
        LEFT JOIN users u ON e.created_by = u.id
        WHERE 1=1";
$params = [];

if ($search !== "") {

    $sql .= " AND (
        e.title LIKE ?
        OR e.description LIKE ?
        OR e.category LIKE ?
        OR e.venue LIKE ?
    )";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
}

if ($category !== "") {

    $sql .= " AND e.category = ?";

    $params[] = $category;
}

$sql .= " ORDER BY
            e.event_date ASC,
            e.start_time ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$events = $stmt->fetchAll();


$categoryStmt = $pdo->query(
    "SELECT DISTINCT category
     FROM events
     WHERE category IS NOT NULL
     AND category != ''
     ORDER BY category ASC"
);

$categories = $categoryStmt->fetchAll();

require "includes/header.php";

?>

<section class="hero">

    <div>

        <span class="badge bg-warning text-dark mb-3 px-3 py-2">
            PATEROS TECHNOLOGICAL COLLEGE
        </span>

        <h1>
            Discover. Connect.<br>
            Be Part of Campus Life.
        </h1>

        <p>
            Explore seminars, workshops, student activities,
            competitions, and other exciting events happening
            around the PTC community.
        </p>

        <div class="d-flex flex-wrap gap-2">

            <a
                href="#events"
                class="btn btn-light btn-lg"
            >
                Explore Events
            </a>

            <?php if (!isset($_SESSION["user_id"])): ?>

                <a
                    href="register.php"
                    class="btn btn-outline-light btn-lg"
                >
                    Join PTC Events
                </a>

            <?php endif; ?>

        </div>

    </div>

</section>


<section class="mb-5">

    <div class="row align-items-center g-4">

        <div class="col-lg-8">

            <span
                class="text-uppercase fw-bold small"
                style="
                    color: #006b3c;
                    letter-spacing: 1px;
                "
            >
                Campus Event Hub
            </span>

            <h2 class="mt-2 mb-3">
                What's happening at PTC?
            </h2>

            <p class="text-muted mb-0">
                Stay updated with upcoming campus activities
                and find opportunities to learn, participate,
                connect, and grow with fellow students.
            </p>

        </div>

        <div class="col-lg-4">

            <div
                class="p-4 rounded-4"
                style="
                    background: #eaf6ef;
                    border-left: 5px solid #006b3c;
                "
            >

                <h5 class="mb-2">
                    🎓 Student Community
                </h5>

                <p class="text-muted mb-0">
                    Register for events and become an active
                    part of the campus community.
                </p>

            </div>

        </div>

    </div>

</section>


<section id="events">

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
                Upcoming Activities
            </span>

            <h2 class="mt-2 mb-1">
                Upcoming Events
            </h2>

            <p class="text-muted mb-0">

                <?php if ($category !== ""): ?>

                    Showing events in
                    <strong>
                        <?php echo htmlspecialchars($category); ?>
                    </strong>

                <?php elseif ($search !== ""): ?>

                    Showing results for
                    <strong>
                        <?php echo htmlspecialchars($search); ?>
                    </strong>

                <?php else: ?>

                    Find an event that interests you.

                <?php endif; ?>

            </p>

        </div>


        <?php if (count($events) > 0): ?>

            <span class="badge rounded-pill bg-primary px-3 py-2">

                <?php echo count($events); ?>

                Event<?php echo count($events) !== 1 ? "s" : ""; ?>

            </span>

        <?php endif; ?>

    </div>


    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body p-3">

            <form method="GET" action="index.php">

                <div class="row g-2">

                    <div class="col-lg-7">

                        <div class="input-group">

                            <span class="input-group-text bg-white">
                                🔎
                            </span>

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search events, categories, venues..."
                                value="<?php echo htmlspecialchars($search); ?>"
                            >

                        </div>

                    </div>


                    <div class="col-lg-3">

                        <select
                            name="category"
                            class="form-select"
                            onchange="this.form.submit()"
                        >

                            <option value="">
                                All Categories
                            </option>

                            <?php foreach ($categories as $cat): ?>

                                <?php
                                $categoryName = trim(
                                    $cat["category"]
                                );
                                ?>

                                <option
                                    value="<?php echo htmlspecialchars($categoryName); ?>"
                                    <?php
                                    echo $category === $categoryName
                                        ? "selected"
                                        : "";
                                    ?>
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $categoryName
                                    );
                                    ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="col-lg-2">

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Search
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <?php if ($category !== "" || $search !== ""): ?>

        <div
            class="d-flex flex-wrap align-items-center
                   justify-content-between gap-2 mb-4"
        >

            <div class="d-flex flex-wrap gap-2">

                <?php if ($category !== ""): ?>

                    <span
                        class="badge rounded-pill"
                        style="
                            background: #eaf6ef;
                            color: #006b3c;
                            padding: 9px 13px;
                        "
                    >
                        Category:
                        <?php echo htmlspecialchars($category); ?>
                    </span>

                <?php endif; ?>


                <?php if ($search !== ""): ?>

                    <span
                        class="badge rounded-pill"
                        style="
                            background: #f1f3f2;
                            color: #34443c;
                            padding: 9px 13px;
                        "
                    >
                        Search:
                        <?php echo htmlspecialchars($search); ?>
                    </span>

                <?php endif; ?>

            </div>


            <a
                href="index.php"
                class="btn btn-sm btn-outline-secondary"
            >
                Clear Filters
            </a>

        </div>

    <?php endif; ?>


    <?php if (count($events) > 0): ?>

        <div class="row g-4">

            <?php foreach ($events as $event): ?>

                <?php

                $registered = (int) ($event["registered_count"] ?? 0);
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

                ?>

                <div class="col-md-6 col-xl-4">

                    <article class="card event-card h-100">

                        <div
                            class="card-body d-flex flex-column"
                        >

                            <div
                                class="d-flex
                                       justify-content-between
                                       align-items-center
                                       mb-3"
                            >

                                <span class="badge bg-primary">

                                    <?php
                                    echo htmlspecialchars(
                                        $event["category"]
                                    );
                                    ?>

                                </span>


                                <?php if ($remaining > 0): ?>

                                    <small class="text-success fw-bold">
                                        ● Open
                                    </small>

                                <?php else: ?>

                                    <small class="text-danger fw-bold">
                                        ● Full
                                    </small>

                                <?php endif; ?>

                            </div>


                            <h4 class="card-title mb-3">

                                <?php
                                echo htmlspecialchars(
                                    $event["title"]
                                );
                                ?>

                            </h4>


                            <p class="card-text text-muted">

                                <?php

                                $description =
                                    $event["description"];

                                if (strlen($description) > 130) {

                                    $description =
                                        substr(
                                            $description,
                                            0,
                                            130
                                        ) . "...";
                                }

                                echo htmlspecialchars(
                                    $description
                                );

                                ?>

                            </p>


                            <div class="mt-2">

                                <div class="mb-2">

                                    <span class="event-date">
                                        📅
                                    </span>

                                    <?php
                                    echo date(
                                        "F d, Y",
                                        strtotime(
                                            $event["event_date"]
                                        )
                                    );
                                    ?>

                                </div>


                                <div class="mb-2">

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

                                </div>


                                <div class="mb-2">

                                    📍

                                    <?php
                                    echo htmlspecialchars(
                                        $event["venue"]
                                    );
                                    ?>

                                </div>


                                <div class="mb-3">

                                    👤

                                    <?php
                                    echo htmlspecialchars(
                                        $event["organizer"]
                                        ?? "PTC"
                                    );
                                    ?>

                                </div>

                            </div>


                            <div class="mb-3">

                                <div
                                    class="d-flex
                                           justify-content-between
                                           small mb-1"
                                >

                                    <span>
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
                                    style="height: 7px;"
                                >

                                    <div
                                        class="progress-bar bg-success"
                                        style="width: <?php echo $percentage; ?>%;"
                                    ></div>

                                </div>


                                <small class="text-muted">

                                    <?php echo $remaining; ?>

                                    slots remaining

                                </small>

                            </div>


                            <a
                                href="event-details.php?id=<?php echo $event["id"]; ?>"
                                class="btn btn-outline-primary mt-auto"
                            >
                                View Event Details →
                            </a>

                        </div>

                    </article>

                </div>

            <?php endforeach; ?>

        </div>


    <?php else: ?>


        <div class="card border-0 shadow-sm">

            <div class="card-body text-center py-5">

                <div style="font-size: 55px;">
                    🔎
                </div>


                <?php if ($category !== ""): ?>

                    <h4 class="mt-3">
                        No Events Found in This Category
                    </h4>

                    <p class="text-muted">
                        There are currently no upcoming events
                        under
                        <strong>
                            <?php echo htmlspecialchars($category); ?>
                        </strong>.
                    </p>

                <?php elseif ($search !== ""): ?>

                    <h4 class="mt-3">
                        No Events Found
                    </h4>

                    <p class="text-muted">
                        We couldn't find any upcoming events
                        matching your search.
                    </p>

                <?php else: ?>

                    <h4 class="mt-3">
                        No Upcoming Events
                    </h4>

                    <p class="text-muted">
                        There are currently no upcoming campus events.
                    </p>

                <?php endif; ?>


                <?php if ($search !== "" || $category !== ""): ?>

                    <a
                        href="index.php"
                        class="btn btn-primary"
                    >
                        View All Events
                    </a>

                <?php endif; ?>

            </div>

        </div>

    <?php endif; ?>

</section>


<?php if (!isset($_SESSION["user_id"])): ?>

    <section class="mt-5">

        <div
            class="p-5 rounded-4 text-center"
            style="
                background:
                    linear-gradient(
                        135deg,
                        #004d2b,
                        #006b3c
                    );
                color: white;
            "
        >

            <div style="font-size: 42px;">
                🎓
            </div>

            <h2 class="text-white mt-2">
                Be Part of the PTC Community
            </h2>

            <p
                class="mx-auto mb-4"
                style="
                    max-width: 650px;
                    color: rgba(255,255,255,.85);
                "
            >
                Create your account and start registering
                for campus events, seminars, workshops,
                competitions, and student activities.
            </p>

            <a
                href="register.php"
                class="btn btn-light btn-lg px-4"
            >
                Create Your Account
            </a>

        </div>

    </section>

<?php endif; ?>


<?php require "includes/footer.php"; ?>