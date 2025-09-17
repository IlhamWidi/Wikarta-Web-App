<?php
namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Branch;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\WhatsappService;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with(['customer', 'branch', 'package', 'teknisi']);
        if ($request->filled('kode')) {
            $query->where('code', 'like', '%' . $request->kode . '%');
        }
        if ($request->filled('nama')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->nama . '%')
                    ->orWhere('phone_number', 'like', '%' . $request->nama . '%');
            });
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $tickets = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $branches = \App\Models\Branch::all();
        return view('pages.support_ticket.index', compact('tickets', 'branches', 'request'));
    }

    public function create()
    {
        $teknisi = User::where('user_type', 'ADMIN')->get();
        return view('pages.support_ticket.create', compact('teknisi'));
    }

    public function searchCustomer(Request $request)
    {
        $q = $request->input('q');
        $customer = User::where(function ($query) use ($q) {
            $query->where('name', 'like', "%$q%")
                ->orWhere('phone_number', 'like', "%$q%")
                ->where('user_type', 'CUSTOMER');
        })
            ->with('branches', 'packages')
            ->first();
        if (!$customer)
            return response()->json(['error' => 'Customer not found'], 404);
        $branch = $customer->branches;
        $package = $customer->packages;
        return response()->json([
            'customer' => $customer,
            'branch' => $branch,
            'package' => $package,
            'address' => $customer->address,
            'kode_server' => $customer->kode_server,
            'password_server' => $customer->password_server,
            'vlan' => $customer->vlan,
            'odp' => $customer->odp,
            'odc' => $customer->odc,
            'keterangan' => $customer->keterangan,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'keluhan' => 'required',
            'teknisi_id' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $last = SupportTicket::orderBy('id', 'desc')->first();
        $increment = $last ? str_pad($last->id + 1, 5, '0', STR_PAD_LEFT) : '00001';
        $code = 'TSP-' . date('ymd') . '-' . $increment;
        $customer = User::findOrFail($request->customer_id);
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('support_tickets', 'public');
        }
        $ticket = SupportTicket::create([
            'code' => $code,
            'customer_id' => $request->customer_id,
            'branch_id' => $customer->branch_id,
            'package_id' => $customer->package_id,
            'address' => $request->address,
            'kode_server' => $request->kode_server,
            'password_server' => $request->password_server,
            'vlan' => $request->vlan,
            'odp' => $request->odp,
            'odc' => $request->odc,
            'keterangan' => $request->keterangan,
            'keluhan' => $request->keluhan,
            'teknisi_id' => $request->teknisi_id,
            'status' => 'REGISTER',
            'photo' => $photoPath,
        ]);

        // Kirim notifikasi WhatsApp ke grup
        $teknisi = User::findOrFail($request->teknisi_id);
        $waService = new WhatsappService();
        $createdBy = auth()->user()->name ?? '-';
        $alamat = $request->address ?? $customer->address ?? null;
        $alamatText = $alamat ? "\nAlamat: $alamat" : "";
        $message = "Tiket baru: $code\nNama: {$customer->name}\nKeluhan: {$request->keluhan}\nPIC: {$teknisi->name}\nTanggal: {$ticket->created_at}{$alamatText}\nCreated By: {$createdBy}";
        $waService->sendText($message);

        return redirect()->route('support_ticket.index')->with('success', 'Tiket support berhasil ditambahkan');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required']);
        $ticket = SupportTicket::findOrFail($id);
        $ticket->status = $request->status;
        $ticket->save();
        return back()->with('success', 'Status tiket diperbarui');
    }
}
