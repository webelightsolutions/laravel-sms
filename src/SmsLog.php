<?php

namespace Webelightdev\LaravelSms;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $table = 'sms_logs';

    protected $guarded = [];

    /**
     * Get the status name.
     *
     * @param  string $value
     * @return string
     */
    public function getStatusAttribute($value)
    {
        return ($value == config('thunderSchool.mail_status.success')) ? 'success' : 'failure';
    }
}
