<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Message;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Konsultan menyetujui / menolak / menyelesaikan janji meet miliknya.
 *  - Online Meet : muncul setelah mahasiswa membayar -> konsultan mengisi link lalu menyetujui
 *  - Offline Meet: konsultan menyetujui waktu & lokasi -> mahasiswa baru membayar
 */
class AppointmentController extends Controller
{
    public function approve(Request $request, $id)
    {
        $appointment = $this->mine($id);

        if ($appointment->status !== 'pending') {
            return back()->with('error', 'Janji ini sudah diproses.');
        }

        if ($appointment->tipe === 'online_meet' && ! $appointment->isPaid()) {
            return back()->with('error', 'Online Meet baru dapat disetujui setelah mahasiswa menyelesaikan pembayaran.');
        }

        $rules = $appointment->tipe === 'online_meet'
            ? ['link_meeting' => ['required', 'url', 'max:255']]
            : ['lokasi' => ['required', 'string', 'max:255']];

        $data = $request->validate($rules, [
            'link_meeting.required' => 'Link meeting wajib diisi untuk online meet.',
            'link_meeting.url' => 'Link meeting harus diawali http:// atau https://',
            'lokasi.required' => 'Lokasi wajib diisi untuk offline meet.',
        ]);

        $appointment->update($data + ['status' => 'approved']);

        if ($appointment->tipe === 'online_meet') {
            $this->notify($appointment, 'Online Meet dikonfirmasi konsultan. Link meeting: ' . $appointment->link_meeting . '.');
        } else {
            $price = number_format((int) config('temuedu.services.offline_meet.price'), 0, ',', '.');
            $this->notify($appointment, 'Offline Meet disetujui konsultan. Lokasi: ' . $appointment->lokasi
                . '. Silakan lakukan pembayaran Rp' . $price . ' untuk mengonfirmasi pertemuan.');
        }

        return back()->with('success', $appointment->tipe === 'online_meet'
            ? 'Online Meet dikonfirmasi.'
            : 'Offline Meet disetujui. Mahasiswa akan diminta melakukan pembayaran.');
    }

    public function reject($id)
    {
        $appointment = $this->mine($id);

        if ($appointment->status !== 'pending') {
            return back()->with('error', 'Janji ini sudah diproses.');
        }

        DB::transaction(function () use ($appointment) {
            $appointment->update(['status' => 'rejected']);
            $appointment->schedule()->update(['is_booked' => false]);     // jadwal dibuka lagi

            // sudah dibayar -> tandai agar admin mengembalikan dana lewat dashboard Midtrans
            Payment::where('appointment_id', $appointment->id)->where('status', 'paid')->update(['status' => 'refund_pending']);
        });

        $this->notify($appointment, 'Janji ' . $this->label($appointment) . ' ditolak konsultan.'
            . ($appointment->isPaid() ? ' Pembayaran akan dikembalikan oleh administrator.' : ' Silakan pilih jadwal lain.'));

        return back()->with('success', $appointment->isPaid()
            ? 'Janji ditolak. Pembayaran mahasiswa ditandai "perlu refund" untuk diproses administrator.'
            : 'Janji ditolak dan jadwal dibuka kembali.');
    }

    public function complete($id)
    {
        $appointment = $this->mine($id);

        if ($appointment->status !== 'approved') {
            return back()->with('error', 'Hanya janji yang sudah disetujui yang dapat diselesaikan.');
        }

        if (! $appointment->isPaid()) {
            return back()->with('error', 'Janji belum dibayar mahasiswa, tidak dapat diselesaikan.');
        }

        $appointment->update(['status' => 'completed']);

        // Online Meet: ruang chat koordinasi ikut ditutup setelah pertemuan selesai
        if ($appointment->tipe === 'online_meet') {
            Consultation::where('id', $appointment->consultation_id)->where('status', 'active')->update(['status' => 'closed']);
        }

        $this->notify($appointment, 'Janji ' . $this->label($appointment) . ' ditandai selesai.');

        return back()->with('success', 'Janji meet ditandai selesai.');
    }

    /** Hanya janji milik konsultan yang login (dan yang memang boleh dilihat konsultan); lainnya -> 404. */
    private function mine($id): Appointment
    {
        $me = Auth::user()->consultant ?? abort(403);

        return Appointment::with('schedule')
            ->visibleToConsultant()
            ->where('consultant_id', $me->id)
            ->findOrFail($id);
    }

    private function label(Appointment $a): string
    {
        return $a->tipe === 'online_meet' ? 'Online Meet' : 'Offline Meet';
    }

    private function notify(Appointment $a, string $text): void
    {
        Message::create([
            'consultation_id' => $a->consultation_id,
            'sender_id' => Auth::id(),
            'tipe' => 'system',
            'isi' => $text,
        ]);
    }
}
