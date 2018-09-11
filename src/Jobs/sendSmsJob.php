<?php

namespace Webelightdev\LaravelSms\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webelightdev\LaravelSms\SmsManager;
use Webelightdev\LaravelSms\Drivers\TwilioSms;
use Webelightdev\LaravelSms\Facade\Sms;

class sendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contact;
    protected $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contact, $message)
    {
        $this->contact = $contact;
        $this->message = $message;
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
        $object->send($this->contact, $this->message);
    }
}
