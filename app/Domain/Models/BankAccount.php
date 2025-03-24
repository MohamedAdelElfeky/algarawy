<?php

namespace App\Domain\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;
    protected $table = 'bank_accounts';
    protected $fillable = ['account_number', 'iban', 'bank_name', 'swift_number', 'type', 'status', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
