<?php

namespace App\Http\Controllers\DashBoard\User;

use App\Models\User\User;
use Illuminate\Http\Request;
use App\Models\Product\Category;
use App\Services\User\OtpService;
use App\Services\User\SmsService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\DashBoard\User\UserRequest;
use App\Http\Requests\DashBoard\User\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('category')->latest()->get();
        return view('DashBoard.User.view', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('DashBoard.User.add', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $user = User::create($request->toArray());
        if ($user->phone_verified_at) {
            return back()->withErrors(['phone' => 'الرقم متفعل بالفعل']);
        }
        $otp = app(OtpService::class)->generate($user);
     //   app(SmsService::class)->sendOtp($user->phone, $otp);
        if (! app(SmsService::class)->sendOtp($user->phone, $otp)) {
            return back()->withErrors([
                'phone' => 'فشل إرسال الرسالة'
            ]);
        }

        return to_route('user.index')->with('success', 'User added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('category')->findOrFail($id);
        return view('DashBoard.User.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with('category')->findOrFail($id);
        $categories = Category::whereNull('parent_id')->get();
        return view('DashBoard.User.update', compact('user', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->update(
            $request->only(['name', 'email', 'phone', 'city', 'region', 'category_id'])
        );
        if ($request->filled('password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->withErrors([
                    'old_password' => 'Old password is incorrect'
                ]);
            }
            $user->update([
                'password' => $request->password
            ]);
        }

        return to_route('user.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::findOrFail($id)->delete();
        return to_route('user.index')->with('success', 'User deleted successfully');
    }
}
