<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        // 'name', 'description', 'price', 'category_id'
        return $form
            ->schema([
                Forms\Components\TextInput::make("name")
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make("description")
                    ->rows(3)
                    ->maxLength(1000),

                Forms\Components\TextInput::make("price")
                    ->numeric()
                    ->required()
                    ->suffix('COP'), // puedes cambiar la moneda

                Forms\Components\Select::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->label('Description')
                            ->required()
                            ->maxLength(255),
                    ]),
                Forms\Components\FileUpload::make('image')
                    ->label('Imagen')
                    ->image() // activa vista previa
                    ->imageEditor() // opcional, activa edición recorte
                    ->directory('products') // carpeta en storage/app/public/products
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Id'),
                Tables\Columns\ImageColumn::make('image')->label('Imagen'),
                Tables\Columns\TextColumn::make('name')->label('Name'),
                Tables\Columns\TextColumn::make('description')->limit(50)->tooltip(fn($record) => $record->description),
                Tables\Columns\TextColumn::make('price')->label('Precio')->money('cop'),
                Tables\Columns\TextColumn::make('category.name')->label('Category'),
            ])

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
