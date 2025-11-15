<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Installation;
use Illuminate\Http\Request;

class InstallationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_installations')->only(['index', 'show']);
        $this->middleware('permission:create_installations')->only('store');
        $this->middleware('permission:edit_installations')->only('update');
        $this->middleware('permission:assign_installations')->only('assignTechnician');
        $this->middleware('permission:complete_installations')->only('updateStatus');
    }

    public function index(Request $request)
    {
        $query = Installation::with(['customer', 'package', 'technician'])
            ->latest('scheduled_date');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->technician_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('scheduled_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('scheduled_date', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('installation_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return response()->json($query->paginate($request->get('per_page', 10)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'package_id' => 'required|exists:packages,id',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required',
            'installation_address' => 'required|string',
            'technician_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $installation = Installation::create([
            ...$validated,
            'status' => 'scheduled',
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Jadwal instalasi dibuat',
            'data' => $installation->load(['customer', 'package', 'technician']),
        ], 201);
    }

    public function show(Installation $installation)
    {
        return response()->json([
            'data' => $installation->load(['customer', 'package', 'technician']),
        ]);
    }

    public function update(Request $request, Installation $installation)
    {
        $validated = $request->validate([
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required',
            'installation_address' => 'required|string',
            'technician_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'equipment_used' => 'nullable|string',
        ]);

        $installation->update([
            ...$validated,
            'updated_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Detail instalasi diperbarui',
            'data' => $installation->fresh()->load(['customer', 'package', 'technician']),
        ]);
    }

    public function assignTechnician(Request $request, Installation $installation)
    {
        $validated = $request->validate([
            'technician_id' => 'required|exists:users,id',
        ]);

        $installation->update([
            'technician_id' => $validated['technician_id'],
        ]);

        return response()->json([
            'message' => 'Teknisi ter-assign',
            'data' => $installation->fresh()->load(['customer', 'package', 'technician']),
        ]);
    }

    public function updateStatus(Request $request, Installation $installation)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $installation->update([
            'status' => $validated['status'],
            'completed_date' => $validated['status'] === 'completed' ? now()->toDateString() : null,
            'completed_time' => $validated['status'] === 'completed' ? now()->toTimeString() : null,
            'notes' => $validated['notes'] ?? $installation->notes,
            'updated_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Status instalasi diperbarui',
            'data' => $installation->fresh()->load(['customer', 'package', 'technician']),
        ]);
    }

    public function destroy(Installation $installation)
    {
        $installation->delete();

        return response()->json([
            'message' => 'Jadwal instalasi dihapus',
        ]);
    }
}
