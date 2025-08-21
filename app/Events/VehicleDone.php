<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Queue\SerializesModels;

class VehicleDone
{
    use SerializesModels;

    public $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
