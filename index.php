<?php
// Load the necessary files
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

// Use the required classes
use App\Models\Box;
use App\Services\PrayerTimeService;

// Set the timezone to Malaysia/Singapore (GMT+8)
date_default_timezone_set('Asia/Kuala_Lumpur');

// Get the selected box ID from the query parameter, default to the first box if not provided
$selectedBoxId = $_GET['box_id'] ?? null;
if (!$selectedBoxId) {
    $boxes = Box::getAllBoxes();
    if (!empty($boxes)) {
        $selectedBoxId = $boxes[0]->getId();
    }
}

// Get the prayer times for the selected box
$prayerTimes = [];
if ($selectedBoxId) {
    $prayerTimeService = new PrayerTimeService();
    $prayerTimes = $prayerTimeService->getPrayerTimesForBox($selectedBoxId);
    $selectedBox = Box::findById($selectedBoxId);
}

// Get today's prayer times
$today = date('Y-m-d');
$todayPrayerTimes = $prayerTimes[$today] ?? [];

// Determine the next prayer (excluding Imsak and Syuruk)
$currentDateTime = new DateTime();
$nextPrayer = null;
$nextPrayerTime = null;

$excludedPrayers = ['Imsak', 'Syuruk'];

foreach ($todayPrayerTimes as $prayer => $time) {
    if (in_array($prayer, $excludedPrayers)) {
        continue;
    }

    // Ensure the time format is correct, e.g., '05:42 AM' should be converted to '05:42'
    $formattedTime = date('H:i', strtotime($time));
    $prayerDateTime = DateTime::createFromFormat('H:i', $formattedTime);

    if ($prayerDateTime === false) {
        // Log error or handle the case where the date could not be created
        error_log("Failed to create DateTime object from time: '$formattedTime'");
        continue;
    }

    if ($prayerDateTime > $currentDateTime) {
        $nextPrayer = $prayer;
        $nextPrayerTime =  $formattedTime;
        break;
    }
}

// If no next prayer found, get the first prayer of the next day (excluding Imsak and Syuruk)
if ($nextPrayer === null) {
    $nextDay = date('Y-m-d', strtotime($today . ' +1 day'));
    $nextDayPrayerTimes = $prayerTimes[$nextDay] ?? [];

    foreach ($nextDayPrayerTimes as $prayer => $time) {
        if (in_array($prayer, $excludedPrayers)) {
            continue;
        }

        $formattedTime = date('H:i', strtotime($time));
        $nextPrayer = $prayer;
        $nextPrayerTime = $formattedTime;
        break;
    }
}

// Calculate the remaining time until the next prayer
$remainingTime = 0;
if ($nextPrayer !== null) {
    $nextPrayerDateTime = DateTime::createFromFormat('Y-m-d H:i', $today . ' ' . $nextPrayerTime);

    if ($nextPrayerDateTime < $currentDateTime) {
        $nextPrayerDateTime->modify('+1 day');
    }

    if ($nextPrayerDateTime) {
        $remainingTime = $nextPrayerDateTime->getTimestamp() - $currentDateTime->getTimestamp();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Express In Music - Prayer Times</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f4f4f4;
            height: 100vh;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .timer {
            font-size: 2rem;
            /* Adjusted for better visibility on mobile */
        }

        .card-title,
        .card-subtitle {
            text-transform: capitalize;
        }

        select.custom-select {
            width: 100%;
            /* Full width */
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            border: 1px solid #ced4da;
        }

        .blink {
            animation: blinker 1s linear infinite;
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <div class="text-center mb-4">
            <h1>Express In Music - Prayer Times</h1>
            <h2><?php echo date('d M Y'); ?></h2>
            <p>Waktu Solat Seterusnya</p>
        </div>

        <?php if ($nextPrayer !== null && $nextPrayerTime !== null) : ?>
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h3 class="card-title"><?php echo $nextPrayer; ?></h3>
                    <h4 class="card-subtitle mb-2 text-muted"><?php echo date('h:i A', strtotime($nextPrayerTime)); ?></h4>
                    <div id="timer" class="timer"></div>
                    <p class="card-text">Sehingga Waktu Solat Seterusnya Di Zon</p>
                    <h5 class="card-text"><?php echo $selectedBox->getPrayerZone() . ' - ' . $selectedBox->getName(); ?></h5>
                </div>
            </div>
        <?php else : ?>
            <p class="text-center">No more prayers for the next 7 days</p>
        <?php endif; ?>

        <div class="form-container text-center my-4">
            <form action="" method="get">
                <select name="box_id" class="custom-select" onchange="this.form.submit()">
                    <?php
                    $boxes = Box::getAllBoxes();
                    foreach ($boxes as $box) {
                        $selected = ($box->getId() == $selectedBoxId) ? 'selected' : '';
                        echo "<option value='" . $box->getId() . "' $selected>" . $box->getPrayerZone() . " - " . $box->getName() . "</option>";
                    }
                    ?>
                </select>
            </form>
        </div>

        <div class="table-responsive">
            <?php if (!empty($todayPrayerTimes)) : ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <?php foreach ($todayPrayerTimes as $prayer => $time) : ?>
                                <th><?php echo $prayer; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach ($todayPrayerTimes as $prayer => $time) : ?>
                                <td class="prayer-time"><?php echo date('h:i A', strtotime($time)); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="jadual-waktu-solat.php?box_id=<?php echo $selectedBoxId; ?>" class="btn btn-primary">JADUAL WAKTU SOLAT</a>
        </div>
    </div>

    <!-- <button onclick="playPrayerAudio()" class="btn btn-primary">Play Prayer Call</button> -->

    <audio id="prayerAudio" style="display: none;">
        <source src="voice/Time To Pray.mp3" type="audio/mpeg">
    </audio>

    <!-- <button id="playButton" style="display: none;"></button> -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const timeZone = 'Asia/Kuala_Lumpur';
        let timerInterval;
        let blinkInterval;
        let audioLoaded = false;

        function playPrayerAudio(prayerAudio) {
            if (prayerAudio) {
                prayerAudio.play();
                setTimeout(function() {
                    prayerAudio.pause();
                    prayerAudio.currentTime = 0;
                }, 15000);
            }
        }

        function startBlinking() {
            document.getElementById("timer").classList.add("blink");
            blinkInterval = setInterval(function() {
                document.getElementById("timer").classList.toggle("blink");
            }, 500);
        }

        function stopBlinking() {
            clearInterval(blinkInterval);
            document.getElementById("timer").classList.remove("blink");
        }

        function updateTimer() {
            const now = new Date(new Date().toLocaleString('en-US', {
                timeZone: timeZone
            })).getTime();
            const endTime = new Date("<?php echo date('Y-m-d H:i:s', strtotime($today . ' ' . $nextPrayerTime)); ?>").getTime();
            const distance = endTime - now;

            if (distance <= 0) {
                clearInterval(timerInterval);
                document.getElementById("timer").innerHTML = "Waktu solat <?php echo $nextPrayer; ?> telah tiba!";
                startBlinking();
                console.log(audioLoaded);
                if (audioLoaded === false) {
                    loadAudio();
                }

                setTimeout(function() {
                    stopBlinking();
                    startNextPrayerCountdown();
                }, 15000);
            } else {
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.getElementById("timer").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";
            }
        }

        function startNextPrayerCountdown() {
            updateTimer();
            timerInterval = setInterval(updateTimer, 1000);
        }

        function loadAudio() {

            console.log('loadAudio');
            const prayerAudio = document.getElementById('prayerAudio');
            console.log(prayerAudio);
            if (prayerAudio) {
                prayerAudio.play();
                prayerAudio.pause();
                prayerAudio.currentTime = 0;
                audioLoaded = true;

                // Call playPrayerAudio directly
                playPrayerAudio(prayerAudio);
            }
        }


        // Add the following script to trigger the audio play when a key is pressed on the document
        document.addEventListener('keydown', function() {
            loadAudio();
        });

        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);
    </script>
</body>

</html>