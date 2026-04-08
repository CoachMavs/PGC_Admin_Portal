<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class AdminPersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $table = 'personal_access_tokens_admin';
}
