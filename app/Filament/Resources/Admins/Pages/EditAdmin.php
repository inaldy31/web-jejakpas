<?php

namespace App\Filament\Resources\Admins\Pages;

use App\Filament\Resources\Admins\AdminResource;
use App\Models\Upt;
use App\Models\User;
use App\Support\AdminAccounts;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EditAdmin extends EditRecord
{
    protected static string $resource = AdminResource::class;

    private ?string $oldName = null;

    private ?int $oldUptId = null;

    protected function beforeSave(): void
    {
        $this->oldName = $this->getRecord()->getOriginal('name');
        $this->oldUptId = $this->getRecord()->getOriginal('upt_id');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->forceFill([
            'name' => $data['name'],
            'upt_id' => $record->is_primary_admin ? null : ($data['upt_id'] ?? null),
        ])->save();

        return $record;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();

        if ($record->wasChanged(['name', 'upt_id'])) {
            $editor = Auth::user();

            AdminAccounts::log('updated', $record, $editor instanceof User ? $editor : null, [
                'old' => ['name' => $this->oldName, 'upt' => $this->uptName($this->oldUptId)],
                'attributes' => ['name' => $record->name, 'upt' => $this->uptName($record->upt_id)],
            ]);
        }
    }

    private function uptName(?int $id): ?string
    {
        return $id === null ? null : Upt::find($id)?->name;
    }
}
