<?php

namespace App\Filament\Resources\PeerCandidates\Tables;

use App\Enums\CandidateStatus;
use App\Models\PeerCandidate;
use Closure;
use DomainException;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class PeerCandidatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label(__('jejak.candidates.columns.code'))->searchable()->sortable(),
                TextColumn::make('label')->label(__('jejak.candidates.columns.label'))->searchable(),
                TextColumn::make('upt.short_name')->label(__('jejak.candidates.columns.upt')),
                TextColumn::make('competency_field')->label(__('jejak.candidates.columns.competency_field'))->badge(),
                TextColumn::make('current_status')->label(__('jejak.candidates.columns.status'))->badge(),
                TextColumn::make('assessments_count')->label(__('jejak.candidates.columns.assessments_count'))->counts('assessments'),
                TextColumn::make('designated_at')->label(__('jejak.candidates.columns.designated_at'))->date('d M Y')
                    ->placeholder(__('jejak.candidates.columns.designated_at_placeholder')),
            ])
            ->filters([
                SelectFilter::make('upt')->label(__('jejak.candidates.filters.upt'))->relationship('upt', 'name'),
                SelectFilter::make('current_status')->label(__('jejak.candidates.filters.status'))->options(CandidateStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('activate')
                    ->label(__('jejak.candidates.activation.action_label'))
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->color('success')
                    ->authorize('update')
                    ->visible(fn (PeerCandidate $record): bool => $record->current_status === CandidateStatus::Siap && ! $record->trashed())
                    ->modalDescription(__('jejak.candidates.activation.modal_description'))
                    ->schema([self::approvedByPositionField(), self::designatedAtField(withDefault: true)])
                    ->action(fn (PeerCandidate $record, array $data) => self::handleDomainAction(
                        fn () => $record->activateAsPeerEducator($data['approved_by_position'], Carbon::parse($data['designated_at'])),
                        'jejak.candidates.activation.success',
                    )),
                Action::make('deactivate')
                    ->label(__('jejak.candidates.deactivation.action_label'))
                    ->icon(Heroicon::OutlinedUserMinus)
                    ->color('danger')
                    ->authorize('update')
                    ->visible(fn (PeerCandidate $record): bool => $record->current_status === CandidateStatus::PeerEducatorAktif && ! $record->trashed())
                    ->modalDescription(__('jejak.candidates.deactivation.modal_description'))
                    ->schema([
                        Textarea::make('reason')->label(__('jejak.candidates.deactivation.reason_label'))->required()->maxLength(500),
                    ])
                    ->action(fn (PeerCandidate $record, array $data) => self::handleDomainAction(
                        fn () => $record->deactivate($data['reason']),
                        'jejak.candidates.deactivation.success',
                    )),
                Action::make('reactivate')
                    ->label(__('jejak.candidates.reactivation.action_label'))
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->color('success')
                    ->authorize('update')
                    ->visible(fn (PeerCandidate $record): bool => $record->current_status === CandidateStatus::Nonaktif && ! $record->trashed())
                    ->requiresConfirmation()
                    ->modalDescription(__('jejak.candidates.reactivation.modal_description'))
                    ->action(fn (PeerCandidate $record) => self::handleDomainAction(
                        fn () => $record->reactivate(),
                        'jejak.candidates.reactivation.success',
                    )),
                Action::make('correctDesignation')
                    ->label(__('jejak.candidates.correction.action_label'))
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->authorize('update')
                    ->visible(fn (PeerCandidate $record): bool => in_array($record->current_status, PeerCandidate::DESIGNATED_STATUSES, true) && ! $record->trashed())
                    ->modalDescription(__('jejak.candidates.correction.modal_description'))
                    ->fillForm(fn (PeerCandidate $record): array => [
                        'approved_by_position' => $record->approved_by_position,
                        'designated_at' => $record->designated_at?->toDateString(),
                    ])
                    ->schema([self::approvedByPositionField(), self::designatedAtField()])
                    ->action(fn (PeerCandidate $record, array $data) => self::handleDomainAction(
                        fn () => $record->correctDesignation($data['approved_by_position'], Carbon::parse($data['designated_at'])),
                        'jejak.candidates.correction.success',
                    )),
                Action::make('profile')
                    ->label(__('jejak.candidates.profile.action_label'))
                    ->icon(Heroicon::OutlinedPrinter)
                    ->authorize('view')
                    ->visible(fn (PeerCandidate $record): bool => $record->current_status === CandidateStatus::PeerEducatorAktif && ! $record->trashed())
                    ->url(fn (PeerCandidate $record): string => route('admin-files.peer-educator-profile', $record), shouldOpenInNewTab: true),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->authorizeIndividualRecords(),
                    RestoreBulkAction::make()->authorizeIndividualRecords(),
                    ForceDeleteBulkAction::make()->authorizeIndividualRecords(),
                ]),
            ])
            ->defaultSort('code');
    }

    private static function approvedByPositionField(): TextInput
    {
        return TextInput::make('approved_by_position')->label(__('jejak.candidates.approved_by_position'))->required()->maxLength(255);
    }

    private static function designatedAtField(bool $withDefault = false): DatePicker
    {
        $field = DatePicker::make('designated_at')->label(__('jejak.candidates.designated_at'))->required()
            ->beforeOrEqual('today')->validationMessages(['before_or_equal' => __('jejak.validation.date_not_future')]);

        return $withDefault ? $field->default(now()) : $field;
    }

    private static function handleDomainAction(Closure $action, string $successMessageKey): void
    {
        try {
            $action();
        } catch (DomainException $exception) {
            Notification::make()->title($exception->getMessage())->danger()->send();

            return;
        }

        Notification::make()->title(__($successMessageKey))->success()->send();
    }
}
