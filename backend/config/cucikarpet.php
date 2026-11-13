<?php

return [
    // Pickup & delivery is free when the cleaning subtotal reaches this amount (RM).
    'free_pickup_from' => (float) env('CK_FREE_PICKUP_FROM', 150),

    // Charged when the subtotal is above 0 and below free_pickup_from (RM).
    'pickup_fee' => (float) env('CK_PICKUP_FEE', 15),

    // Order numbers look like CK-2611-0001 (prefix-YYMM-sequence).
    'order_prefix' => env('CK_ORDER_PREFIX', 'CK'),

    'whatsapp' => env('CK_WHATSAPP', '601174493244'),
    'whatsapp_display' => env('CK_WHATSAPP_DISPLAY', '+60 11-7449 3244'),

    'areas' => ['Kuala Lumpur', 'Selangor', 'Luar kawasan'],

    'slots' => ['Pagi (9–12)', 'Petang (2–5)', 'Malam (8–10)'],
];
