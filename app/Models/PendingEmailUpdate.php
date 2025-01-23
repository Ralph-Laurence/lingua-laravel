<?php

namespace App\Models;

use App\Models\FieldNames\PendingEmailUpdateFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingEmailUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        PendingEmailUpdateFields::UserId,
        PendingEmailUpdateFields::OldEmail,
        PendingEmailUpdateFields::NewEmail,
        PendingEmailUpdateFields::VerificationCode
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
