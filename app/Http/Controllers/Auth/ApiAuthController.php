<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{
    public function memberLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            throw ValidationException::withMessages([
                'email' => ['提供的憑證不正確。'],
            ]);
        }

        $token = $member->createToken('member-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $member,
            'type' => 'member',
        ]);
    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = User::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['提供的憑證不正確。'],
            ]);
        }

        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $admin,
            'type' => 'admin',
        ]);
    }

    public function memberRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:members',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $member = Member::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $member->createToken('member-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $member,
            'type' => 'member',
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => '已成功登出']);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'type' => $request->user() instanceof Member ? 'member' : 'admin',
        ]);
    }
}
