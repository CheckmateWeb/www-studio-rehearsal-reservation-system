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
            <h1 class="brand-logo-custom">STUDIO RESERVE</h1>
            <div>
                <span>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</span>
                <a href="javascript:void(0)" onclick="logout()" class="logout-link">Logout</a>
            </div>
        </div>
    </nav>

    <main class="dashboard-container">

        <div class="section-intro">
            <h2 class="main-title">Available Rooms</h2>
            <p class="main-subtitle">Book your rehearsal space quickly and easily</p>
        </div>

        <div class="rooms-grid">

            <div class="studio-card">
                <div class="studio-img">
                    <img src="https://drummingbase.com/wp-content/uploads/2022/07/snare-drum-2-2-2048x1000.jpg" alt="The Amp Room" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1a1a1a/bb86fc?text=The+Amp+Room';">
                </div>

                <div class="studio-info">
                    <h3 class="studio-name">The Amp Room</h3>
                    <p class="studio-desc">Equipped with Marshall stacks and Pearl drum kit.</p>
                    <p class="studio-price">Rate: 500.00 / hr</p>
                </div>

                <div class="studio-footer">
                    <button class="btn reserve-btn" onclick="reserveRoom(1, 'The Amp Room', 500)">
                        Reserve Now
                    </button>
                </div>
            </div>

            <div class="studio-card">
                <div class="studio-img">
                    <img src="https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&q=80&w=600" alt="The Synth Cave" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1a1a1a/bb86fc?text=The+Synth+Cave';">
                </div>

                <div class="studio-info">
                    <h3 class="studio-name">The Synth Cave</h3>
                    <p class="studio-desc">Professional keys and acoustic treatments.</p>
                    <p class="studio-price">Rate: 750.00 / hr</p>
                </div>

                <div class="studio-footer">
                    <button class="btn reserve-btn" onclick="reserveRoom(2, 'The Synth Cave', 750)">
                        Reserve Now
                    </button>
                </div>
            </div>

            <div class="studio-card">
                <div class="studio-img">
                    <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&q=80&w=600" alt="The Vocal Booth" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1a1a1a/bb86fc?text=The+Vocal+Booth';">
                </div>

                <div class="studio-info">
                    <h3 class="studio-name">The Vocal Booth</h3>
                    <p class="studio-desc">Isolated vocal recording room with acoustic foam treatments.</p>
                    <p class="studio-price">Rate: 600.00 / hr</p>
                </div>

                <div class="studio-footer">
                    <button class="btn reserve-btn" onclick="reserveRoom(3, 'The Vocal Booth', 600)">
                        Reserve Now
                    </button>
                </div>
            </div>

            <div class="studio-card">
                <div class="studio-img">
                    <img src="https://images.unsplash.com/photo-1459749411175-04bf5292ceea?auto=format&fit=crop&q=80&w=600" alt="The Bass Den" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1a1a1a/bb86fc?text=The+Bass+Den';">
                </div>

                <div class="studio-info">
                    <h3 class="studio-name">The Bass Den</h3>
                    <p class="studio-desc">Low-end friendly room with bass traps and professional monitoring.</p>
                    <p class="studio-price">Rate: 550.00 / hr</p>
                </div>

                <div class="studio-footer">
                    <button class="btn reserve-btn" onclick="reserveRoom(4, 'The Bass Den', 550)">
                        Reserve Now
                    </button>
                </div>
            </div>

        </div>

        <div class="table-section">
            <h2 class="table-title">Your Reservations</h2>

            <table class="reservation-table">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Date</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($reservations)): ?>
                        <?php foreach ($reservations as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['studio_name']) ?></td>
                                <td><?= date('F j, Y, g:i a', strtotime($r['reservation_date'])) ?></td>
                                <td class="highlight-text">₱<?= number_format($r['total_amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="empty-state">No reservations yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

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
        <p>© 2026 StudioRehearsal. All rights reserved.</p>
    </div>
</footer>

<script src="../scripts/service.js"></script>

</body>
</html>