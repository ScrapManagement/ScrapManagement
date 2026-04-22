<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashBoard\User\UpdateUserRequest;
use App\Http\Requests\DashBoard\User\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User\User;
use App\Services\Product\ImageService;
use App\Services\User\OtpService;
use App\Services\User\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        try {

            return DB::transaction(function () use ($request) {

                $existingUser = User::where('phone', $request->phone)
                    ->whereNotNull('phone_verified_at')
                    ->first();

                if ($existingUser) {
                    return response()->json([
                        'status' => false,
                        'message' => 'User already exists.'
                    ], 422);
                }

                $user = User::create($request->validated());

                $token = auth('api')->login($user);

                $otpService = app(OtpService::class);
                $smsService = app(SmsService::class);

                $otp = $otpService->generate($user);

                if (! $smsService->sendOtp($user->phone, $otp)) {
                    $user->delete();
                    throw new \Exception('OTP_SEND_FAILED');
                }

                return response()->json([
                    'status'  => true,
                    'message' => 'User created successfully. OTP sent.',
                    'data'    => new UserResource($user),
                    'token_otp'   => $token,
                ], 201);
            });
        } catch (\Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'User creation or OTP sending failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = auth('api')->user();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $verified = app(OtpService::class)->verify($user, $request->otp);

        if (! $verified) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Phone verified successfully'
        ]);
    }

    public function resendOtp()
    {
        try {
            $user = auth('api')->user();

            if (! $user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            if ($user->phone_verified_at) {
                return response()->json([
                    'status' => false,
                    'message' => 'Phone already verified.'
                ], 400);
            }

            $otpService = app(OtpService::class);
            $smsService = app(SmsService::class);

            $otp = $otpService->generate($user);
            $smsService->sendOtp($user->phone, $otp);

            return response()->json([
                'status' => true,
                'message' => 'A new verification code has been sent to your phone.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to resend OTP.',
                'error' => $e->getMessage()
            ], 500);
        }
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

    public function uploadIDCard(Request $request)
    {
        $request->validate([
            'id_card_front' => 'required|image|max:5120',
            'id_card_back'  => 'required|image|max:5120',
            'company'       => 'required|string|max:255',
            'job_title'     => 'required|string|max:255',
        ]);

        $user = auth()->user();

        if ($user->id_card_status === 'approved') {
            return response()->json([
                'status'  => 'false',
                'message' => 'Your ID card is already verified.',
            ], 422);
        }



        if ($user->id_card_front) {
            Storage::disk('public')->delete([$user->id_card_front, $user->id_card_back]);
        }

        $frontPath = ImageService::saveImages([$request->file('id_card_front')], 'id_cards')[0];
        $backPath  = ImageService::saveImages([$request->file('id_card_back')],  'id_cards')[0];

        $user->update([
            'id_card_front'  => $frontPath,
            'id_card_back'   => $backPath,
            'company'        => $request->company,
            'job_title'      => $request->job_title,
            'id_card_status' => 'pending',
        ]);

        return response()->json([
            'status'  => 'true',
            'message' => 'ID card uploaded successfully, pending review.',
            'data'    => [
                'id_card_status' => 'pending',
                'company'        => $user->company,
                'job_title'      => $user->job_title,
                'front_image'    => asset('storage/' . $frontPath),
                'back_image'     => asset('storage/' . $backPath),
            ]
        ], 200);
    }

    public function idCardStatus()
    {
        $user = auth()->user();

        return response()->json([
            'id_card_status'      => $user->id_card_status,
            'id_card_verified_at' => $user->id_card_verified_at,
            'company'             => $user->company,
            'job_title'           => $user->job_title,
        ], 200);
    }

    public function verifyIdCard(Request $request, User $user)
    {

        $data = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $updateData = ([
            'id_card_status'      => $data['status'],
            'id_card_verified_at' => $data['status'] === 'approved' ? now() : null,
        ]);

        if ($data['status'] === 'approved') {
            $updateData['account_type'] = 'auction';
        } else {
            $updateData['account_type'] = 'normal';
        }

        $user->update($updateData);

        return response()->json([
            'status'  => 'true',
            'message' => $data['status'] === 'approved'
                ? 'The ID card has been approved successfully.'
                : 'The ID card has been rejected.',
            'data'    => [
                'id'                  => $user->id,
                'name'                => $user->name,
                'account_type'        => $user->account_type,
                'id_card_status'      => $user->id_card_status,
                'id_card_verified_at' => $user->id_card_verified_at ? $user->id_card_verified_at->format('Y-m-d H:i:s') : null,
            ]
        ], 200);
    }

    public function pendingIdCards()
    {
        $users = User::where('id_card_status', 'pending')
            ->latest()
            ->paginate(20);

        return response()->json([
            'status'  => 'true',
            'message' => 'Pending ID cards retrieved successfully.',
            'data'    => UserResource::collection($users),
        ], 200);
    }

    public function showIdCard(User $user)
    {
        if (!$user) {
            return response()->json(['status' => 'false', 'message' => 'User not found.'], 404);
        }

        if (!$user->id_card_front) {
            return response()->json(['status' => 'false', 'message' => 'No ID card uploaded for this user.'], 404);
        }

        return response()->json([
            'status'  => 'true',
            'message' => 'User ID card details.',
            'data'    => new UserResource($user),
        ], 200);
    }
}
