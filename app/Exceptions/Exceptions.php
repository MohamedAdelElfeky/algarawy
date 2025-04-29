<?php

namespace App\Exceptions;


class InvalidModelException extends \Exception
{
    protected $message = 'نوع النموذج غير صالح';
}
