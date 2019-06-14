<?php
namespace Webelightdev\LaravelSms;

use Illuminate\Support\Facades\Queue;
use Webelightdev\LaravelSms\Jobs\sendSmsJob;

class SmsManager
{
    /**
     * Sms Configuration.
     *
     * @var null|object
     */
    protected $config = null;
    /**
     * Sms Driver Settings.
     *
     * @var null|object
     */
    protected $settings = null;
    /**
     * Sms Driver Name.
     *
     * @var null|string
     */
    protected $driver = null;
    /**
     * SmsManager constructor.
     */
    public function __construct()
    {
        $this->config = config('sms');
        $this->driver = $this->config['default'];
        $this->settings = $this->config['drivers'][$this->driver];

    }
    /**
     * Change the driver on the fly.
     *
     * @param $driver
     * @return $this
     */
    public function with($driver)
    {
        $this->driver = $driver;
        $this->settings = $this->config['drivers'][$this->driver];

        return $this;
    }
    /**
     * Send message.
     *
     * @param $message
     * @param $callback
     * @return mixed
     */
    public function send($message, $mobileNumbers, $logData = [] ,$callback)
    {
        $smsEnable = config('sms.enable');

        if ($smsEnable) {
            $this->validateParams();            
            $class = $this->config['map'][$this->driver];
            $object = new $class($this->settings);
            $object->message($message);
            
            call_user_func($callback, $object);
        
            foreach ($mobileNumbers as $recipient) {
               Queue::push(new sendSmsJob($recipient, $message, $logData));
            }
            return response()->json(['success' => "message sent successfully"], 200);
        }
        return response()->json(['message' => $message]);
    }
    /**
     * Validate Parameters before sending.
     *
     * @throws \Exception
     */
    protected function validateParams()
    {
        if (empty($this->driver)) {
            throw new \Exception("Driver not selected or default driver does not exist.");
        }
        if (empty($this->config['drivers'][$this->driver]) or empty($this->config['map'][$this->driver])) {
            throw new \Exception("Driver not found in config file. Try updating the package.");
        }
        if (!class_exists($this->config['map'][$this->driver])) {
            throw new \Exception("Driver source not found. Please update the package.");
        }
    }
}