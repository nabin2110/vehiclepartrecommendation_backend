<?php

namespace App\Models;

use App\Constant\TableConstant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuthChallenge extends Model
{
    protected $table = TableConstant::AUTH_CHALLENGE;
    private const CHALLENGE_TTL_SECONDS = 120;
    protected $fillable = [ 
        'challenge_id',
        'email',
        'device_id',
        'nonce',
        'ip_address',
        'user_agent',
        'used',
        'attempt_count',
        'expires_at',
    ];

    protected $hidden = ['nonce'];

    protected static function boot():void{
        parent::boot();

        static::creating(function($model){
            $model->challenge_id = (string) Str::uuid();
            $model->nonce = bin2hex(random_bytes(32));
            $model->expires_at = now()->addSeconds(self::CHALLENGE_TTL_SECONDS);
        });
    }
    
}
