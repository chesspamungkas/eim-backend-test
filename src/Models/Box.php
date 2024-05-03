<?php

namespace App\Models;

use App\Utils\DatabaseConnection;
use PDO;

class Box
{
    private $id;
    private $boxName;
    private $prayerZone;
    private $subscriberId;

    // Constructor to initialize the Box object with name, prayer zone, and subscriber ID
    public function __construct($boxName, $prayerZone, $subscriberId)
    {
        $this->boxName = $boxName;
        $this->prayerZone = $prayerZone;
        $this->subscriberId = $subscriberId;
    }

    // Save the Box object to the database
    public function save()
    {
        $pdo = DatabaseConnection::getPDO();
        $stmt = $pdo->prepare("INSERT INTO box (box_name, prayer_zone, subs_id) VALUES (:box_name, :prayer_zone, :subs_id)");
        $stmt->bindParam(':box_name', $this->boxName);
        $stmt->bindParam(':prayer_zone', $this->prayerZone);
        $stmt->bindParam(':subs_id', $this->subscriberId, PDO::PARAM_INT);
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }

    // Find a Box by ID
    public static function findById($id)
    {
        $pdo = DatabaseConnection::getPDO();
        $stmt = $pdo->prepare("SELECT * FROM box WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $box = new self($row['box_name'], $row['prayer_zone'], $row['subs_id']);
            $box->id = $row['id'];
            return $box;
        }

        return null;
    }

    // Get all boxes from the database
    public static function getAllBoxes()
    {
        $pdo = DatabaseConnection::getPDO();
        $stmt = $pdo->query("SELECT * FROM box");
        $boxes = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $box = new self($row['box_name'], $row['prayer_zone'], $row['subs_id']);
            $box->id = $row['id'];
            $boxes[] = $box;
        }

        return $boxes;
    }

    // Getter for the Box ID
    public function getId()
    {
        return $this->id;
    }

    // Getter for the Box name
    public function getName()
    {
        return $this->boxName;
    }

    // Getter for the prayer zone
    public function getPrayerZone()
    {
        return $this->prayerZone;
    }

    // Getter for the subscriber ID
    public function getSubscriberId()
    {
        return $this->subscriberId;
    }
}
