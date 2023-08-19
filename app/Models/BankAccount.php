<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;
    protected $table = 'bank_accounts';
    protected $fillable = [
        'account_number',
        'iban',
        'bank_name',
        'swift_number',
        'type',
        'user_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
