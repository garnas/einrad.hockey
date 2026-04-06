<?php

namespace unit;

use App\Service\Mail\MailService;
use Config;
use Helper;
use PHPMailer\PHPMailer\Exception;
use PHPUnit\Framework\TestCase;

class MailServiceTest extends TestCase
{
    protected function setUp(): void
    {
        // Disable file I/O so Helper::log() is a no-op during unit tests.
        Helper::$log_user = false;
    }

    protected function tearDown(): void
    {
        Helper::$log_user = true;
    }

    // -------------------------------------------------------------------------
    // queue()
    // -------------------------------------------------------------------------

    public function testQueueWithEmptyArrayIsNoOp(): void
    {
        // Must return without touching the DB when recipients is empty.
        MailService::queue('Subject', 'Content', []);
        $this->assertTrue(true);
    }

    public function testQueueWithEmptyStringIsNoOp(): void
    {
        MailService::queue('Subject', 'Content', '');
        $this->assertTrue(true);
    }

    // -------------------------------------------------------------------------
    // send()
    //
    // Env::ACTIVATE_EMAIL = false in the test environment, so PHPMailer never
    // opens an SMTP connection.  All assertions below verify that the method
    // returns the expected boolean and that no exception is thrown.
    // -------------------------------------------------------------------------

    /**
     * @throws Exception
     */
    public function testSendReturnsTrueWithSingleAddress(): void
    {
        $result = MailService::send(
            subject: 'Test',
            body: 'Hello',
            addresses: ['to@example.com'],
            from: 'sender@example.com',
        );
        $this->assertTrue($result);
    }

    /**
     * @throws Exception
     */
    public function testSendReturnsTrueWithMultipleAddressesBelowBccGrenze(): void
    {
        // Exactly at the limit — all recipients share one PHPMailer call.
        $addresses = array_fill(0, Config::BCC_GRENZE, 'to@example.com');
        $result = MailService::send(
            subject: 'Test',
            body: 'Hello',
            addresses: $addresses,
            from: 'sender@example.com',
        );
        $this->assertTrue($result);
    }

    /**
     * One address above BCC_GRENZE triggers the bulk path: each recipient gets
     * a separate PHPMailer call with clearAddresses() in between.
     *
     * @throws Exception
     */
    public function testSendReturnsTrueAboveBccGrenze(): void
    {
        $addresses = array_fill(0, Config::BCC_GRENZE + 1, 'to@example.com');
        $result = MailService::send(
            subject: 'Test',
            body: 'Hello',
            addresses: $addresses,
            from: 'sender@example.com',
        );
        $this->assertTrue($result);
    }

    /**
     * @throws Exception
     */
    public function testSendWithHtmlBodyReturnsTrue(): void
    {
        $result = MailService::send(
            subject: 'Test',
            body: '<p>Hello</p>',
            addresses: ['to@example.com'],
            from: 'sender@example.com',
            isHtml: true,
        );
        $this->assertTrue($result);
    }

    /**
     * @throws Exception
     */
    public function testSendWithCcAndBccReturnsTrue(): void
    {
        $result = MailService::send(
            subject: 'Test',
            body: 'Hello',
            addresses: ['to@example.com'],
            from: 'sender@example.com',
            ccs: ['cc@example.com'],
            bccs: ['bcc@example.com'],
        );
        $this->assertTrue($result);
    }

    /**
     * @throws Exception
     */
    public function testSendWithReplyToReturnsTrue(): void
    {
        $result = MailService::send(
            subject: 'Test',
            body: 'Hello',
            addresses: ['to@example.com'],
            from: 'sender@example.com',
            replyTos: ['replyto@example.com'],
        );
        $this->assertTrue($result);
    }
}
