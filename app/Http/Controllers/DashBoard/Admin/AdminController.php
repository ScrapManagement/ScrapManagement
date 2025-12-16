<?php

namespace App\Http\Controllers\DashBoard\Admin;

use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DashBoard\Admin\AdminRequest;
use App\Http\Requests\DashBoard\Admin\UpdateAdminRequest;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Admin = Admin::get();
        return view("DashBoard.Admin.view", compact("Admin"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("DashBoard.Admin.add");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $request)
    {
        Admin::create($request->toArray());
        return to_route("admin.index")->with("success", "Admin added successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //$admin = Admin::findOrfail($id);
        $admin = auth()->guard('admin')->user();
        return view("DashBoard.Admin.show", compact("admin"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $admin = auth()->guard('admin')->user();
        return view('DashBoard.Admin.update', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminRequest $request, string $id)
    {
        $admin = auth()->guard('admin')->user();
        // $admin = Admin::findOrfail($id);

        $admin->update(
            $request->only(['name', 'email', 'phone'])
        );
        if ($request->filled('password')) {

            if (!Hash::check($request->old_password, $admin->password)) {
                return back()->withErrors([
                    'old_password' => 'Old password is incorrect'
                ]);
            }
            $admin->update([
                'password' => $request->password
            ]);
        }


        return to_route("admin.index")->with("success", "Data updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Admin::where("id", $id)->delete();
        return to_route("admin.index")->with("success", "Admin deleted successfully");
    }
}
