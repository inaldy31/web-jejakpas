<?php

namespace App\Filament\Resources\Admins\Pages;

use App\Filament\Resources\Admins\AdminResource;
use App\Models\User;
use App\Support\AdminAccounts;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditAdmin extends EditRecord
{
    protected static string $resource = AdminResource::class;

    private ?string $oldName = null;

    protected function beforeSave(): void
    {
        $this->oldName = $this->getRecord()->getOriginal('name');
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();

        if ($record->wasChanged('name')) {
            $editor = Auth::user();

            AdminAccounts::log('updated', $record, $editor instanceof User ? $editor : null, [
                'old' => ['name' => $this->oldName],
                'attributes' => ['name' => $record->name],
            ]);
        }
    }
}
