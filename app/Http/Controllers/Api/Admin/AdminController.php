<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashBoard\Admin\AdminRequest;
use App\Http\Requests\DashBoard\Admin\UpdateAdminRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin\Admin;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = Admin::get();

        return response()->json([
            'status'  => true,
            'message' => 'Admins retrieved successfully',
            'data'    => AdminResource::collection($admins),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $request)
    {
        $admin = Admin::create($request->toArray());

        return response()->json([
            'status'  => true,
            'message' => 'Admin created successfully',
            'data'    => new AdminResource($admin),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'status'  => false,
                'message' => 'Admin not found',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Admin retrieved successfully',
            'data'    => new AdminResource($admin),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminRequest $request, string $id)
    {

        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'status'  => false,
                'message' => 'Admin not found',
            ], 404);
        }

        $admin->update(
            $request->only(['name', 'email', 'phone'])
        );

        if ($request->filled('password')) {
            if (!Hash::check($request->old_password, $admin->password)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation error',
                    'errors'  => [
                        'old_password' => ['Old password is incorrect'],
                    ],
                ], 422);
            }

            $admin->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Admin updated successfully',
            'data'    => new AdminResource($admin),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function softDelete(string $id)
    {
        $admin = Admin::find($id);

        if (! $admin) {
            return response()->json([
                'status'  => false,
                'message' => 'Admin not found',
            ], 404);
        }

        $admin->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Admin soft deleted successfully',
        ]);
    }

    public function forceDelete(string $id)
    {
        $admin = Admin::withTrashed()->find($id);

        if (! $admin) {
            return response()->json([
                'status'  => false,
                'message' => 'Admin not found',
            ], 404);
        }

        $admin->forceDelete();

        return response()->json([
            'status'  => true,
            'message' => 'Admin permanently deleted',
        ]);
    }

    public function trashed()
    {
        $admins = Admin::onlyTrashed()->latest()->get();
        return response()->json([
            'status'  => true,
            'message' => 'Trashed admins retrieved successfully',
            'data'    => AdminResource::collection($admins),
        ], 200);
    }

    public function restore(string $id)
    {
        $admin = Admin::withTrashed()->find($id);

        if (! $admin) {
            return response()->json([
                'status'  => false,
                'message' => 'Admin not found',
            ], 404);
        }
        
        if (!$admin->trashed()) {
            return response()->json([
                'status'  => false,
                'message' => 'Admin is not deleted',
            ], 400);
        }

        $admin->restore();

        return response()->json([
            'status'  => true,
            'message' => 'Admin restored successfully',
        ]);
    }


    /**
     * Get the authenticated admin profile.
     */
    public function profile()
    {
        $admin = auth()->guard('admin-api')->user();

        return response()->json([
            'status'  => true,
            'message' => 'Profile retrieved successfully',
            'data'    => new AdminResource($admin),
        ], 200);
    }
}
