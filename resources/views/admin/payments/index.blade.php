@extends('layouts.admin')

@section('title', 'Pembayaran')
@section('heading', 'Pembayaran')

@section('content')
  <div class="stats" style="grid-template-columns:repeat(2,1fr)">
    <div class="stat"><div class="ico g"><i class="fa-solid fa-wallet"></i></div><div><b>Rp{{ number_format($total, 0, ',', '.') }}</b><span>Total pembayaran lunas</span></div></div>
    <div class="stat"><div class="ico o"><i class="fa-solid fa-rotate-left"></i></div><div><b>{{ $refund }}</b><span>Perlu refund (diproses di dashboard Midtrans)</span></div></div>
  </div>

  <div class="card">
    <form method="GET" class="toolbar">
      <select name="status">
        <option value="">Semua status</option>
        @foreach (\App\Models\Payment::LABELS as $k => $v)
          <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
        @endforeach
      </select>
      <select name="service">
        <option value="">Semua layanan</option>
        @foreach (config('temuedu.services') as $k => $svc)
          <option value="{{ $k }}" @selected(request('service') === $k)>{{ $svc['label'] }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-ghost"><i class="fa-solid fa-filter"></i> Terapkan</button>
      @if (request()->hasAny(['status', 'service']))<a href="{{ route('admin.payments.index') }}" class="btn btn-ghost">Reset</a>@endif
    </form>

    @if ($payments->isEmpty())
      <div class="empty"><i class="fa-solid fa-wallet"></i>Belum ada data pembayaran.</div>
    @else
      <div class="table-wrap">
        <table>
          <thead><tr><th>Order</th><th>Mahasiswa</th><th>Konsultan</th><th>Layanan</th><th>Nominal</th><th>Metode</th><th>Status</th><th>Waktu</th></tr></thead>
          <tbody>
            @foreach ($payments as $p)
              <tr>
                <td><small>{{ $p->order_id }}</small></td>
                <td>{{ $p->user->name }}</td>
                <td>{{ $p->consultant->user->name }}</td>
                <td>{{ config('temuedu.services.' . $p->service . '.label', $p->service) }}</td>
                <td>Rp{{ number_format($p->amount, 0, ',', '.') }}</td>
                <td>{{ $p->payment_type ?: '-' }}</td>
                <td>
                  @if ($p->status === 'paid')<span class="badge ok">Lunas</span>
                  @elseif ($p->status === 'pending')<span class="badge" style="background:#fff0e4;color:#c97432">{{ $p->label() }}</span>
                  @elseif ($p->status === 'refund_pending')<span class="badge" style="background:#fff0f2;color:#b43048">{{ $p->label() }}</span>
                  @else<span class="badge off">{{ $p->label() }}</span>@endif
                </td>
                <td>{{ ($p->paid_at ?? $p->created_at)->format('d M Y H:i') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{ $payments->links('admin.partials.pagination') }}
    @endif
  </div>
@endsection
