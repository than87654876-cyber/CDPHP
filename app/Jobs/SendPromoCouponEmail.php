<?php

namespace App\Jobs;

use App\Services\PHPMailerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPromoCouponEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $fullname;
    protected $couponCode;
    protected $subject;
    protected $body;
    protected $expiryDays;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $fullname, $couponCode, $subject, $body, $expiryDays)
    {
        $this->email = $email;
        $this->fullname = $fullname;
        $this->couponCode = $couponCode;
        $this->subject = $subject;
        $this->body = $body;
        $this->expiryDays = $expiryDays;
    }

    /**
     * Execute the job.
     */
    public function handle(PHPMailerService $phpMailer): void
    {
        try {
            $phpMailer->sendWithTemplate(
                $this->email,
                $this->subject,
                'emails.coupon_promo',
                [
                    'fullname' => $this->fullname,
                    'couponCode' => $this->couponCode,
                    'subjectText' => $this->subject,
                    'bodyText' => $this->body,
                    'expiryDays' => $this->expiryDays,
                    'websiteUrl' => route('muahang'),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Queue SendPromoCouponEmail failed for ' . $this->email . ': ' . $e->getMessage());
            throw $e;
        }
    }
}
