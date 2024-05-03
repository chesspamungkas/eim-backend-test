<?php

namespace App\Services;

use App\Models\Box;
use App\Models\Song;
use DateTime;

class PrayerTimeService
{
    private $prayerTimeApiUrl = 'https://www.e-solat.gov.my/index.php?r=esolatApi/TakwimSolat&period=week&zone=';

    /**
     * Generate prayer times for the next 7 days (including today) for a given box ID.
     *
     * @param int $boxId The ID of the box.
     * @throws \Exception If the box is not found.
     */
    public function generatePrayerTimes($boxId)
    {
        $box = Box::findById($boxId);

        if (!$box) {
            throw new \Exception("Box not found");
        }

        $prayerZone = $box->getPrayerZone();
        $prayerTimes = $this->fetchPrayerTimes($prayerZone);

        $currentDate = new DateTime();

        foreach ($prayerTimes as $prayerTime) {
            if (!isset($prayerTime['date'])) {
                throw new \Exception("Invalid prayer time data: date is missing");
            }

            $prayerTimeDate = DateTime::createFromFormat('d-M-Y', $prayerTime['date']);

            if (!$prayerTimeDate) {
                throw new \Exception("Invalid prayer time date format: " . $prayerTime['date']);
            }

            $prayerTimeSeq = 1;

            foreach ($prayerTime['times'] as $prayerName => $time) {

                // Check if the song already exists in the database
                $existingSong = Song::findByBoxIdAndPrayerTimeSeqAndDate($box->getId(), $prayerTimeSeq, $prayerTimeDate->format('Y-m-d'));

                if (!$existingSong) {
                    // Create a new song only if it doesn't exist
                    $song = new Song(
                        "$prayerName (" . $prayerTimeDate->format('m-d') . ")",
                        $box->getSubscriberId(),
                        $box->getId(),
                        $prayerZone,
                        $prayerTimeSeq,
                        $prayerTimeDate->format('Y-m-d'),
                        $time
                    );
                    $song->save();
                }

                $prayerTimeSeq++;
            }

            $currentDate->modify('+1 day');
        }
    }

    /**
     * Get prayer times for the next 7 days (including today) for a given box ID.
     *
     * @param int $boxId The ID of the box.
     * @return array The prayer times for the next 7 days (including today).
     */
    public function getPrayerTimesForBox($boxId)
    {
        $prayerTimes = [];
        $currentDate = new DateTime();

        for ($i = 0; $i < 7; $i++) {
            $date = $currentDate->format('Y-m-d');
            $songs = Song::findByBoxIdAndDate($boxId, $date);

            $times = [
                'Imsak' => '',
                'Subuh' => '',
                'Syuruk' => '',
                'Zohor' => '',
                'Asar' => '',
                'Maghrib' => '',
                'Isyak' => '',
            ];

            foreach ($songs as $song) {
                $prayerName = explode(' ', $song->getTitle())[0];
                $times[$prayerName] = $song->getPrayerTime();
            }

            $prayerTimes[$date] = $times;

            $currentDate->modify('+1 day');
        }

        return $prayerTimes;
    }

    /**
     * Fetch prayer times for the next 7 days from the API for a given prayer zone.
     *
     * @param string $prayerZone The prayer zone.
     * @return array The prayer times for the next 7 days.
     */
    private function fetchPrayerTimes($prayerZone)
    {
        $url = $this->prayerTimeApiUrl . $prayerZone;
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if ($data === null) {
            // Handle the case when the API response is invalid or empty
            throw new \Exception("Failed to fetch prayer times from the API.");
        }

        $prayerTimes = [];

        foreach ($data['prayerTime'] as $prayerTime) {
            $times = [
                'Imsak' => $prayerTime['imsak'] ?? '',
                'Subuh' => $prayerTime['fajr'] ?? '',
                'Syuruk' => $prayerTime['syuruk'] ?? '',
                'Zohor' => $prayerTime['dhuhr'] ?? '',
                'Asar' => $prayerTime['asr'] ?? '',
                'Maghrib' => $prayerTime['maghrib'] ?? '',
                'Isyak' => $prayerTime['isha'] ?? '',
            ];

            $prayerTimes[] = [
                'date' => $prayerTime['date'] ?? '',
                'times' => $times,
            ];
        }

        return $prayerTimes;
    }

    /**
     * Play the voice-over at the corresponding prayer times for a given box ID.
     *
     * @param int $boxId The ID of the box.
     * @throws \Exception If the box is not found or the voice-over file is missing.
     */
    public function playVoiceOver($boxId)
    {
        $box = Box::findById($boxId);

        if (!$box) {
            throw new \Exception("Box not found");
        }

        $currentDate = date('Y-m-d');
        $currentTime = date('H:i');

        $songs = Song::findByBoxIdAndDate($box->getId(), $currentDate);

        foreach ($songs as $song) {
            if ($song->getPrayerTime() === $currentTime) {
                $this->playMp3File('voice/Time To Pray.mp3');
                break;
            }
        }
    }

    /**
     * Play the MP3 file.
     *
     * @param string $filePath The path to the MP3 file.
     * @throws \Exception If the voice-over file is not found.
     */
    private function playMp3File($filePath)
    {
        if (file_exists($filePath)) {
            $escapedFilePath = escapeshellarg($filePath);
            exec("mpg123 -q $escapedFilePath");
        } else {
            throw new \Exception("Voice-over file not found: $filePath");
        }
    }
}
