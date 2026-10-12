<?php

namespace App\Filament\Resources\Partners\Pages;

use App\Domain\Partners\ImportException;
use App\Domain\Partners\PartnerExcelImporter;
use App\Filament\Resources\Partners\PartnerResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

class ListPartners extends ListRecords
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('import')
                ->label(__('jejak.partners.import.action_label'))
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->authorize('create')
                ->schema([
                    FileUpload::make('file')
                        ->label(__('jejak.partners.import.file_label'))
                        ->disk('local')
                        ->directory('imports')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->maxSize(config('jejak.upload.max_kb'))
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $disk = Storage::disk('local');

                    try {
                        $result = app(PartnerExcelImporter::class)->import($disk->path($data['file']));
                    } catch (ImportException $e) {
                        Notification::make()->title(__('jejak.partners.import.failed_title'))->body($e->getMessage())->danger()->send();

                        return;
                    } finally {
                        $disk->delete($data['file']);
                    }

                    $body = collect($result->errors)
                        ->map(fn (string $message, int $row): string => __('jejak.partners.import.row_line', ['row' => $row, 'message' => $message]))
                        ->prepend($result->summary())
                        ->map(fn (string $line): string => '<p>'.e($line).'</p>')
                        ->implode('');

                    Notification::make()
                        ->title(__('jejak.partners.import.completed_title'))
                        ->body($body)
                        ->status($result->errors === [] ? 'success' : 'warning')
                        ->persistent()
                        ->send();
                }),
        ];
    }
}
