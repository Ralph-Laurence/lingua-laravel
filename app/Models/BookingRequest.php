<?php

namespace App\Models;

use App\Models\FieldNames\BookingRequestFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRequest extends Model
{
    use HasFactory;

    public function sender() {
        return $this->belongsTo(User::class, BookingRequestFields::SenderId);
    }

    public function receiver() {
        return $this->belongsTo(User::class, BookingRequestFields::ReceiverId);
    }
}
