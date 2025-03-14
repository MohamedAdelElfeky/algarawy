<?php

namespace App\Exceptions;

use Exception;

class InvalidModelException extends Exception
{
    protected $message = 'نوع النموذج غير صالح';
}
