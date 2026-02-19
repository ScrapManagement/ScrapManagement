<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashBoard\User\UpdateUserRequest;
use App\Http\Requests\DashBoard\User\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('category')->latest()->get();

        return response()->json([
            'status'  => true,
            'message' => 'Users retrieved successfully',
            'data'    => UserResource::collection($users),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $user = User::create($request->toArray());

        return response()->json([
            'status'  => true,
            'message' => 'User created successfully',
            'data'    => new UserResource($user),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('category')->find($id);

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'User retrieved successfully',
            'data'    => new UserResource($user),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->update(
            $request->only(['name', 'email', 'phone', 'city', 'region', 'category_id'])
        );

        if ($request->filled('password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation error',
                    'errors'  => [
                        'old_password' => ['Old password is incorrect'],
                    ],
                ], 422);
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'User updated successfully',
            'data'    => new UserResource($user),
        ], 200);
    }


    public function softDelete(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status'  => true,
            'message' => 'User soft deleted successfully',
        ], 200);
    }


    public function forceDelete(string $id)
    {
        $user = User::withTrashed()->find($id);

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->forceDelete();

        return response()->json([
            'status'  => true,
            'message' => 'User permanently deleted successfully',
        ], 200);
    }


    public function restore(string $id)
    {
        $user = User::withTrashed()->find($id);

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found',
            ], 404);
        }

        if (!$user->trashed()) {
            return response()->json([
                'status'  => false,
                'message' => 'User is not deleted',
            ], 400);
        }

        $user->restore();

        return response()->json([
            'status'  => true,
            'message' => 'User restored successfully',
            'data'    => new UserResource($user),
        ], 200);
    }


    public function trashed()
    {
        $users = User::onlyTrashed()->with('category')->latest()->get();

        return response()->json([
            'status'  => true,
            'message' => 'Trashed users retrieved successfully',
            'data'    => UserResource::collection($users),
        ], 200);
    }

    public function profile()
    {
        $user = auth()->guard('api')->user();

        return response()->json([
            'status'  => true,
            'message' => 'Profile retrieved successfully',
            'data'    => new UserResource($user->load('category')),
        ], 200);
    }
}
