<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class PHPMailerService
{
    protected $host;
    protected $port;
    protected $username;
    protected $password;
    protected $encryption;
    protected $fromAddress;
    protected $fromName;
    protected $skipSslVerify;
    protected $debug;

    public function __construct()
    {
        $this->host = env('MAIL_HOST', 'smtp.gmail.com');
        $this->port = env('MAIL_PORT', 465);
        $this->username = env('MAIL_USERNAME');
        $this->password = env('MAIL_PASSWORD');
        $this->encryption = env('MAIL_ENCRYPTION', 'ssl');
        $this->fromAddress = env('MAIL_FROM_ADDRESS');
        $this->fromName = env('MAIL_FROM_NAME', config('app.name', 'FOODDAILY'));
        $this->skipSslVerify = env('MAIL_SKIP_SSL_VERIFY', false);
        $this->debug = env('MAIL_DEBUG', false);
    }

    protected static $sharedMailer = null;

    public static function resetSharedMailer(): void
    {
        static::$sharedMailer = null;
    }

    /**
     * Get or initialize reusable PHPMailer instance with SMTPKeepAlive
     */
    protected function getSharedMailer()
    {
        if (static::$sharedMailer === null) {
            $mail = $this->getPHPMailerInstance();

            // Server settings
            if ($this->debug) {
                $mail->SMTPDebug  = SMTP::DEBUG_SERVER;
                $mail->Debugoutput = function($str, $level) {
                    Log::debug("PHPMailer Debug ($level): $str");
                };
            }
            $mail->isSMTP();
            $mail->SMTPKeepAlive = true; // Giữ kết nối socket mở để gửi hàng loạt siêu tốc (8x faster)
            $mail->Host       = $this->host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->username;
            $mail->Password   = $this->password;

            // Encryption/Port setup
            if (strtolower($this->encryption) === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif (strtolower($this->encryption) === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPSecure = '';
            }
            $mail->Port       = $this->port;
            $mail->CharSet    = 'UTF-8';

            if ($this->skipSslVerify) {
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
            }

            $mail->setFrom($this->fromAddress, $this->fromName);
            static::$sharedMailer = $mail;
        }

        return static::$sharedMailer;
    }

    /**
     * Send email via SMTP using PHPMailer.
     *
     * @param string|array $to Receiver email address or list of addresses.
     * @param string $subject Email subject.
     * @param string $body Email content.
     * @param array $attachments Array of file paths to attach.
     * @param bool $isHtml Whether the email body is HTML.
     * @return bool
     */
    public function send($to, $subject, $body, $attachments = [], $isHtml = true)
    {
        if (empty($this->username) || empty($this->password)) {
            Log::warning('SMTP Mail username or password is not configured. Mail skipped: ' . $subject);
            return false;
        }

        $mail = $this->getSharedMailer();

        try {
            // Xóa người nhận và tệp đính kèm cũ để tái sử dụng kết nối
            $mail->clearAllRecipients();
            $mail->clearAttachments();
            $mail->clearCustomHeaders();
            $mail->clearReplyTos();

            // Cập nhật người gửi
            $mail->setFrom($this->fromAddress, $this->fromName);

            // Recipients
            if (is_array($to)) {
                foreach ($to as $address) {
                    $mail->addAddress($address);
                }
            } else {
                $mail->addAddress($to);
            }

            // Attachments
            foreach ($attachments as $filePath) {
                if (file_exists($filePath)) {
                    $mail->addAttachment($filePath);
                } else {
                    Log::warning("PHPMailer attachment not found: " . $filePath);
                }
            }

            // Content
            $mail->isHTML($isHtml);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            // Send
            $mail->send();
            return true;
        } catch (Exception $e) {
            Log::error("PHPMailer failed to send email: " . $mail->ErrorInfo . " | Exception: " . $e->getMessage());
            // Đóng socket và đặt lại mailer nếu gặp lỗi để lần gửi sau tự động kết nối lại
            try {
                $mail->smtpClose();
            } catch (\Throwable $t) {}
            static::$sharedMailer = null;
            return false;
        }
    }

    /**
     * Tự động đóng kết nối socket khi tiến trình kết thúc
     */
    public function __destruct()
    {
        if (static::$sharedMailer !== null) {
            try {
                static::$sharedMailer->smtpClose();
            } catch (\Throwable $t) {}
        }
    }

    /**
     * Send email using a Blade template.
     *
     * @param string|array $to
     * @param string $subject
     * @param string $view
     * @param array $data
     * @param array $attachments
     * @return bool
     */
    public function sendWithTemplate($to, $subject, $view, $data = [], $attachments = [])
    {
        try {
            $body = view($view, $data)->render();
            return $this->send($to, $subject, $body, $attachments, true);
        } catch (\Exception $e) {
            Log::error("PHPMailer template render error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get a new PHPMailer instance.
     *
     * @return PHPMailer
     */
    protected function getPHPMailerInstance()
    {
        return new PHPMailer(true);
    }
}
