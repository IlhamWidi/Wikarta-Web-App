<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('publicIndex');
        $this->middleware('permission:view_packages')->only(['index', 'show']);
        $this->middleware('permission:create_packages')->only('store');
        $this->middleware('permission:edit_packages')->only('update');
        $this->middleware('permission:delete_packages')->only('destroy');
    }

    // Public endpoint for landing page
    public function publicIndex()
    {
        $packages = Package::active()
            ->featured()
            ->ordered()
            ->get();

        return response()->json([
            'data' => $packages
        ]);
    }

    public function index(Request $request)
    {
        $query = Package::with(['creator', 'updater']);

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $query->ordered();

        $perPage = $request->get('per_page', 15);
        $packages = $query->paginate($perPage);

        return response()->json($packages);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'installation_fee' => 'nullable|numeric|min:0',
            'speed_mbps' => 'required|integer|min:1',
            'quota_gb' => 'nullable|integer|min:0',
            'period' => 'required|in:monthly,quarterly,semiannual,annual',
            'period_months' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'features' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $package = Package::create(array_merge(
                $validator->validated(),
                ['created_by' => $request->user()?->id]
            ));

            DB::commit();

            return response()->json([
                'message' => 'Package created successfully',
                'data' => $package->load('creator')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create package',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $package = Package::with(['creator', 'updater'])->findOrFail($id);

        return response()->json([
            'data' => $package
        ]);
    }

    public function update(Request $request, $id)
    {
        $package = Package::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'installation_fee' => 'nullable|numeric|min:0',
            'speed_mbps' => 'sometimes|required|integer|min:1',
            'quota_gb' => 'nullable|integer|min:0',
            'period' => 'sometimes|required|in:monthly,quarterly,semiannual,annual',
            'period_months' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'features' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $package->update(array_merge(
                $validator->validated(),
                ['updated_by' => $request->user()?->id]
            ));

            DB::commit();

            return response()->json([
                'message' => 'Package updated successfully',
                'data' => $package->load('updater')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update package',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $package = Package::findOrFail($id);

        // Check if package has active subscriptions
        if ($package->subscriptions()->active()->exists()) {
            return response()->json([
                'message' => 'Cannot delete package with active subscriptions'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $package->delete();
            DB::commit();

            return response()->json([
                'message' => 'Package deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete package',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
