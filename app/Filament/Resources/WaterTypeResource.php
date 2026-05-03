<?php

namespace App\Filament\Resources;

use UnitEnum;
use BackedEnum;
use App\Models\WaterType;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\WaterTypeResource\Pages;

class WaterTypeResource extends Resource
{
    protected static ?string $model = WaterType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static string|UnitEnum|null $navigationGroup = 'المياه والتركيبة';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'نوع مياه';

    protected static ?string $pluralModelLabel = 'أنواع المياه';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cost_by_size')
                    ->label('تكلفة كل حجم')
                    ->getStateUsing(fn (WaterType $record): string => $record->compositionCostsBySizeFormatted())
                    ->formatStateUsing(
                        fn (string $state): HtmlString => new HtmlString(nl2br(e($state)))
                    )
                    ->html()
                    ->wrap(),
                TextColumn::make('composition_details')
                    ->label('تفاصيل الأحجام والعناصر')
                    ->getStateUsing(fn (WaterType $record): string => $record->compositionDetailsBySizeFormatted())
                    ->formatStateUsing(
                        fn (string $state): HtmlString => new HtmlString(nl2br(e($state)))
                    )
                    ->html()
                    ->wrap(),
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWaterTypes::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
