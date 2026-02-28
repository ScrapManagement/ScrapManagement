<?php

namespace App\Http\Controllers\DashBoard\User;


use App\Models\User\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DashBoard\Role\RoleRequest;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $roles = Role::all();
        return view('DashBoard.Role.view', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('DashBoard.Role.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        //
        Role::create($request->validated());
        return redirect()->route('role.index')->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $role = Role::findOrFail($id);
        return view('DashBoard.Role.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $role = Role::findOrFail($id);
        return view('DashBoard.Role.update', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, string $id)
    {
        //
        $role = Role::findOrFail($id);
        $role->update($request->validated());
        return redirect()->route('role.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $role = Role::findOrFail($id);
        $role->delete();
        return redirect()->route('role.index')->with('success', 'Role deleted successfully.');
    }
}
