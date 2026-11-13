<?php

namespace App\Filament\Driver\Pages;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\CarpetType;
use App\Models\Order;
use App\Models\Team;
use App\Services\OrderPricing;
use App\Services\OrderService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The team's route for the day: numbered stops (pickups and deliveries) with
 * Waze / Google Maps / WhatsApp buttons, measuring + photos at pickup,
 * and payment collection at delivery.
 */
class TodayRoute extends Page
{
    protected static string $view = 'filament.driver.pages.today-route';

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Laluan saya';

    protected static ?string $title = 'Laluan saya';

    protected static string $routePath = '/';

    public string $day = 'today';

    public ?int $teamId = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->teamId = $user->isAdmin() ? null : $user->team_id;
    }

    public function updatedTeamId($value): void
    {
        // Drivers are always locked to their own team.
        if (! auth()->user()->isAdmin()) {
            $this->teamId = auth()->user()->team_id;
        }
    }

    public function getSubheading(): ?string
    {
        $team = $this->teamId ? Team::find($this->teamId)?->name : 'Semua team';

        return $team.' · '.$this->date()->locale('ms')->translatedFormat('l, j F');
    }

    protected function date()
    {
        return $this->day === 'tomorrow' ? today()->addDay() : today();
    }

    /** @return Collection<int, array{kind:string, order:Order, done:bool}> */
    public function jobs(): Collection
    {
        $date = $this->date()->toDateString();
        $orders = Order::with(['customer', 'items'])
            ->notCancelled()
            ->when($this->teamId, fn ($q) => $q->where('team_id', $this->teamId))
            ->where(fn ($q) => $q->whereDate('pickup_date', $date)->orWhereDate('delivery_date', $date))
            ->get();

        $jobs = collect();
        foreach ($orders as $o) {
            if ($o->pickup_date?->toDateString() === $date) {
                $jobs->push(['kind' => 'pickup', 'order' => $o, 'seq' => $o->pickup_seq,
                    'done' => $o->status->position() >= OrderStatus::Diambil->position()]);
            }
            if ($o->delivery_date?->toDateString() === $date) {
                $jobs->push(['kind' => 'delivery', 'order' => $o, 'seq' => $o->delivery_seq,
                    'done' => $o->status === OrderStatus::Selesai]);
            }
        }

        $slotRank = fn (?string $s) => str_contains((string) $s, 'Pagi') ? 0 : (str_contains((string) $s, 'Petang') ? 1 : 2);

        return $jobs->sort(function ($a, $b) use ($slotRank) {
            return [$a['seq'] ?? 999, $slotRank($a['order']->pickup_slot), $a['order']->number]
                <=> [$b['seq'] ?? 999, $slotRank($b['order']->pickup_slot), $b['order']->number];
        })->values();
    }

    /** One Google Maps link with every remaining stop, in order (Maps allows up to 9 waypoints). */
    public function routeUrl(Collection $jobs): ?string
    {
        $addresses = $jobs->reject(fn ($j) => $j['done'])->map(fn ($j) => $j['order']->customer->address)->filter()->values();
        if ($addresses->isEmpty()) {
            return null;
        }
        $dest = $addresses->pop();
        $way = $addresses->take(9);

        return 'https://www.google.com/maps/dir/?api=1&travelmode=driving&destination='.rawurlencode($dest)
            .($way->isNotEmpty() ? '&waypoints='.rawurlencode($way->implode('|')) : '');
    }

    protected function findOrder(int $id): Order
    {
        $order = Order::with(['customer', 'items'])->findOrFail($id);
        $user = auth()->user();
        abort_unless($user->isAdmin() || $order->team_id === $user->team_id, 403);

        return $order;
    }

    public function pickupAction(): Action
    {
        return Action::make('pickup')
            ->label('Ambil karpet')
            ->icon('heroicon-o-arrow-down-tray')
            ->size('lg')
            ->extraAttributes(['class' => 'w-full'])
            ->modalHeading(fn (array $arguments) => 'Pickup '.Order::find($arguments['order'])?->number)
            ->modalDescription('Ukur setiap karpet dan lekat tag mengikut turutan. Harga dikira semula.')
            ->modalSubmitActionLabel('Sahkan pickup')
            ->fillForm(fn (array $arguments) => [
                'carpets' => $this->findOrder($arguments['order'])->items->map(fn ($i) => [
                    'carpet_type_id' => $i->carpet_type_id,
                    'width_ft' => (float) $i->width_ft,
                    'length_ft' => (float) $i->length_ft,
                ])->all(),
            ])
            ->form([
                Forms\Components\Repeater::make('carpets')->label('Karpet (tag -01, -02… ikut turutan)')
                    ->schema([
                        Forms\Components\Select::make('carpet_type_id')->label('Jenis')->options(fn () => CarpetType::selectOptions())->required()->columnSpanFull(),
                        Forms\Components\TextInput::make('width_ft')->label('Lebar (kaki)')->numeric()->inputMode('decimal')->step(0.5)->minValue(0.5)->maxValue(60)->required(),
                        Forms\Components\TextInput::make('length_ft')->label('Panjang (kaki)')->numeric()->inputMode('decimal')->step(0.5)->minValue(0.5)->maxValue(60)->required(),
                    ])
                    ->columns(2)->minItems(1)->maxItems(30)->addActionLabel('+ Karpet'),
                Forms\Components\FileUpload::make('photos')->label('Foto keadaan asal (pilihan)')
                    ->image()->multiple()->maxFiles(12)->maxSize(8192)
                    ->disk('public')->directory('order-photos')->visibility('public')
                    ->imageResizeMode('contain')->imageResizeTargetWidth('1600'),
            ])
            ->action(function (array $data, array $arguments) {
                $order = $this->findOrder($arguments['order']);
                app(OrderService::class)->confirmPickup($order, array_values($data['carpets']), $data['photos'] ?? [], auth()->user());
                $order->refresh();
                Notification::make()->title('Pickup '.$order->number.' disahkan')
                    ->body($order->items()->count().' karpet · '.OrderPricing::rm($order->total))->success()->send();
            });
    }

    public function startDeliveryAction(): Action
    {
        return Action::make('startDelivery')
            ->label('Mula penghantaran')
            ->icon('heroicon-o-truck')
            ->size('lg')
            ->extraAttributes(['class' => 'w-full'])
            ->requiresConfirmation()
            ->modalHeading(fn (array $arguments) => 'Mula hantar '.Order::find($arguments['order'])?->number.'?')
            ->modalDescription('Pelanggan akan dapat emel "Karpet dalam perjalanan" jika ada alamat emel.')
            ->action(function (array $arguments) {
                $order = $this->findOrder($arguments['order']);
                app(OrderService::class)->changeStatus($order, OrderStatus::Hantar, auth()->user());
                Notification::make()->title($order->number.' dalam penghantaran')->success()->send();
            });
    }

    public function completeDeliveryAction(): Action
    {
        return Action::make('completeDelivery')
            ->label(fn (array $arguments) => ($o = Order::find($arguments['order'] ?? 0)) && ! $o->isPaid()
                ? 'Terima '.OrderPricing::rm($o->total).' & selesai'
                : 'Selesai hantar')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->size('lg')
            ->extraAttributes(['class' => 'w-full'])
            ->modalHeading(fn (array $arguments) => 'Selesai '.Order::find($arguments['order'])?->number)
            ->form(fn (array $arguments) => Order::find($arguments['order'])?->isPaid()
                ? [Forms\Components\Placeholder::make('paid')->label('')->content('Order ini sudah dibayar.')]
                : [
                    Forms\Components\Placeholder::make('amount')->label('Jumlah perlu dikutip')
                        ->content(OrderPricing::rm(Order::find($arguments['order'])?->total)),
                    Forms\Components\Radio::make('payment_method')->label('Pelanggan bayar dengan')->options(PaymentMethod::class)->required(),
                ])
            ->action(function (array $data, array $arguments) {
                $order = $this->findOrder($arguments['order']);
                $method = isset($data['payment_method'])
                    ? ($data['payment_method'] instanceof PaymentMethod ? $data['payment_method'] : PaymentMethod::from($data['payment_method']))
                    : null;
                app(OrderService::class)->completeDelivery($order, $method, auth()->user());
                Notification::make()->title($order->number.' selesai')->body('Resit dihantar jika pelanggan ada emel.')->success()->send();
            });
    }

    /** Move a stop up or down; every stop for the day gets its new position. */
    public function move(int $index, int $direction): void
    {
        $jobs = $this->jobs();
        $target = $index + $direction;
        if ($target < 0 || $target >= $jobs->count()) {
            return;
        }
        $list = $jobs->all();
        [$list[$index], $list[$target]] = [$list[$target], $list[$index]];

        DB::transaction(function () use ($list) {
            foreach (array_values($list) as $pos => $job) {
                $order = $this->findOrder($job['order']->id);
                $field = $job['kind'] === 'pickup' ? 'pickup_seq' : 'delivery_seq';
                if ($order->{$field} !== $pos) {
                    $order->update([$field => $pos]);
                }
            }
        });
    }

    protected function getViewData(): array
    {
        $jobs = $this->jobs();
        $left = $jobs->reject(fn ($j) => $j['done']);

        return [
            'jobs' => $jobs,
            'routeUrl' => $this->routeUrl($jobs),
            'left' => $left->count(),
            'rugs' => $left->sum(fn ($j) => $j['order']->items->count()),
            'collect' => $left->filter(fn ($j) => $j['kind'] === 'delivery' && ! $j['order']->isPaid())->sum(fn ($j) => (float) $j['order']->total),
            'teams' => auth()->user()->isAdmin() ? Team::where('active', true)->orderBy('name')->pluck('name', 'id') : collect(),
        ];
    }
}
