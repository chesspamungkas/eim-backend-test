<!DOCTYPE html>
<html>

<head>
    <title>Jadual Waktu Solat</title>
    <style>
        /* Add your desired styles here */
    </style>
</head>

<body>
    <h2>Jadual Waktu Solat</h2>
    <form action="" method="get">
        <select name="box_id">
            <?php
            // Use the required classes
            use App\Models\Box;

            // Get all boxes from the database
            $boxes = Box::getAllBoxes();

            foreach ($boxes as $box) {
                $selected = ($box->getId() == $_GET['box_id']) ? 'selected' : '';
                echo "<option value='" . $box->getId() . "' $selected>" . $box->getPrayerZone() . " - " . $box->getName() . "</option>";
            }
            ?>
        </select>
        <input type="submit" value="CARI">
    </form>

    <?php
    // Use the required classes
    use App\Services\PrayerTimeService;

    if (isset($_GET['box_id'])) {
        $boxId = $_GET['box_id'];
        $prayerTimeService = new PrayerTimeService();
        $prayerTimes = $prayerTimeService->getPrayerTimesForBox($boxId);

        echo "<table>";
        echo "<tr><th>Date</th><th>Imsak</th><th>Subuh</th><th>Syuruk</th><th>Zohor</th><th>Asar</th><th>Maghrib</th><th>Isyak</th></tr>";

        foreach ($prayerTimes as $date => $times) {
            echo "<tr>";
            echo "<td>" . $date . "</td>";
            echo "<td>" . $times['Imsak'] . "</td>";
            echo "<td>" . $times['Subuh'] . "</td>";
            echo "<td>" . $times['Syuruk'] . "</td>";
            echo "<td>" . $times['Zohor'] . "</td>";
            echo "<td>" . $times['Asar'] . "</td>";
            echo "<td>" . $times['Maghrib'] . "</td>";
            echo "<td>" . $times['Isyak'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    }
    ?>
</body>

</html>