<?php

namespace Webelightdev\LaravelSms\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webelightdev\LaravelSms\SmsManager;
use Webelightdev\LaravelSms\Drivers\TwilioSms;
use Webelightdev\LaravelSms\Facade\Sms;
use Webelightdev\LaravelSms\SmsLog;
use Carbon\Carbon;

class sendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mobileNumber;
    protected $message;
    protected $logData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mobileNumber, $message , $logData)
    {
        $this->mobileNumber = $mobileNumber;
        $this->message = $message;
        $this->logData = $logData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $class = config('sms.map.twilio_sms');

        $object = new $class;
        $object->send($this->mobileNumber, $this->message);

        $data = [
            'message_date'  => Carbon::now(),
            'mobile_no'     => $this->mobileNumber,
            'message_body'   => $this->message,
            'status'         => config('sms.status.success'),
            'status_message' => 'No errors.',
        ];
        SmsLog::create(array_merge($data, $this->logData));   
    }

    public function failed(Exception $exception)
    {
        $data = [
            'message_date'  => Carbon::now(),
            'mobile_no'     => $this->mobileNumber,
            'message_body'   => $this->message,
            'status'         => config('sms.status.failure'),
            'status_message' => $exception->getMessage(),
        ];
        SmsLog::create(array_merge($data, $this->logData));
    }
}
