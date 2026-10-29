<?php

require_once "config/database.php";
require_once "includes/auth.php";

requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: my-registrations.php");
    exit;
}

$userId = (int) $_SESSION["user_id"];
$registrationId = (int) ($_POST["registration_id"] ?? 0);
$action = $_POST["action"] ?? "cancel";

if ($registrationId <= 0) {
    $_SESSION["cancel_message"] = "Invalid registration.";
    $_SESSION["cancel_message_type"] = "danger";

    header("Location: my-registrations.php");
    exit;
}

if (!in_array($action, ["cancel", "uncancel"], true)) {
    $_SESSION["cancel_message"] = "Invalid registration action.";
    $_SESSION["cancel_message_type"] = "danger";

    header("Location: my-registrations.php");
    exit;
}

try {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        "SELECT
            r.id,
            r.user_id,
            r.event_id,
            r.status,
            e.event_date,
            e.start_time,
            e.capacity
        FROM registrations r
        INNER JOIN events e
            ON r.event_id = e.id
        WHERE r.id = ?
        AND r.user_id = ?
        FOR UPDATE"
    );

    $stmt->execute([
        $registrationId,
        $userId
    ]);

    $registration = $stmt->fetch();

    if (!$registration) {

        $pdo->rollBack();

        $_SESSION["cancel_message"] =
            "Registration not found.";

        $_SESSION["cancel_message_type"] =
            "danger";

        header("Location: my-registrations.php");
        exit;
    }


    $eventStartTimestamp = strtotime(
        $registration["event_date"] . " " .
        $registration["start_time"]
    );


    if (time() >= $eventStartTimestamp) {

        $pdo->rollBack();

        if ($action === "cancel") {

            $_SESSION["cancel_message"] =
                "Cancellation is no longer allowed because the event has already started.";

        } else {

            $_SESSION["cancel_message"] =
                "You can no longer register for this event because it has already started.";

        }

        $_SESSION["cancel_message_type"] =
            "warning";

        header("Location: my-registrations.php");
        exit;
    }


    if ($action === "cancel") {

        if ($registration["status"] !== "registered") {

            $pdo->rollBack();

            $_SESSION["cancel_message"] =
                "This registration is already cancelled.";

            $_SESSION["cancel_message_type"] =
                "warning";

            header("Location: my-registrations.php");
            exit;
        }


        $update = $pdo->prepare(
            "UPDATE registrations
             SET status = 'cancelled'
             WHERE id = ?
             AND user_id = ?
             AND status = 'registered'"
        );

        $update->execute([
            $registrationId,
            $userId
        ]);


        $pdo->commit();


        $_SESSION["cancel_message"] =
            "Your event registration has been cancelled successfully.";

        $_SESSION["cancel_message_type"] =
            "success";

        header("Location: my-registrations.php");
        exit;
    }


    if ($action === "uncancel") {

        if ($registration["status"] !== "cancelled") {

            $pdo->rollBack();

            $_SESSION["cancel_message"] =
                "This registration is already active.";

            $_SESSION["cancel_message_type"] =
                "warning";

            header("Location: my-registrations.php");
            exit;
        }


        $countStmt = $pdo->prepare(
            "SELECT COUNT(*)
             FROM registrations
             WHERE event_id = ?
             AND status = 'registered'
             FOR UPDATE"
        );

        $countStmt->execute([
            $registration["event_id"]
        ]);

        $registeredCount = (int) $countStmt->fetchColumn();


        if ($registeredCount >= (int) $registration["capacity"]) {

            $pdo->rollBack();

            $_SESSION["cancel_message"] =
                "Registration failed because this event is already full.";

            $_SESSION["cancel_message_type"] =
                "warning";

            header("Location: my-registrations.php");
            exit;
        }


        $update = $pdo->prepare(
            "UPDATE registrations
             SET status = 'registered'
             WHERE id = ?
             AND user_id = ?
             AND status = 'cancelled'"
        );

        $update->execute([
            $registrationId,
            $userId
        ]);


        $pdo->commit();


        $_SESSION["cancel_message"] =
            "You have successfully registered for the event again.";

        $_SESSION["cancel_message_type"] =
            "success";

        header("Location: my-registrations.php");
        exit;
    }


} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION["cancel_message"] =
        "Something went wrong while processing your registration.";

    $_SESSION["cancel_message_type"] =
        "danger";

    header("Location: my-registrations.php");
    exit;
}