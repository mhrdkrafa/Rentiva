<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case BANK_TRANSFER = 'bank_transfer';
    case QRIS = 'qris';
    case CREDIT_CARD = 'credit_card';
    case E_WALLET = 'e_wallet';

    public function label(): string
    {
        return match ($this) {
            self::BANK_TRANSFER => 'Virtual Account (Bank Transfer)',
            self::QRIS => 'QRIS (Gopay, OVO, Dana, ShopeePay)',
            self::CREDIT_CARD => 'Kartu Kredit / Debit',
            self::E_WALLET => 'E-Wallet',
        };
    }
}
