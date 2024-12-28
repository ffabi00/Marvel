<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json(['message' => 'User updated successfully']);
    }

    public function destroy()
    {
        $user = Auth::user();
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Password verified successfully']);
        }

        return response()->json(['message' => 'Password verification failed'], 403);
    }
}
