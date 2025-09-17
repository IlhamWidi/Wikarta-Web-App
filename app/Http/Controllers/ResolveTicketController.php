<?php
namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\WhatsappService;

class ResolveTicketController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tickets = SupportTicket::with(['customer'])
            ->where('teknisi_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $adminUsers = User::where('user_type', Constant::USER_TYPE_ADMIN)
            ->where('id', '!=', $user->id)
            ->get();
        return view('pages.resolve_ticket.index', compact('tickets', 'adminUsers'));
    }

    public function solve(Request $request, $id)
    {
        $request->validate([
            'action' => 'required',
            'feedback' => 'required',
        ]);
        $ticket = SupportTicket::with(['customer', 'teknisi'])->findOrFail($id);
        $oldTeknisi = $ticket->teknisi;
        $newTeknisi = User::find($request->admin_id);

        if ($request->action === 'solved') {
            $ticket->status = 'SELESAI';
        } elseif ($request->action === 'forward') {
            $ticket->status = 'FORWARD_TEKNISI';
            $ticket->teknisi_id = $request->admin_id;
        }
        $ticket->feedback = $request->feedback;
        $ticket->save();

        // Kirim notifikasi WhatsApp ke grup
        $waService = new WhatsappService();
        $customerName = $ticket->customer ? $ticket->customer->name : '-';
        $teknisiName = $ticket->teknisi ? $ticket->teknisi->name : '-';
        $status = $ticket->status;
        $feedback = $ticket->feedback;
        $code = $ticket->code;
        $keluhan = $ticket->keluhan;
        $pesan = "Tiket: $code\nNama: $customerName\nKeluhan: $keluhan\nStatus: $status\nFeedback: $feedback";
        if ($request->action === 'solved') {
            $tanggal = now()->format('d-m-Y H:i');
            $pesan .= "\nTanggal Selesai: $tanggal\nPIC: $teknisiName";
        }
        if ($request->action === 'forward') {
            $pesan .= "\nDiteruskan ke teknisi: $newTeknisi->name";
            $tanggal = now()->format('d-m-Y H:i');
            $pesan .= "\nTanggal Forward: $tanggal";
            $pesan .= "\nCreated By: $oldTeknisi->name";
        }
        $waService->sendText($pesan);

        return back()->with('success', 'Tiket berhasil diupdate.');
    }
}
