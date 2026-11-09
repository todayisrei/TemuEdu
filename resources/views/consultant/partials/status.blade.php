@php
    if ($c->status === 'active') {
        $label = $c->replied ? 'Berlangsung' : 'Menunggu';
        $cls = $c->replied ? 'ok' : 'warn';
    } elseif ($c->status === 'closed') {
        $label = 'Selesai';
        $cls = '';
    } else {
        $label = 'Berakhir';
        $cls = 'off';
    }
@endphp
<span class="badge {{ $cls }}">{{ $label }}</span>
