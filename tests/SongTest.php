<?php

namespace Tests;

require_once __DIR__ . '/../config.php';

use App\Models\Song;
use PHPUnit\Framework\TestCase;

class SongTest extends TestCase
{
    /**
     * Test the save method of the Song model.
     */
    public function testSave()
    {
        $song = new Song("Test Song", 1, 1, "JHR01", "2023-06-10", 1, "06:00");
        $song->save();

        $this->assertDatabaseHas('song', [
            'title' => "Test Song",
            'subscriber_id' => 1,
            'box_id' => 1,
            'prayer_zone' => "JHR01",
            'prayer_time_date' => "2023-06-10",
            'prayer_time_seq' => 1,
            'prayer_time' => "06:00",
        ]);
    }

    /**
     * Test the findById method of the Song model.
     */
    public function testFindById()
    {
        $song = new Song("Test Song", 1, 1, "JHR01", "2023-06-10", 1, "06:00");
        $song->save();

        $foundSong = Song::findById($song->getId());

        $this->assertInstanceOf(Song::class, $foundSong);
        $this->assertEquals("Test Song", $foundSong->getTitle());
        $this->assertEquals(1, $foundSong->getSubscriberId());
        $this->assertEquals(1, $foundSong->getBoxId());
        $this->assertEquals("JHR01", $foundSong->getPrayerZone());
        $this->assertEquals("2023-06-10", $foundSong->getPrayerTimeDate());
        $this->assertEquals(1, $foundSong->getPrayerTimeSeq());
        $this->assertEquals("06:00", $foundSong->getPrayerTime());
    }

    /**
     * Clean up the database after each test.
     */
    protected function tearDown(): void
    {
        \DB::table('song')->truncate();
    }
}
