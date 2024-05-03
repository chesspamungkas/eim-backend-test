<?php

namespace Tests;

require_once __DIR__ . '/../config.php';

use App\Models\Subscriber;
use PHPUnit\Framework\TestCase;

class SubscriberTest extends TestCase
{
    /**
     * Test the save method of the Subscriber model.
     */
    public function testSave()
    {
        $subscriber = new Subscriber("Test Subscriber");
        $subscriber->save();

        $this->assertDatabaseHas('subscribers', [
            'name' => "Test Subscriber",
        ]);
    }

    /**
     * Test the findById method of the Subscriber model.
     */
    public function testFindById()
    {
        $subscriber = new Subscriber("Test Subscriber");
        $subscriber->save();

        $foundSubscriber = Subscriber::findById($subscriber->getId());

        $this->assertInstanceOf(Subscriber::class, $foundSubscriber);
        $this->assertEquals("Test Subscriber", $foundSubscriber->getName());
    }

    /**
     * Clean up the database after each test.
     */
    protected function tearDown(): void
    {
        \DB::table('subscriber')->truncate();
    }
}
