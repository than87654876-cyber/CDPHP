<?php

namespace Tests\Feature;

use App\Services\PHPMailerService;
use PHPMailer\PHPMailer\PHPMailer;
use Tests\TestCase;

class PHPMailerServiceTest extends TestCase
{
    /**
     * Test the PHPMailerService can load environment variables.
     */
    public function test_phpmailer_service_loads_config()
    {
        // Set environment variables for the test
        config(['mail.mailers.smtp.host' => 'smtp.test-server.com']);
        
        $service = new PHPMailerService();
        
        $reflection = new \ReflectionClass($service);
        
        $hostProp = $reflection->getProperty('host');
        $hostProp->setAccessible(true);
        
        $this->assertEquals(env('MAIL_HOST', 'smtp.gmail.com'), $hostProp->getValue($service));
    }

    /**
     * Test send returns false if credentials are not configured.
     */
    public function test_send_returns_false_if_credentials_empty()
    {
        // Temporarily clear env using putenv or config if we mock it,
        // but PHPMailerService reads from env() helper.
        // Let's instantiate PHPMailerService and mock its config properties using Reflection
        $service = new PHPMailerService();
        
        $reflection = new \ReflectionClass($service);
        
        $userProp = $reflection->getProperty('username');
        $userProp->setAccessible(true);
        $userProp->setValue($service, null);

        $passProp = $reflection->getProperty('password');
        $passProp->setAccessible(true);
        $passProp->setValue($service, null);

        $result = $service->send('test@example.com', 'Subject', 'Body');
        $this->assertFalse($result);
    }

    /**
     * Test send email successfully with mocked PHPMailer.
     */
    public function test_send_email_success_with_mocked_phpmailer()
    {
        PHPMailerService::resetSharedMailer();

        // Mock the PHPMailer class
        $mockMailer = $this->createMock(PHPMailer::class);
        
        // Assert PHPMailer methods are called during send
        $mockMailer->expects($this->once())->method('isSMTP');
        $mockMailer->expects($this->atLeastOnce())->method('setFrom');
        $mockMailer->expects($this->once())->method('addAddress')->with('recipient@example.com');
        $mockMailer->expects($this->once())->method('isHTML')->with(true);
        $mockMailer->expects($this->once())->method('send')->willReturn(true);

        // Partially mock PHPMailerService to override getPHPMailerInstance
        $service = $this->getMockBuilder(PHPMailerService::class)
            ->onlyMethods(['getPHPMailerInstance'])
            ->getMock();

        $service->expects($this->once())
            ->method('getPHPMailerInstance')
            ->willReturn($mockMailer);

        // Inject SMTP settings via reflection
        $reflection = new \ReflectionClass($service);
        
        $userProp = $reflection->getProperty('username');
        $userProp->setAccessible(true);
        $userProp->setValue($service, 'than28112000@gmail.com');

        $passProp = $reflection->getProperty('password');
        $passProp->setAccessible(true);
        $passProp->setValue($service, 'jkebhwrbuytsnpqp');

        $hostProp = $reflection->getProperty('host');
        $hostProp->setAccessible(true);
        $hostProp->setValue($service, 'smtp.gmail.com');

        $portProp = $reflection->getProperty('port');
        $portProp->setAccessible(true);
        $portProp->setValue($service, 465);

        $encryptionProp = $reflection->getProperty('encryption');
        $encryptionProp->setAccessible(true);
        $encryptionProp->setValue($service, 'ssl');

        $fromAddressProp = $reflection->getProperty('fromAddress');
        $fromAddressProp->setAccessible(true);
        $fromAddressProp->setValue($service, 'than28112000@gmail.com');

        $result = $service->send('recipient@example.com', 'Test Subject', '<p>Test Body</p>');
        
        $this->assertTrue($result);
    }
}
