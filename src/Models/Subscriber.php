<?php

namespace App\Models;

use App\Utils\DatabaseConnection;
use PDO;

class Subscriber
{
    private $id;
    private $subsName;

    // Constructor to initialize the Subscriber object with a name
    public function __construct($subsName)
    {
        $this->subsName = $subsName;
    }

    // Save the Subscriber object to the database
    public function save()
    {
        $pdo = DatabaseConnection::getPDO();
        $stmt = $pdo->prepare("INSERT INTO subscriber (subs_name) VALUES (:subs_name)");
        $stmt->bindParam(':subs_name', $this->subsName);
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }

    // Find a Subscriber by ID
    public static function findById($id)
    {
        $pdo = DatabaseConnection::getPDO();
        $stmt = $pdo->prepare("SELECT * FROM subscriber WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $subscriber = new self($row['subs_name']);
            $subscriber->id = $row['id'];
            return $subscriber;
        }

        return null;
    }

    // Getter for the Subscriber ID
    public function getId()
    {
        return $this->id;
    }

    // Getter for the Subscriber name
    public function getName()
    {
        return $this->subsName;
    }
}
