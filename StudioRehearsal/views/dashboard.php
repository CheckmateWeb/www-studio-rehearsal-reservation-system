<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../bl/user_manager.php";
$manager = new UserManager();
$reservations = $manager->getMyReservations();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio Dashboard</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="../Designs/style.css">
</head>

<body>

<div class="main-content">

    <nav class="custom-nav">
        <div class="nav-container">
            <div class="brand-block">
                <h1 class="brand-logo-custom">StudioReserve</h1>
                <span class="brand-caption">Cleaner rehearsal booking experience</span>
            </div>
            <div class="nav-actions">
                <span class="welcome-badge">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></span>
                <a href="javascript:void(0)" onclick="logout()" class="logout-link" aria-label="Log out of your account">Logout</a>
            </div>
        </div>
    </nav>

    <main class="dashboard-container">
        <section class="hero-panel">
            <div class="hero-grid">
                <div>
                    <p class="section-kicker">Dashboard</p>
                    <h2 class="main-title">Reserve the right room without the clutter.</h2>
                    <p class="main-subtitle">Browse studio spaces, confirm your booking through the existing popup flow, and review your reservation history in one clean layout.</p>
                </div>

                <div class="hero-stats">
                    <div class="mini-stat">
                        <span class="mini-stat-label">Account</span>
                        <span class="mini-stat-value"><?= htmlspecialchars($_SESSION['name']) ?></span>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Reservations</span>
                        <span class="mini-stat-value"><?= count($reservations) ?> active records</span>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Booking access</span>
                        <span class="mini-stat-value">Reserve and manage your sessions here</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-block">
            <div class="section-title-row">
                <div class="section-intro">
                    <h2 class="section-title">Available Rooms</h2>
                    <p class="section-note">Choose the space that fits your setup, then reserve it directly.</p>
                </div>
            </div>

            <div class="rooms-grid">

                <div class="studio-card">
                    <div class="studio-img">
                        <img src="https://drummingbase.com/wp-content/uploads/2022/07/snare-drum-2-2-2048x1000.jpg" alt="The Amp Room" loading="lazy" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1a1a1a/bb86fc?text=The+Amp+Room';">
                    </div>

                    <div class="studio-info">
                        <span class="studio-tag">Band Setup</span>
                        <h3 class="studio-name">The Amp Room</h3>
                        <p class="studio-desc">Equipped with Marshall stacks and Pearl drum kit.</p>
                        <p class="studio-price">Rate: &#8369;500.00 / hr</p>
                    </div>

                    <div class="studio-footer">
                        <button type="button" class="btn reserve-btn" onclick="reserveRoom(1, 'The Amp Room', 500)">
                            Reserve Now
                        </button>
                    </div>
                </div>

                <div class="studio-card">
                    <div class="studio-img">
                        <img src="https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&q=80&w=600" alt="The Synth Cave" loading="lazy" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1a1a1a/bb86fc?text=The+Synth+Cave';">
                    </div>

                    <div class="studio-info">
                        <span class="studio-tag">Keys Focus</span>
                        <h3 class="studio-name">The Synth Cave</h3>
                        <p class="studio-desc">Professional keys and acoustic treatments.</p>
                        <p class="studio-price">Rate: &#8369;750.00 / hr</p>
                    </div>

                    <div class="studio-footer">
                        <button type="button" class="btn reserve-btn" onclick="reserveRoom(2, 'The Synth Cave', 750)">
                            Reserve Now
                        </button>
                    </div>
                </div>

                <div class="studio-card">
                    <div class="studio-img">
                        <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&q=80&w=600" alt="The Vocal Booth" loading="lazy" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1a1a1a/bb86fc?text=The+Vocal+Booth';">
                    </div>

                    <div class="studio-info">
                        <span class="studio-tag">Recording</span>
                        <h3 class="studio-name">The Vocal Booth</h3>
                        <p class="studio-desc">Isolated vocal recording room with acoustic foam treatments.</p>
                        <p class="studio-price">Rate: &#8369;600.00 / hr</p>
                    </div>

                    <div class="studio-footer">
                        <button type="button" class="btn reserve-btn" onclick="reserveRoom(3, 'The Vocal Booth', 600)">
                            Reserve Now
                        </button>
                    </div>
                </div>

                <div class="studio-card">
                    <div class="studio-img">
                        <img src="https://images.unsplash.com/photo-1459749411175-04bf5292ceea?auto=format&fit=crop&q=80&w=600" alt="The Bass Den" loading="lazy" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1a1a1a/bb86fc?text=The+Bass+Den';">
                    </div>

                    <div class="studio-info">
                        <span class="studio-tag">Low End</span>
                        <h3 class="studio-name">The Bass Den</h3>
                        <p class="studio-desc">Low-end friendly room with bass traps and professional monitoring.</p>
                        <p class="studio-price">Rate: &#8369;550.00 / hr</p>
                    </div>

                    <div class="studio-footer">
                        <button type="button" class="btn reserve-btn" onclick="reserveRoom(4, 'The Bass Den', 550)">
                            Reserve Now
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="table-section">
            <div class="section-title-row">
                <div>
                    <h2 class="table-title">Your Reservations</h2>
                    <p class="section-note">A clean summary of your latest bookings.</p>
                </div>
            </div>

            <div class="table-panel">
                <div class="table-wrapper">
                    <table class="reservation-table">
                        <thead>
                            <tr>
                                <th scope="col">Room</th>
                                <th scope="col">Date</th>
                                <th scope="col">Total</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($reservations)): ?>
                                <?php foreach ($reservations as $r): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['studio_name']) ?></td>
                                        <td><?= date('F j, Y, g:i a', strtotime($r['reservation_date'])) ?></td>
                                        <td class="highlight-text">&#8369;<?= number_format($r['total_amount'], 2) ?></td>
                                        <td class="actions-cell">
                                            <button
                                                type="button"
                                                class="btn action-btn delete-btn"
                                                onclick="cancelReservation('<?= htmlspecialchars($r['reservation_id'], ENT_QUOTES) ?>', '<?= htmlspecialchars($r['studio_name'], ENT_QUOTES) ?>')">
                                                Cancel
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="empty-state">No reservations yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

</div>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-left">
            <h2>StudioRehearsal</h2>
            <p>Your space. Your sound. Your moment.</p>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2026 StudioRehearsal. All rights reserved.</p>
    </div>
</footer>

<script src="../scripts/service.js"></script>
<script>
    function cancelReservation(reservationID, roomName) {
        Swal.fire({
            title: 'Cancel Reservation?',
            text: 'This will cancel your reservation for ' + roomName + '.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, cancel it',
            cancelButtonText: 'Keep it'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: "../controllers/controller.php",
                type: "POST",
                data: {
                    action: "cancelReservation",
                    reservationID: reservationID
                },
                success: function(response) {
                    if (isSuccessfulTextResponse(response, ["reservation cancelled successfully"])) {
                        Swal.fire("Cancelled!", "Your reservation has been cancelled.", "success")
                            .then(() => location.reload(true));
                    } else {
                        Swal.fire("Error", response || "Unable to cancel reservation", "error");
                    }
                },
                error: function(xhr) {
                    Swal.fire("Error", xhr.status + " - " + xhr.responseText, "error");
                }
            });
        });
    }
</script>

</body>
</html>
