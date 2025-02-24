<?php
namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'account_number', 'iban', 'bank_name', 'swift_number', 
        'type', 'user_id', 'status'
    ];
}
