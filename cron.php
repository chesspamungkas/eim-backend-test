<?php
// Require the necessary files
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

// Use the required classes
use App\Models\Box;
use App\Services\PrayerTimeService;
use App\Services\EmailService;

$prayerTimeService = new PrayerTimeService();
$emailService = new EmailService();

// Fetch all boxes from the database
$pdo = \App\Utils\DatabaseConnection::getPDO();
$stmt = $pdo->query("SELECT * FROM box");
$boxes = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($boxes as $box) {
    $boxId = $box['id'];
    $prayerZone = $box['prayer_zone'];

    try {
        $prayerTimeService->generatePrayerTimes($boxId);
        echo "Prayer times generated successfully for Box ID: $boxId" . PHP_EOL;
    } catch (\Exception $e) {
        echo "Error generating prayer times for Box ID: $boxId" . PHP_EOL;
        $errorMessage = $e->getMessage();
        $emailService->sendErrorEmail($boxId, $prayerZone, $errorMessage);
    }
}
