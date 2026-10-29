<?php

require_once "config/database.php";
require_once "includes/auth.php";

requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

if (!isset($_POST["event_id"]) || !is_numeric($_POST["event_id"])) {
    header("Location: index.php");
    exit;
}

$eventId = (int) $_POST["event_id"];
$userId = (int) $_SESSION["user_id"];

try {

    $pdo->beginTransaction();

    $eventStmt = $pdo->prepare(
        "SELECT id, title, capacity, status, event_date
         FROM events
         WHERE id = ?
         FOR UPDATE"
    );

    $eventStmt->execute([$eventId]);

    $event = $eventStmt->fetch();

    if (!$event) {

        $pdo->rollBack();

        $_SESSION["registration_message"] =
            "The event could not be found.";

        $_SESSION["registration_type"] = "danger";

        header("Location: index.php");
        exit;
    }

    if ($event["status"] !== "active" && $event["status"] !== "upcoming") {

        $pdo->rollBack();

        $_SESSION["registration_message"] =
            "Registration for this event is closed.";

        $_SESSION["registration_type"] = "warning";

        header(
            "Location: event-details.php?id=" . $eventId
        );
        exit;
    }

    if ($event["event_date"] < date("Y-m-d")) {

        $pdo->rollBack();

        $_SESSION["registration_message"] =
            "Registration for this event is closed because the event date has passed.";

        $_SESSION["registration_type"] = "warning";

        header(
            "Location: event-details.php?id=" . $eventId
        );
        exit;
    }

    $duplicateStmt = $pdo->prepare(
        "SELECT id
         FROM registrations
         WHERE user_id = ?
         AND event_id = ?
         AND status = 'registered'"
    );

    $duplicateStmt->execute([
        $userId,
        $eventId
    ]);

    if ($duplicateStmt->fetch()) {

        $pdo->rollBack();

        $_SESSION["registration_message"] =
            "You are already registered for this event.";

        $_SESSION["registration_type"] = "warning";

        header(
            "Location: event-details.php?id=" . $eventId
        );
        exit;
    }

    $countStmt = $pdo->prepare(
        "SELECT COUNT(*) AS total
         FROM registrations
         WHERE event_id = ?
         AND status = 'registered'"
    );

    $countStmt->execute([$eventId]);

    $registrationCount = (int) $countStmt->fetch()["total"];

    if ($registrationCount >= (int) $event["capacity"]) {

        $pdo->rollBack();

        $_SESSION["registration_message"] =
            "Sorry, this event has reached its maximum capacity.";

        $_SESSION["registration_type"] = "danger";

        header(
            "Location: event-details.php?id=" . $eventId
        );
        exit;
    }

    $insertStmt = $pdo->prepare(
        "INSERT INTO registrations
        (user_id, event_id, status, registered_at)
        VALUES (?, ?, 'registered', NOW())"
    );

    $insertStmt->execute([
        $userId,
        $eventId
    ]);

    $pdo->commit();

    $_SESSION["registration_message"] =
        "Registration successful! You are registered for " .
        $event["title"] . ".";

    $_SESSION["registration_type"] = "success";

    header(
        "Location: event-details.php?id=" . $eventId
    );
    exit;

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION["registration_message"] =
        "Something went wrong while processing your registration.";

    $_SESSION["registration_type"] = "danger";

    header(
        "Location: event-details.php?id=" . $eventId
    );
    exit;
}
?>