<?php

namespace App\Models;

use App\Constant\TableConstant;
use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
    protected $table = TableConstant::REFRESH_TOKEN;
    protected $fillable = [
        'token_hash',
        'user_id',
        'device_id',
        'ip_address',
        'jti',
        'revoked',
        'revoked_at',
        'expires_at', 
    ];
    
}
