<?php
session_start();
require_once __DIR__ . "/../bl/user_manager.php";
require_once __DIR__ . "/../model/databaseCon.php";
require_once __DIR__ . '/../helper/send.php';

$manager = new UserManager();

if (isset($_POST['action']) && $_POST['action'] == 'login') {
    ob_clean();
    $manager->loginUserFunc($_POST['email'], $_POST['password']);
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'register') {
    ob_clean();

    $f = trim($_POST['fName']);
    $l = trim($_POST['lName']);
    $e = trim($_POST['email']);
    $p = $_POST['password'];

    if ($manager->getUserByEmail($e)) {
        echo "Email already exists";
        exit;
    }

    $created = $manager->userModel->createUserFull($f, $l, $e, $p);

    if ($created) {
        $body = '
        <div style="margin:0;padding:24px;background-color:#f4f4f7;font-family:Arial,sans-serif;">
            <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,0.08);border:1px solid #e5e7eb;">
                
                <div style="background:linear-gradient(135deg,#7c3aed,#a855f7);padding:24px 30px;text-align:center;">
                    <h2 style="margin:0;color:#ffffff;font-size:28px;">New User Registration</h2>
                    <p style="margin:8px 0 0;color:#f3e8ff;font-size:14px;">Studio Rehearsal Notification</p>
                </div>

                <div style="padding:30px;">
                    <p style="margin:0 0 20px;font-size:16px;color:#374151;">
                        A new user has successfully registered on your website.
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:20px;">
                        <p style="margin:0 0 12px;font-size:15px;color:#111827;">
                            <strong style="color:#7c3aed;">Full Name:</strong> ' . htmlspecialchars($f . ' ' . $l) . '
                        </p>
                        <p style="margin:0;font-size:15px;color:#111827;">
                            <strong style="color:#7c3aed;">Email Address:</strong> ' . htmlspecialchars($e) . '
                        </p>
                    </div>

                    <div style="margin-top:24px;padding:16px;background:#f3e8ff;border-left:4px solid #7c3aed;border-radius:10px;">
                        <p style="margin:0;font-size:14px;color:#4b5563;">
                            This is an automated message from your Studio Rehearsal system.
                        </p>
                    </div>
                </div>

                <div style="padding:18px 30px;background:#111827;text-align:center;">
                    <p style="margin:0;font-size:13px;color:#d1d5db;">
                        © 2026 Studio Rehearsal. All rights reserved.
                    </p>
                </div>
            </div>
        </div>';

        $mailResult = sendEmail(
            "adventureprofile2@gmail.com",
            "Admin",
            "New User Registration",
            $body
        );

        echo "success";
    } else {
        echo "error";
    }

    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'reserve') {
    if (!isset($_SESSION['user_id'])) {
        echo "Not logged in";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $studio_id = $_POST['studio_id'];
    $price = $_POST['price'];

    $dateNow = date('Y-m-d H:i:s');
    $start_time = $dateNow;
    $end_time = date('Y-m-d H:i:s', strtotime($dateNow . ' +1 hour'));

    $database = new DatabaseCon();
    $conn = $database->connectDB();

    $query = "INSERT INTO tbl_reservations_table
              (user_id, studio_id, reservation_date, start_time, end_time, total_amount, createdAt, updatedAt)
              VALUES
              (:user_id, :studio_id, :reservation_date, :start_time, :end_time, :total_amount, :createdAt, :updatedAt)";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':studio_id', $studio_id);
    $stmt->bindParam(':reservation_date', $dateNow);
    $stmt->bindParam(':start_time', $start_time);
    $stmt->bindParam(':end_time', $end_time);
    $stmt->bindParam(':total_amount', $price);
    $stmt->bindParam(':createdAt', $dateNow);
    $stmt->bindParam(':updatedAt', $dateNow);

    echo $stmt->execute() ? "success" : "error";
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'logout') {
    session_unset();
    session_destroy();
    echo "success";
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'contact') {

    $firstName = isset($_POST["fName"]) ? trim($_POST["fName"]) : "";
    $lastName = isset($_POST["lName"]) ? trim($_POST["lName"]) : "";
    $fullName = trim($firstName . " " . $lastName);

    $email = isset($_POST["email"]) ? filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL) : false;
    $message = isset($_POST["message"]) ? htmlspecialchars(trim($_POST["message"])) : "";

    if ($fullName === "" || !$email || $message === "") {
        echo "Invalid input";
        exit;
    }

    $body = '
    <div style="margin:0;padding:24px;background-color:#f4f4f7;font-family:Arial,sans-serif;">
        <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,0.08);border:1px solid #e5e7eb;">
            
            <div style="background:linear-gradient(135deg,#7c3aed,#a855f7);padding:24px 30px;text-align:center;">
                <h2 style="margin:0;color:#ffffff;font-size:28px;">New Contact Message</h2>
                <p style="margin:8px 0 0;color:#f3e8ff;font-size:14px;">Studio Rehearsal Contact Form</p>
            </div>

            <div style="padding:30px;">
                <p style="margin:0 0 20px;font-size:16px;color:#374151;">
                    You received a new message from your website contact form.
                </p>

                <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:20px;">
                    <p style="margin:0 0 12px;font-size:15px;color:#111827;">
                        <strong style="color:#7c3aed;">Full Name:</strong> ' . htmlspecialchars($fullName) . '
                    </p>
                    <p style="margin:0 0 12px;font-size:15px;color:#111827;">
                        <strong style="color:#7c3aed;">Email Address:</strong> ' . htmlspecialchars($email) . '
                    </p>
                    <p style="margin:0;font-size:15px;color:#111827;">
                        <strong style="color:#7c3aed;">Message:</strong><br><br>' . nl2br($message) . '
                    </p>
                </div>

                <div style="margin-top:24px;padding:16px;background:#f3e8ff;border-left:4px solid #7c3aed;border-radius:10px;">
                    <p style="margin:0;font-size:14px;color:#4b5563;">
                        This is an automated message from your Studio Rehearsal system.
                    </p>
                </div>
            </div>

            <div style="padding:18px 30px;background:#111827;text-align:center;">
                <p style="margin:0;font-size:13px;color:#d1d5db;">
                    © 2026 Studio Rehearsal. All rights reserved.
                </p>
            </div>
        </div>
    </div>';

    $result = sendEmail(
        "adventureprofile2@gmail.com",
        "Admin",
        "Contact Form Submission",
        $body
    );

    if ($result === true) {
        echo "success";
    } else {
        echo "Failed: " . $result;
    }

    exit();
}

if (isset($_POST["fName"], $_POST["lName"]) && !isset($_POST["uID"])) {
    $manager->addUserFunc($_POST["fName"], $_POST["lName"]);
    exit;
}
else if (isset($_POST["fName"], $_POST["lName"], $_POST["uID"], $_POST["email"])) {
    $manager->updateUserFunc(
        $_POST["uID"],
        $_POST["fName"],
        $_POST["lName"],
        $_POST["email"]
    );
    exit;
}
else if (isset($_POST["fName"], $_POST["lName"], $_POST["uID"])) {
    $manager->updateUserFunc($_POST["uID"], $_POST["fName"], $_POST["lName"], "");
    exit;
}
else if (isset($_POST["dID"])) {
    $manager->deleteUserFunc($_POST["dID"]);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] == 'updateReservationStatus') {
    $reservationID = $_POST['reservationID'];
    $statusID = $_POST['statusID'];
    
    $manager->updateReservationStatus($reservationID, $statusID);
    exit;
}

if (isset($_POST['userID']) && isset($_POST['action']) && $_POST['action'] == 'deleteUser') {
    $manager->deleteUserFunc($_POST['userID']);
    exit;
}
?>