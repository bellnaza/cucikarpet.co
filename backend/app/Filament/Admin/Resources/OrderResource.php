<?php

namespace App\Filament\Admin\Resources;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Filament\Admin\Resources\OrderResource\OrderActions;
use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Models\CarpetType;
use App\Models\Order;
use App\Services\OrderPricing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Operasi';

    protected static ?string $modelLabel = 'order';

    protected static ?string $pluralModelLabel = 'Order';

    protected static ?string $recordTitleAttribute = 'number';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) Order::active()->count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['number', 'customer.name', 'customer.phone'];
    }

    public static function form(Form $form): Form
    {
        return $form->columns(3)->schema([
            Forms\Components\Group::make()->columnSpan(['lg' => 2])->schema([
                Forms\Components\Section::make('Pelanggan')->schema([
                    Forms\Components\Select::make('customer_id')->label('Pelanggan')
                        ->relationship('customer', 'name')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->name.' · '.$record->phone)
                        ->searchable(['name', 'phone', 'email'])
                        ->preload()
                        ->required()
                        ->createOptionForm(CustomerResource::formSchema())
                        ->createOptionModalHeading('Pelanggan baru'),
                ]),
                Forms\Components\Section::make('Karpet')
                    ->description('Setiap karpet dapat tag ikut turutan: -01, -02, -03… bila order disimpan.')
                    ->schema([
                        Forms\Components\Repeater::make('carpets')->hiddenLabel()
                            ->schema([
                                Forms\Components\Select::make('carpet_type_id')->label('Jenis karpet')
                                    ->options(fn () => CarpetType::selectOptions())->required()->live()->columnSpan(2),
                                Forms\Components\TextInput::make('width_ft')->label('Lebar (kaki)')->numeric()->step(0.5)
                                    ->minValue(0.5)->maxValue(60)->required()->live(debounce: 500),
                                Forms\Components\TextInput::make('length_ft')->label('Panjang (kaki)')->numeric()->step(0.5)
                                    ->minValue(0.5)->maxValue(60)->required()->live(debounce: 500),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->maxItems(30)
                            ->reorderable()
                            ->addActionLabel('+ Tambah karpet'),
                    ]),
                Forms\Components\Section::make('Jadual')->columns(2)->schema([
                    Forms\Components\DatePicker::make('pickup_date')->label('Tarikh pickup')->default(today()->addDay()),
                    Forms\Components\Select::make('pickup_slot')->label('Slot')->options(array_combine(config('cucikarpet.slots'), config('cucikarpet.slots'))),
                    Forms\Components\Select::make('team_id')->label('Team')->relationship('team', 'name', fn (Builder $query) => $query->where('active', true))
                        ->helperText('Tarikh + team diisi = status terus "Pickup dijadual".'),
                    Forms\Components\DatePicker::make('delivery_date')->label('Tarikh delivery')->hiddenOn('create'),
                    Forms\Components\Textarea::make('notes')->label('Nota untuk team')->rows(2)->columnSpanFull()
                        ->placeholder('Contoh: rumah pagar biru, call dulu sebelum sampai'),
                ]),
            ]),
            Forms\Components\Group::make()->columnSpan(['lg' => 1])->schema([
                Forms\Components\Section::make('Ringkasan')->schema([
                    Forms\Components\Placeholder::make('summary')->hiddenLabel()
                        ->content(fn (Get $get) => self::summaryHtml($get('carpets') ?? [])),
                ]),
                Forms\Components\Section::make('Emel pelanggan')->visibleOn('create')->schema([
                    Forms\Components\Toggle::make('notify_customer')->label('Hantar "Tempahan diterima"')->default(true)
                        ->helperText('Hanya jika pelanggan ada alamat emel.'),
                ]),
            ]),
        ]);
    }

    /** Live price summary for the order form. */
    public static function summaryHtml(array $carpets): HtmlString
    {
        $rates = CarpetType::pluck('rate_per_sqft', 'id');
        $lines = [];
        foreach ($carpets as $c) {
            $w = (float) ($c['width_ft'] ?? 0);
            $l = (float) ($c['length_ft'] ?? 0);
            $rate = (float) ($rates[$c['carpet_type_id'] ?? 0] ?? 0);
            $sqft = round($w * $l, 2);
            $lines[] = ['sqft' => $sqft, 'amount' => round($sqft * $rate, 2)];
        }
        $t = OrderPricing::totals($lines);
        $row = fn ($k, $v) => '<div style="display:flex;justify-content:space-between;gap:12px;padding:3px 0"><span style="opacity:.7">'.e($k).'</span><span style="font-variant-numeric:tabular-nums">'.e($v).'</span></div>';

        return new HtmlString(
            $row('Karpet', count($carpets)).
            $row('Keluasan', rtrim(rtrim(number_format($t['total_sqft'], 2), '0'), '.').' sqft').
            $row('Kos cucian', OrderPricing::rm($t['subtotal'])).
            $row('Pickup & hantar', $t['subtotal'] > 0 && ! $t['pickup_fee'] ? 'Percuma' : OrderPricing::rm($t['pickup_fee'])).
            '<div style="font-size:1.75rem;font-weight:600;margin-top:8px;font-variant-numeric:tabular-nums">'.e(OrderPricing::rm($t['total'])).'</div>'.
            '<div style="font-size:.8rem;opacity:.7;margin-top:4px">Percuma dari RM'.e((float) config('cucikarpet.free_pickup_from')).'. Bayar penuh semasa delivery.</div>'
        );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->label('No. order')->searchable()->sortable()->copyable()
                    ->weight('bold')->fontFamily('mono')->color('primary'),
                Tables\Columns\TextColumn::make('customer.name')->label('Pelanggan')->searchable()
                    ->description(fn (Order $record) => $record->customer?->phone),
                Tables\Columns\TextColumn::make('customer.phone')->label('Telefon')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('items_count')->counts('items')->label('Karpet')
                    ->formatStateUsing(fn ($state, Order $record) => $state.' · '.(float) $record->total_sqft.' sqft'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()->sortable(),
                Tables\Columns\TextColumn::make('pickup_date')->label('Pickup')->date('D, j M')->sortable()->placeholder('—'),
                Tables\Columns\TextColumn::make('delivery_date')->label('Delivery')->date('D, j M')->sortable()->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('team.name')->label('Team')->placeholder('—'),
                Tables\Columns\TextColumn::make('payment_status')->label('Bayaran')->badge(),
                Tables\Columns\TextColumn::make('total')->label('Jumlah')->alignEnd()->sortable()
                    ->formatStateUsing(fn ($state) => OrderPricing::rm($state)),
                Tables\Columns\TextColumn::make('source')->label('Sumber')->badge()->color('gray')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')->label('Order')
                    ->placeholder('Semua order')->trueLabel('Aktif sahaja')->falseLabel('Selesai / batal')
                    ->default(true)
                    ->queries(
                        true: fn (Builder $query) => $query->active(),
                        false: fn (Builder $query) => $query->whereIn('status', [OrderStatus::Selesai->value, OrderStatus::Batal->value]),
                        blank: fn (Builder $query) => $query,
                    ),
                Tables\Filters\SelectFilter::make('status')->label('Status')->options(OrderStatus::options())->multiple(),
                Tables\Filters\SelectFilter::make('team_id')->label('Team')->relationship('team', 'name'),
                Tables\Filters\SelectFilter::make('payment_status')->label('Bayaran')->options(PaymentStatus::class),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    OrderActions::next(Tables\Actions\Action::class),
                    OrderActions::markPaid(Tables\Actions\Action::class),
                    OrderActions::whatsapp(Tables\Actions\Action::class),
                    Tables\Actions\EditAction::make(),
                ]),
            ])
            ->recordUrl(fn (Order $record) => self::getUrl('view', ['record' => $record]))
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['customer', 'team']));
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->columns(3)->schema([
            Infolists\Components\Group::make()->columnSpan(['lg' => 2])->schema([
                Infolists\Components\Section::make('Karpet')->schema([
                    Infolists\Components\RepeatableEntry::make('items')->hiddenLabel()->columns(4)->schema([
                        Infolists\Components\TextEntry::make('tag')->label('Tag')->fontFamily('mono')->weight('bold')->color('primary')->copyable(),
                        Infolists\Components\TextEntry::make('carpetType.name')->label('Jenis'),
                        Infolists\Components\TextEntry::make('sqft')->label('Saiz')
                            ->formatStateUsing(fn ($state, $record) => (float) $record->width_ft.' × '.(float) $record->length_ft.' kaki = '.(float) $state.' sqft'),
                        Infolists\Components\TextEntry::make('amount')->label('Harga')
                            ->formatStateUsing(fn ($state, $record) => OrderPricing::rm($state).' (RM'.number_format((float) $record->rate, 2).'/sqft)'),
                    ]),
                ]),
                Infolists\Components\Section::make('Foto')->visible(fn (Order $record) => $record->photos()->exists())->schema([
                    Infolists\Components\RepeatableEntry::make('photos')->hiddenLabel()->grid(4)->schema([
                        Infolists\Components\ImageEntry::make('path')->hiddenLabel()->disk('public')->height(120),
                        Infolists\Components\TextEntry::make('kind')->hiddenLabel()->badge()->color('gray'),
                    ]),
                ]),
                Infolists\Components\Section::make('Pelanggan')->columns(2)->schema([
                    Infolists\Components\TextEntry::make('customer.name')->label('Nama'),
                    Infolists\Components\TextEntry::make('customer.phone')->label('Telefon')->copyable()
                        ->url(fn (Order $record) => 'https://wa.me/'.$record->customer->whatsappNumber(), true),
                    Infolists\Components\TextEntry::make('customer.email')->label('Emel')->placeholder('Tiada (emel tidak dihantar)'),
                    Infolists\Components\TextEntry::make('customer.area')->label('Kawasan')->placeholder('—'),
                    Infolists\Components\TextEntry::make('customer.address')->label('Alamat')->columnSpanFull()
                        ->url(fn (Order $record) => 'https://www.google.com/maps/search/?api=1&query='.urlencode((string) $record->customer->address), true),
                    Infolists\Components\TextEntry::make('notes')->label('Nota')->columnSpanFull()->placeholder('—'),
                ]),
                Infolists\Components\Section::make('Sejarah')->collapsible()->schema([
                    Infolists\Components\RepeatableEntry::make('histories')->hiddenLabel()->columns(3)->schema([
                        Infolists\Components\TextEntry::make('status')->hiddenLabel()->badge(),
                        Infolists\Components\TextEntry::make('note')->hiddenLabel()->placeholder(''),
                        Infolists\Components\TextEntry::make('created_at')->hiddenLabel()->dateTime('j M Y, g:i a')
                            ->suffix(fn ($record) => $record->user ? ' · '.$record->user->name : ''),
                    ]),
                ]),
            ]),
            Infolists\Components\Group::make()->columnSpan(['lg' => 1])->schema([
                Infolists\Components\Section::make('Ringkasan')->schema([
                    Infolists\Components\TextEntry::make('status')->label('Status')->badge(),
                    Infolists\Components\TextEntry::make('payment_status')->label('Bayaran')->badge()
                        ->suffix(fn (Order $record) => $record->payment_method ? ' · '.$record->payment_method->getLabel() : ''),
                    Infolists\Components\TextEntry::make('subtotal')->label('Kos cucian')
                        ->formatStateUsing(fn ($state, Order $record) => OrderPricing::rm($state).' · '.(float) $record->total_sqft.' sqft'),
                    Infolists\Components\TextEntry::make('pickup_fee')->label('Pickup & hantar')
                        ->formatStateUsing(fn ($state) => (float) $state > 0 ? OrderPricing::rm($state) : 'Percuma'),
                    Infolists\Components\TextEntry::make('total')->label('Jumlah')->weight('bold')->size('lg')
                        ->formatStateUsing(fn ($state) => OrderPricing::rm($state)),
                ]),
                Infolists\Components\Section::make('Jadual')->schema([
                    Infolists\Components\TextEntry::make('pickup_date')->label('Pickup')->date('l, j M Y')->placeholder('Belum ditetapkan')
                        ->suffix(fn (Order $record) => $record->pickup_slot ? ' · '.$record->pickup_slot : ''),
                    Infolists\Components\TextEntry::make('delivery_date')->label('Delivery')->date('l, j M Y')->placeholder('Belum ditetapkan'),
                    Infolists\Components\TextEntry::make('team.name')->label('Team')->placeholder('Belum ditetapkan'),
                    Infolists\Components\TextEntry::make('source')->label('Sumber')->badge()->color('gray'),
                ]),
                Infolists\Components\Section::make('Emel')->collapsible()->schema([
                    Infolists\Components\RepeatableEntry::make('emailLogs')->hiddenLabel()->schema([
                        Infolists\Components\TextEntry::make('subject')->hiddenLabel()->weight('medium'),
                        Infolists\Components\TextEntry::make('status')->hiddenLabel()->badge()
                            ->color(fn ($state) => ['sent' => 'success', 'failed' => 'danger'][$state] ?? 'warning')
                            ->formatStateUsing(fn ($state) => ['sent' => 'Dihantar', 'failed' => 'Gagal', 'queued' => 'Dalam baris gilir'][$state] ?? $state),
                    ]),
                ]),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
