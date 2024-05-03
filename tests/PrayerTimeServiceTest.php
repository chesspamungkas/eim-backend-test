<?php

namespace Tests;

require_once __DIR__ . '/../config.php';

use App\Models\Box;
use App\Models\Song;
use App\Services\PrayerTimeService;
use PHPUnit\Framework\TestCase;

class PrayerTimeServiceTest extends TestCase
{
    private $prayerTimeService;

    protected function setUp(): void
    {
        $this->prayerTimeService = new PrayerTimeService();
    }

    public function testGeneratePrayerTimes()
    {
        // Create a mock Box object
        $box = $this->createMock(Box::class);
        $box->method('getId')->willReturn(1);
        $box->method('getSubscriberId')->willReturn(1);
        $box->method('getPrayerZone')->willReturn('JHR01');

        // Generate prayer times for the mock Box
        $this->prayerTimeService->generatePrayerTimes($box->getId());

        // Assert that the generated prayer times are saved in the database
        $this->assertDatabaseHas('song', [
            'subscriber_id' => 1,
            'box_id' => 1,
            'prayer_zone' => 'JHR01',
        ]);
    }

    public function testPlayVoiceOver()
    {
        // Create a mock Box object
        $box = $this->createMock(Box::class);
        $box->method('getId')->willReturn(1);

        // Create a mock Song object
        $song = $this->createMock(Song::class);
        $song->method('getPrayerTime')->willReturn(date('H:i'));

        // Stub the Song::findByBoxIdAndDate method to return the mock Song
        $this->stubSongFindByBoxIdAndDate($box->getId(), date('Y-m-d'), [$song]);

        // Set up the expectation for the playMp3File method
        $this->prayerTimeService->expects($this->once())
            ->method('playMp3File')
            ->with('voice/Time To Pray.mp3');

        // Play the voice-over for the mock Box
        $this->prayerTimeService->playVoiceOver($box->getId());
    }

    private function stubSongFindByBoxIdAndDate($boxId, $date, $songs)
    {
        Song::shouldReceive('findByBoxIdAndDate')
            ->with($boxId, $date)
            ->andReturn($songs);
    }

    protected function tearDown(): void
    {
        // Clean up the database after each test
        \DB::table('song')->truncate();
    }
}
