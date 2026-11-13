<x-filament-panels::page>
    <style>
        .ck { --ck-surface:#fff; --ck-line:#d8e1f0; --ck-muted:#56658a; --ck-sunk:#eef3fb; --ck-brand:#0b3ba8; --ck-pk:#1e58d6; --ck-dl:#6a3fb5; --ck-ok:#137a45; --ck-warn-bg:#fff1cc; --ck-warn:#6b4e00; display:flex; flex-direction:column; gap:16px; }
        .dark .ck { --ck-surface:#111a33; --ck-line:#24345e; --ck-muted:#9aa9cc; --ck-sunk:#0c1530; --ck-pk:#8db6ff; --ck-dl:#c9a8ff; --ck-ok:#5fd89a; --ck-warn-bg:#3a2c08; --ck-warn:#ffd27a; }
        .ck-ctl { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
        .ck-seg { display:inline-flex; border:1px solid var(--ck-line); border-radius:10px; overflow:hidden; }
        .ck-seg button { padding:8px 14px; font-weight:600; font-size:.88rem; }
        .ck-seg button[aria-pressed="true"] { background:var(--ck-brand); color:#fff; }
        .ck-select { border:1px solid var(--ck-line); border-radius:10px; padding:8px 10px; background:var(--ck-surface); font-size:.9rem; }
        .ck-trip { background:var(--ck-brand); color:#fff; border-radius:14px; padding:16px 18px; display:flex; justify-content:space-between; gap:14px; align-items:center; flex-wrap:wrap; }
        .ck-trip .s { display:flex; gap:24px; flex-wrap:wrap; }
        .ck-trip .s div { display:flex; flex-direction:column; }
        .ck-trip b { font-size:1.35rem; font-weight:600; font-variant-numeric:tabular-nums; }
        .ck-trip span { font-size:.78rem; opacity:.85; }
        .ck-trip a { background:#fff; color:var(--ck-brand); padding:10px 16px; border-radius:10px; font-weight:600; font-size:.9rem; }
        .ck-route { list-style:none; margin:0; padding:0; position:relative; display:flex; flex-direction:column; }
        .ck-route::before { content:""; position:absolute; left:15px; top:18px; bottom:18px; border-left:2px dashed var(--ck-line); }
        .ck-stop { display:grid; grid-template-columns:32px minmax(0,1fr); gap:12px; padding-bottom:14px; position:relative; }
        .ck-dot { width:32px; height:32px; border-radius:50%; display:grid; place-items:center; font-weight:700; font-size:.9rem; color:#fff; position:relative; z-index:1; }
        .ck-start .ck-dot { background:#0b1a3a; } .dark .ck-start .ck-dot { background:#e7edfa; color:#0b1a3a; }
        .ck-pickup .ck-dot { background:var(--ck-pk); } .ck-delivery .ck-dot { background:var(--ck-dl); } .ck-done .ck-dot { background:var(--ck-ok); }
        .dark .ck-pickup .ck-dot, .dark .ck-delivery .ck-dot, .dark .ck-done .ck-dot { color:#0b1a3a; }
        .ck-card { background:var(--ck-surface); border:1px solid var(--ck-line); border-radius:14px; padding:14px; display:flex; flex-direction:column; gap:8px; min-width:0; }
        .ck-done .ck-card { opacity:.6; }
        .ck-row { display:flex; justify-content:space-between; gap:8px; align-items:center; flex-wrap:wrap; }
        .ck-pill { font-size:.75rem; font-weight:700; padding:3px 9px; border-radius:999px; background:var(--ck-sunk); }
        .ck-pickup .ck-pill.k { color:var(--ck-pk); } .ck-delivery .ck-pill.k { color:var(--ck-dl); }
        .ck-who { font-weight:600; font-size:1.05rem; }
        .ck-addr { color:var(--ck-muted); font-size:.9rem; overflow-wrap:anywhere; }
        .ck-note { color:var(--ck-warn); font-size:.85rem; }
        .ck-collect { background:var(--ck-warn-bg); color:var(--ck-warn); font-weight:700; padding:3px 10px; border-radius:8px; font-variant-numeric:tabular-nums; }
        .ck-mono { font-family:ui-monospace,Menlo,monospace; font-size:.82rem; font-weight:600; color:var(--ck-brand); } .dark .ck-mono { color:#8db6ff; }
        .ck-navs { display:flex; gap:6px; flex-wrap:wrap; }
        .ck-btn { border:1px solid var(--ck-line); background:var(--ck-surface); border-radius:10px; padding:7px 12px; font-size:.85rem; font-weight:600; }
        .ck-btn:disabled { opacity:.4; }
        .ck-act { border-top:1px solid var(--ck-line); padding-top:10px; }
        .ck-empty { color:var(--ck-muted); font-size:.9rem; }
    </style>

    <div class="ck">
        <div class="ck-ctl">
            <div class="ck-seg" role="group" aria-label="Hari">
                <button type="button" wire:click="$set('day', 'today')" aria-pressed="{{ $day === 'today' ? 'true' : 'false' }}">Hari ini</button>
                <button type="button" wire:click="$set('day', 'tomorrow')" aria-pressed="{{ $day === 'tomorrow' ? 'true' : 'false' }}">Esok</button>
            </div>
            @if ($teams->isNotEmpty())
                <label class="sr-only" for="ck-team">Team</label>
                <select id="ck-team" class="ck-select" wire:model.live="teamId">
                    <option value="">Semua team</option>
                    @foreach ($teams as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        <div class="ck-trip">
            <div class="s">
                <div><b>{{ $left }} <small style="font-size:.9rem;opacity:.7">/ {{ $jobs->count() }}</small></b><span>hentian tinggal</span></div>
                <div><b>{{ $rugs }}</b><span>karpet</span></div>
                <div><b>RM{{ number_format($collect, 2) }}</b><span>perlu dikutip</span></div>
            </div>
            @if ($routeUrl)
                <a href="{{ $routeUrl }}" target="_blank" rel="noopener">Mula laluan di Google Maps</a>
            @endif
        </div>

        <ol class="ck-route">
            <li class="ck-stop ck-start">
                <span class="ck-dot" aria-hidden="true">●</span>
                <div class="ck-card"><span class="ck-who">Lokasi anda</span></div>
            </li>

            @forelse ($jobs as $i => $job)
                @php
                    $o = $job['order'];
                    $c = $o->customer;
                    $addr = (string) $c->address;
                @endphp
                <li class="ck-stop ck-{{ $job['kind'] }} {{ $job['done'] ? 'ck-done' : '' }}" wire:key="stop-{{ $job['kind'] }}-{{ $o->id }}">
                    <span class="ck-dot">{{ $job['done'] ? '✓' : $i + 1 }}</span>
                    <div class="ck-card">
                        <div class="ck-row">
                            <span class="ck-pill k">{{ $job['kind'] === 'pickup' ? 'Pickup' : 'Delivery' }}</span>
                            <span class="ck-addr">{{ $o->pickup_slot }} · <span class="ck-mono">{{ $o->number }}</span></span>
                        </div>
                        <div class="ck-row">
                            <span class="ck-who">{{ $c->name }}</span>
                            @if ($job['kind'] === 'delivery' && ! $o->isPaid())
                                <span class="ck-collect">Kutip RM{{ number_format((float) $o->total, 2) }}</span>
                            @elseif ($job['kind'] === 'delivery')
                                <span class="ck-pill" style="color:var(--ck-ok)">Dibayar</span>
                            @else
                                <span class="ck-addr">{{ $o->items->count() }} karpet</span>
                            @endif
                        </div>
                        <div class="ck-addr">{{ $addr }}</div>
                        @if ($o->notes)
                            <div class="ck-note">{{ $o->notes }}</div>
                        @endif
                        <div class="ck-navs">
                            <a class="ck-btn" href="https://waze.com/ul?q={{ rawurlencode($addr) }}&navigate=yes" target="_blank" rel="noopener">Waze</a>
                            <a class="ck-btn" href="https://www.google.com/maps/dir/?api=1&destination={{ rawurlencode($addr) }}" target="_blank" rel="noopener">Google Maps</a>
                            <a class="ck-btn" href="https://wa.me/{{ $c->whatsappNumber() }}" target="_blank" rel="noopener">WhatsApp</a>
                            @unless ($job['done'])
                                <button type="button" class="ck-btn" wire:click="move({{ $i }}, -1)" @disabled($i === 0) aria-label="Naikkan hentian">↑</button>
                                <button type="button" class="ck-btn" wire:click="move({{ $i }}, 1)" @disabled($i === $jobs->count() - 1) aria-label="Turunkan hentian">↓</button>
                            @endunless
                        </div>
                        @unless ($job['done'])
                            <div class="ck-act">
                                @if ($job['kind'] === 'pickup')
                                    {{ ($this->pickupAction)(['order' => $o->id]) }}
                                @elseif ($o->status === \App\Enums\OrderStatus::Hantar)
                                    {{ ($this->completeDeliveryAction)(['order' => $o->id]) }}
                                @else
                                    <p class="ck-empty" style="margin-bottom:8px">Status: {{ $o->status->getLabel() }}</p>
                                    {{ ($this->startDeliveryAction)(['order' => $o->id]) }}
                                @endif
                            </div>
                        @endunless
                    </div>
                </li>
            @empty
                <li class="ck-stop">
                    <span></span>
                    <div class="ck-card">
                        <b>Tiada tugasan.</b>
                        <span class="ck-empty">Admin tetapkan tarikh pickup atau delivery dan team dalam order. Tugasan akan muncul di sini.</span>
                    </div>
                </li>
            @endforelse
        </ol>
    </div>
</x-filament-panels::page>
