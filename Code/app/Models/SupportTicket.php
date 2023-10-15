<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SupportTicket extends Model
{
    use Notifiable;
    
    protected $fillable = [
        'id',
        'customer_name',
        'problem_title',
        'problem_description',
        'email',
        'phone_number',
        'reference_number',
        'status',
        'created_at'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
