<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Jumlah & ringkasan pesan belum dibaca (dipanggil berkala oleh bel notifikasi di navbar).
 * Hanya menghitung pesan dari LAWAN chat pada sesi milik pengguna yang login.
 */
class NotificationController extends Controller
{
    public function unread(Request $request)
    {
        $user = $request->user();

        // satu baris per sesi konsultasi: jumlah pesan belum dibaca + id pesan terakhir
        $rows = Message::unreadFor($user)
            ->selectRaw('messages.consultation_id as consultation_id, COUNT(*) as total, MAX(messages.id) as last_id')
            ->groupBy('messages.consultation_id')
            ->orderByDesc('last_id')
            ->get();

        $consultations = Consultation::with(['user', 'consultant.user'])
            ->whereIn('id', $rows->pluck('consultation_id'))
            ->get()
            ->keyBy('id');

        $lastMessages = Message::whereIn('id', $rows->pluck('last_id'))->get()->keyBy('id');

        $items = $rows->map(function ($row) use ($consultations, $lastMessages, $user) {
            $c = $consultations->get($row->consultation_id);
            $m = $lastMessages->get($row->last_id);

            if (! $c || ! $m) {
                return null;
            }

            // nama lawan chat
            $peer = $user->role === 'konsultan' ? $c->user->name : $c->consultant->user->name;

            return [
                'consultation_id' => (int) $c->id,
                'name' => $peer,
                'count' => (int) $row->total,
                'last_id' => (int) $row->last_id,
                'preview' => Str::limit($m->isi, 80),
                'time' => $m->created_at->diffForHumans(),
                'url' => route('consultation.room', $c->id),
            ];
        })->filter()->values();

        return response()
            ->json([
                'total' => (int) $items->sum('count'),
                'items' => $items->take(6)->values(),
                'latest_id' => (int) ($items->max('last_id') ?? 0),
                'enabled' => (bool) $user->notification_enabled,
            ])
            ->header('Cache-Control', 'no-store');
    }
}
