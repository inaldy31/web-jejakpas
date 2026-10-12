<?php

namespace App\Filament\Actions;

use App\Models\Exceptions\PublicationBlocked;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class PublicationActions
{
    public static function record(): array
    {
        return [
            Action::make('publish')
                ->label(__('jejak.publication.publish.label'))
                ->icon(Heroicon::OutlinedGlobeAlt)
                ->color('success')
                ->requiresConfirmation()
                ->modalDescription(__('jejak.publication.publish.modal_description'))
                ->authorize('update')
                ->visible(fn (Model $record): bool => ! $record->isPublished())
                ->action(function (Model $record): void {
                    try {
                        $record->publish();
                    } catch (PublicationBlocked $e) {
                        Notification::make()
                            ->title(__('jejak.publication.publish.blocked_title'))
                            ->body(__('jejak.publication.publish.blocked_body', ['fields' => implode(', ', $e->blockers)]))
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('unpublish')
                ->label(__('jejak.publication.unpublish.label'))
                ->icon(Heroicon::OutlinedEyeSlash)
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription(__('jejak.publication.unpublish.modal_description'))
                ->authorize('update')
                ->visible(fn (Model $record): bool => $record->isPublished())
                ->action(fn (Model $record) => $record->unpublish()),
        ];
    }

    public static function bulkPublish(): BulkAction
    {
        return BulkAction::make('publishSelected')
            ->label(__('jejak.publication.publish_selected.label'))
            ->icon(Heroicon::OutlinedGlobeAlt)
            ->requiresConfirmation()
            ->modalDescription(__('jejak.publication.publish_selected.modal_description'))
            ->authorizeIndividualRecords('update')
            ->action(function (Collection $records): void {
                $skipped = [];

                foreach ($records as $record) {
                    if ($record->isPublished()) {
                        continue;
                    }

                    try {
                        $record->publish();
                    } catch (PublicationBlocked) {
                        $skipped[] = $record;
                    }
                }

                if ($skipped === []) {
                    return;
                }

                Notification::make()
                    ->title(__('jejak.publication.publish_selected.skipped_title'))
                    ->body(__('jejak.publication.publish_selected.skipped_body', [
                        'count' => count($skipped),
                        'items' => implode(', ', array_map(fn (Model $record): string => (string) ($record->code ?? $record->getKey()), $skipped)),
                    ]))
                    ->warning()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
