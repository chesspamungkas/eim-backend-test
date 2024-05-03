<?php

namespace Tests;

require_once __DIR__ . '/../config.php';

use App\Services\EmailService;
use PHPUnit\Framework\TestCase;

class EmailServiceTest extends TestCase
{
    /**
     * Test the sendErrorEmail method of the EmailService class.
     */
    public function testSendErrorEmail()
    {
        $emailService = new EmailService();

        $this->expectOutputString("Error email sent successfully." . PHP_EOL);

        $emailService->sendErrorEmail(1, "JHR01", "Test error message");
    }
}
