<?php

namespace App\Filament\Resources\PksDocuments\Schemas;

use App\Filament\Forms\Components\PartnerSelect;
use App\Support\FormLimits;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PksDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            PartnerSelect::make(__('jejak.pks_documents.partner'))->required(),
            TextInput::make('title')->label(__('jejak.pks_documents.title'))->required()->maxLength(255),
            TextInput::make('pks_number')->label(__('jejak.pks_documents.pks_number'))->maxLength(255),
            DatePicker::make('signed_date')->label(__('jejak.pks_documents.signed_date')),
            DatePicker::make('valid_until')->label(__('jejak.pks_documents.valid_until'))->afterOrEqual('signed_date'),
            FileUpload::make('file_path')
                ->label(__('jejak.pks_documents.file_path', ['max' => config('jejak.upload.max_kb') / 1024]))
                ->disk('local')
                ->directory('pks')
                ->visibility('private')
                ->acceptedFileTypes(config('jejak.upload.document_mimes'))
                ->rules(['extensions:pdf,docx'])
                ->maxSize(config('jejak.upload.max_kb'))
                ->preventFilePathTampering()
                ->previewable(false)
                ->required()
                ->columnSpanFull(),
            Textarea::make('notes')->label(__('jejak.pks_documents.notes'))->rows(3)->tap(FormLimits::limit(...))->columnSpanFull(),
        ]);
    }
}
