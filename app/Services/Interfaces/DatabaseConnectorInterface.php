<?php

namespace App\Services\Interfaces;

interface DatabaseConnectorInterface
{
    public function archiveDebt(array $debtDetails): void;

    public function restoreDebt(int $debtId): ?array;

    public function restoreMultipleDebts(array $debtIds): array;
}
