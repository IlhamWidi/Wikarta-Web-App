<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_tickets')->only(['index', 'show']);
        $this->middleware('permission:create_tickets')->only('store');
        $this->middleware('permission:edit_tickets')->only('update');
        $this->middleware('permission:assign_tickets')->only('assign');
        $this->middleware('permission:resolve_tickets')->only('updateStatus');
    }

    public function index(Request $request)
    {
        $query = SupportTicket::with(['customer', 'assignedTo'])
            ->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = $request->get('per_page', 15);

        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $ticket = SupportTicket::create([
            ...$validated,
            'status' => 'open',
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Ticket berhasil dibuat',
            'data' => $ticket->load(['customer', 'assignedTo']),
        ], 201);
    }

    public function show(SupportTicket $ticket)
    {
        return response()->json([
            'data' => $ticket->load(['customer', 'assignedTo', 'creator', 'updater']),
        ]);
    }

    public function update(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $ticket->update([
            ...$validated,
            'updated_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Ticket diperbarui',
            'data' => $ticket->fresh()->load(['customer', 'assignedTo']),
        ]);
    }

    public function assign(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticket->update([
            'assigned_to' => $validated['assigned_to'],
        ]);

        return response()->json([
            'message' => 'Ticket berhasil di-assign',
            'data' => $ticket->fresh()->load(['customer', 'assignedTo']),
        ]);
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'note' => 'nullable|string',
        ]);

        $ticket->update([
            'status' => $validated['status'],
            'notes' => $validated['note'] ?? $ticket->notes,
            'updated_by' => $request->user()->id,
            'resolved_at' => in_array($validated['status'], ['resolved', 'closed']) ? now() : null,
            'closed_at' => $validated['status'] === 'closed' ? now() : null,
        ]);

        return response()->json([
            'message' => 'Status ticket diperbarui',
            'data' => $ticket->fresh()->load(['customer', 'assignedTo']),
        ]);
    }

    public function destroy(SupportTicket $ticket)
    {
        $ticket->delete();

        return response()->json([
            'message' => 'Ticket dihapus',
        ]);
    }
}
