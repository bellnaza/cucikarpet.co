<?php

namespace App\Http\Controllers;

use App\Models\CarpetType;
use App\Services\OrderPricing;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function home(): View
    {
        return view('site', [
            'rates' => CarpetType::where('active', true)->pluck('rate_per_sqft', 'code')->map(fn ($r) => (float) $r),
            'freeFrom' => (float) config('cucikarpet.free_pickup_from'),
            'pickupFee' => (float) config('cucikarpet.pickup_fee'),
        ]);
    }

    /** Booking form on the public website → a new order with source "website". */
    public function store(Request $request, OrderService $orders): RedirectResponse
    {
        // Bots fill the hidden "website" field; real visitors never see it.
        if ($request->filled('website')) {
            return redirect()->to(url('/').'#tempah');
        }

        $request->merge(['items' => json_decode((string) $request->input('items_json'), true) ?: []]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{8,}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['required', 'string', 'max:500'],
            'area' => ['nullable', Rule::in(config('cucikarpet.areas'))],
            'pickup_date' => ['nullable', 'date', 'after_or_equal:today', 'before:+60 days'],
            'pickup_slot' => ['nullable', Rule::in(config('cucikarpet.slots'))],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.type' => ['required', 'string', Rule::exists('carpet_types', 'code')->where('active', true)],
            'items.*.w' => ['required', 'numeric', 'min:0.5', 'max:60'],
            'items.*.l' => ['required', 'numeric', 'min:0.5', 'max:60'],
            'items.*.q' => ['nullable', 'integer', 'min:1', 'max:20'],
        ], [
            'items.required' => 'Tambah sekurang-kurangnya satu karpet dalam kalkulator.',
            'phone.regex' => 'Semak nombor telefon. Contoh: 012-345 6789',
        ]);

        // One carpet per row × quantity, so each physical carpet gets its own tag (-01, -02…).
        $ids = CarpetType::pluck('id', 'code');
        $carpets = [];
        foreach ($data['items'] as $row) {
            for ($i = 0; $i < (int) ($row['q'] ?? 1); $i++) {
                $carpets[] = ['carpet_type_id' => $ids[$row['type']], 'width_ft' => $row['w'], 'length_ft' => $row['l']];
            }
        }
        if (count($carpets) > 30) {
            return back()->withErrors(['items' => 'Lebih 30 karpet? Hubungi kami di WhatsApp.'])->withInput();
        }

        $order = $orders->create(
            [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'address' => $data['address'],
                'area' => $data['area'] ?? null,
            ],
            $carpets,
            [
                'pickup_date' => $data['pickup_date'] ?? null,
                'pickup_slot' => $data['pickup_slot'] ?? null,
                'notes' => $data['notes'] ?? null,
                'source' => 'website',
            ],
            notify: true,
        );

        return redirect()->to(url('/').'#tempah')->with('booking', [
            'number' => $order->number,
            'carpets' => $order->items->count(),
            'total' => OrderPricing::rm($order->total),
            'email' => (bool) ($data['email'] ?? null),
        ]);
    }
}
