<?php

namespace App\Models;

use App\Utils\DatabaseConnection;
use PDO;

class Song
{
    private $id;
    private $songTitle;
    private $subscriberId;
    private $boxId;
    private $prayerZone;
    private $prayerTimeSeq;
    private $prayerTimeDate;
    private $prayerTime;

    // Constructor to initialize the Song object with song title, subscriber ID, box ID, prayer zone, prayer time sequence, prayer time date, and prayer time
    public function __construct($songTitle, $subscriberId, $boxId, $prayerZone, $prayerTimeSeq, $prayerTimeDate, $prayerTime)
    {
        $this->songTitle = $songTitle;
        $this->subscriberId = $subscriberId;
        $this->boxId = $boxId;
        $this->prayerZone = $prayerZone;
        $this->prayerTimeDate = $prayerTimeDate;
        $this->prayerTimeSeq = $prayerTimeSeq;
        $this->prayerTime = $prayerTime;
    }

    // Save the Song object to the database
    public function save()
    {
        $pdo = DatabaseConnection::getPDO();
        $stmt = $pdo->prepare("INSERT INTO song (song_title, subs_id, box_id, prayer_zone, prayer_time_seq, prayer_time_date, prayer_time) 
                               VALUES (:song_title, :subs_id, :box_id, :prayer_zone, :prayer_time_seq, :prayer_time_date, :prayer_time)");
        $stmt->bindParam(':song_title', $this->songTitle);
        $stmt->bindParam(':subs_id', $this->subscriberId, PDO::PARAM_INT);
        $stmt->bindParam(':box_id', $this->boxId, PDO::PARAM_INT);
        $stmt->bindParam(':prayer_zone', $this->prayerZone);
        $stmt->bindParam(':prayer_time_seq', $this->prayerTimeSeq, PDO::PARAM_INT);
        $stmt->bindParam(':prayer_time_date', $this->prayerTimeDate);
        $stmt->bindParam(':prayer_time', $this->prayerTime);
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }

    // Find a Song by ID
    public static function findById($id)
    {
        $pdo = DatabaseConnection::getPDO();
        $stmt = $pdo->prepare("SELECT * FROM song WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $song = new self(
                $row['song_title'],
                $row['subscriber_id'],
                $row['box_id'],
                $row['prayer_zone'],
                $row['prayer_time_seq'],
                $row['prayer_time_date'],
                $row['prayer_time']
            );
            $song->id = $row['id'];
            return $song;
        }

        return null;
    }

    // Find a Song by box ID, prayer time sequence, and date
    public static function findByBoxIdAndPrayerTimeSeqAndDate($boxId, $prayerTimeSeq, $prayerTimeDate)
    {
        $pdo = DatabaseConnection::getPDO();
        $stmt = $pdo->prepare("SELECT * FROM song WHERE box_id = :box_id AND prayer_time_seq = :prayer_time_seq AND prayer_time_date = :prayer_time_date");
        $stmt->bindParam(':box_id', $boxId, PDO::PARAM_INT);
        $stmt->bindParam(':prayer_time_seq', $prayerTimeSeq, PDO::PARAM_INT);
        $stmt->bindParam(':prayer_time_date', $prayerTimeDate);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $song = new self(
                $row['song_title'],
                $row['subs_id'],
                $row['box_id'],
                $row['prayer_zone'],
                $row['prayer_time_seq'],
                $row['prayer_time_date'],
                $row['prayer_time']
            );
            $song->id = $row['id'];
            return $song;
        }

        return null;
    }

    // Find Songs by box ID and date
    public static function findByBoxIdAndDate($boxId, $date)
    {
        $pdo = DatabaseConnection::getPDO();
        $stmt = $pdo->prepare("SELECT * FROM song WHERE box_id = :box_id AND prayer_time_date = :date ORDER BY prayer_time_seq");
        $stmt->bindParam(':box_id', $boxId, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $songs = [];
        foreach ($rows as $row) {
            $song = new self(
                $row['song_title'],
                $row['subs_id'],
                $row['box_id'],
                $row['prayer_zone'],
                $row['prayer_time_seq'],
                $row['prayer_time_date'],
                $row['prayer_time']
            );
            $song->id = $row['id'];
            $songs[] = $song;
        }

        return $songs;
    }

    // Find Song by box ID and date
    public function getId()
    {
        return $this->id;
    }

    // Getter for the Song ID
    public function getTitle()
    {
        return $this->songTitle;
    }

    // Getter for the Song title
    public function getSubscriberId()
    {
        return $this->subscriberId;
    }

    // Getter for the box ID
    public function getBoxId()
    {
        return $this->boxId;
    }

    // Getter for the prayer zone
    public function getPrayerZone()
    {
        return $this->prayerZone;
    }

    // Getter for the prayer time date
    public function getPrayerTimeDate()
    {
        return $this->prayerTimeDate;
    }

    // Getter for the prayer time sequence
    public function getPrayerTimeSeq()
    {
        return $this->prayerTimeSeq;
    }

    // Getter for the prayer time
    public function getPrayerTime()
    {
        return $this->prayerTime;
    }
}
