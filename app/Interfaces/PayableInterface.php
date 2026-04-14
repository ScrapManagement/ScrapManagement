<?php

namespace App\Interfaces;


interface PayableInterface
{
    public function getAmount(): float;
    public function getPayableId(): int;
    public function getPaymentType(): string; // عشان نفرق هل هو 'insurance' ولا 'package'
}
