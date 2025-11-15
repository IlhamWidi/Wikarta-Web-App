<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_customers')->only(['index', 'show']);
        $this->middleware('permission:create_customers')->only('store');
        $this->middleware('permission:edit_customers')->only('update');
        $this->middleware('permission:delete_customers')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = Customer::with(['subscriptions.package', 'creator', 'updater']);

        // Search
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by city
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        // Sort
        $allowedSortColumns = ['id', 'name', 'email', 'customer_code', 'status', 'city', 'created_at', 'updated_at'];
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, $allowedSortColumns) && in_array($sortOrder, ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = $request->get('per_page', 15);
        $customers = $query->paginate($perPage);

        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'installation_address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Generate customer_code
            $lastCustomer = Customer::withTrashed()->orderBy('id', 'desc')->first();
            $nextNumber = $lastCustomer ? (intval(substr($lastCustomer->customer_code, 4)) + 1) : 1;
            $customerCode = 'CUST' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $customer = Customer::create(array_merge(
                $validator->validated(),
                [
                    'customer_code' => $customerCode,
                    'status' => 'pending',
                    'created_by' => $request->user()?->id,
                ]
            ));

            DB::commit();

            return response()->json([
                'message' => 'Customer created successfully',
                'data' => $customer->load('creator')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create customer: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $customer = Customer::with([
            'subscriptions.package',
            'invoices' => fn($q) => $q->latest()->limit(10),
            'payments' => fn($q) => $q->latest()->limit(10),
            'supportTickets' => fn($q) => $q->latest()->limit(10),
            'installations',
            'creator',
            'updater'
        ])->findOrFail($id);

        return response()->json([
            'data' => $customer
        ]);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:customers,email,' . $id,
            'phone' => 'sometimes|required|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'installation_address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'sometimes|required|in:active,suspended,terminated,pending',
            'activation_date' => 'nullable|date',
            'termination_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $customer->update(array_merge(
                $validator->validated(),
                ['updated_by' => $request->user()?->id]
            ));

            DB::commit();

            return response()->json([
                'message' => 'Customer updated successfully',
                'data' => $customer->load('updater')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        // Check if customer has active subscriptions
        if ($customer->subscriptions()->active()->exists()) {
            return response()->json([
                'message' => 'Cannot delete customer with active subscriptions'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $customer->delete();
            DB::commit();

            return response()->json([
                'message' => 'Customer deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
