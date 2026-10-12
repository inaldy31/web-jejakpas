<?php

namespace App\Filament\Resources\KnowledgeItems\Schemas;

use App\Enums\KnowledgeCategory;
use App\Support\FormLimits;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KnowledgeItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('title')->label(__('jejak.knowledge.title'))->required()->maxLength(255),
            Select::make('category')->label(__('jejak.knowledge.category'))->options(KnowledgeCategory::class)->required(),
            Textarea::make('description')->label(__('jejak.knowledge.description'))->rows(2)->tap(FormLimits::limit(...))->columnSpanFull(),
            FileUpload::make('file_path')
                ->label(__('jejak.knowledge.file_path', ['max' => config('jejak.upload.max_kb') / 1024]))
                ->disk('local')
                ->directory('knowledge')
                ->visibility('private')
                ->acceptedFileTypes(config('jejak.upload.document_mimes'))
                ->rules(['extensions:pdf,docx'])
                ->maxSize(config('jejak.upload.max_kb'))
                ->preventFilePathTampering()
                ->previewable(false)
                ->required()
                ->columnSpanFull(),
        ]);
    }
}
