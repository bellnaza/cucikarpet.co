@php
    $rm = fn ($v) => 'RM'.number_format((float) $v, 2);
    $date = fn ($d) => $d ? $d->locale('ms')->translatedFormat('l, j F Y') : '-';
@endphp
<x-mail::message>
# Salam {{ $order->customer->name }},

@switch($template)
@case('diterima')
Tempahan cuci karpet anda telah kami terima.

**No. order:** {{ $order->number }}
**Pickup:** {{ $date($order->pickup_date) }}{{ $order->pickup_slot ? ', '.$order->pickup_slot : '' }}
@break
@case('pickup')
Team kami akan datang mengambil karpet anda.

**No. order:** {{ $order->number }}
**Tarikh:** {{ $date($order->pickup_date) }}{{ $order->pickup_slot ? ', '.$order->pickup_slot : '' }}
**Alamat:** {{ $order->customer->address }}

Setiap karpet akan dipasang tag {{ $order->number }}-01, -02 dan seterusnya.
@break
@case('hantar')
Karpet anda sudah bersih dan sedang dihantar hari ini.

**No. order:** {{ $order->number }}
**Perlu dibayar:** {{ $rm($order->total) }} (tunai, DuitNow QR atau pindahan bank)
@break
@default
Terima kasih kerana menggunakan cucikarpet.co. Ini resit anda.

**No. order:** {{ $order->number }}
**Bayaran:** {{ $order->isPaid() ? 'Dibayar'.($order->payment_method ? ' · '.$order->payment_method->getLabel() : '') : 'Belum bayar' }}
@endswitch

<x-mail::table>
| Tag | Karpet | Saiz | Harga |
|:----|:-------|:-----|------:|
@foreach ($order->items as $item)
| {{ $item->tag }} | {{ $item->carpetType->name }} | {{ (float) $item->width_ft }} × {{ (float) $item->length_ft }} kaki ({{ (float) $item->sqft }} sqft) | {{ $rm($item->amount) }} |
@endforeach
| | | Kos cucian | {{ $rm($order->subtotal) }} |
| | | Pickup & hantar | {{ (float) $order->pickup_fee > 0 ? $rm($order->pickup_fee) : 'Percuma' }} |
| | | **Jumlah** | **{{ $rm($order->total) }}** |
</x-mail::table>

@if ($template === 'diterima')
Harga akhir disahkan selepas karpet diukur semasa pickup. Bayaran penuh semasa delivery.
@endif

Terima kasih,<br>
cucikarpet.co<br>
WhatsApp {{ $whatsapp }}
</x-mail::message>
