<?php

namespace Tests;

require_once __DIR__ . '/../config.php';

use App\Models\Box;
use PHPUnit\Framework\TestCase;

class BoxTest extends TestCase
{
    /**
     * Test the save method of the Box model.
     */
    public function testSave()
    {
        $box = new Box("Test Box", "JHR01", 1);
        $box->save();

        $this->assertDatabaseHas('box', [
            'name' => "Test Box",
            'prayer_zone' => "JHR01",
            'subscriber_id' => 1,
        ]);
    }

    /**
     * Test the findById method of the Box model.
     */
    public function testFindById()
    {
        $box = new Box("Test Box", "JHR01", 1);
        $box->save();

        $foundBox = Box::findById($box->getId());

        $this->assertInstanceOf(Box::class, $foundBox);
        $this->assertEquals("Test Box", $foundBox->getName());
        $this->assertEquals("JHR01", $foundBox->getPrayerZone());
        $this->assertEquals(1, $foundBox->getSubscriberId());
    }

    /**
     * Clean up the database after each test.
     */
    protected function tearDown(): void
    {
        \DB::table('box')->truncate();
    }
}
