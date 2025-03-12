<?php

namespace App\Domain\Entities;

class DashboardEntity
{
    public string $type;
    public $data;

    public function __construct(string $type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }
}
