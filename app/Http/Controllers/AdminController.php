<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }


    public function index()
    {
        $users = User::all();
        return view('admin.members.index', compact('users'));
    }

    public function create()
    {
        return view('admin.members.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'is_admin' => 'boolean',
            'is_judge_eligible' => 'boolean',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_admin' => $request->is_admin ?? false,
            'is_judge_eligible' => $request->is_judge_eligible ?? true,
            'password' => $request->password ? Hash::make($request->password) : null,
            'login_method' => $request->login_method ?? 'email',
            'allowed_google_id' => $request->allowed_google_id,
        ]);

        return redirect()->route('admin.members.index')->with('success', 'Member added successfully.');
    }

    public function edit(User $member)
    {
        return view('admin.members.edit', compact('member'));
    }

    public function update(Request $request, User $member)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($member->id)],
            'phone' => 'nullable|string|max:20',
            'is_admin' => 'boolean',
            'is_judge_eligible' => 'boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_admin' => $request->is_admin ?? false,
            'is_judge_eligible' => $request->is_judge_eligible ?? true,
            'login_method' => $request->login_method ?? $member->login_method,
            'allowed_google_id' => $request->allowed_google_id ?? $member->allowed_google_id,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $member->update($data);

        return redirect()->route('admin.members.index')->with('success', 'Member updated successfully.');
    }

    public function destroy(User $member)
    {
        $member->delete();
        return redirect()->route('admin.members.index')->with('success', 'Member deleted successfully.');
    }
}
