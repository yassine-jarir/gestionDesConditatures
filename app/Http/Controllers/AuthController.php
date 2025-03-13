<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // User Registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'skills' => $request->skills,
            'phone_number' => $request->phone_number,
        ]);

        $token = $user->createToken('tokenYassine123123')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 201);
    }

    // User Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => ['Invalid credentials.']]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 200);
    }

     public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out'], 200);
    }

    public function updateProfile(Request $request)
{
    $user = Auth::user();

    $validatedData = $request->validate([
        'name' => 'string|max:255',
        'email' => 'email|unique:users,email,' . $user->id, // "This email belongs to this user ID, so don’t treat it as a duplicate."
        'phone_number' => 'integer|nullable',
        'skills' => 'array|nullable',  
        'passwords' => 'nullable'
    ]);
/** @var \App\Models\User $user */ 

   if( $user->update($validatedData)){
    return response()->json([
        'message' => 'Profile updated successfully',
        'user' => $user
    ]);   }
   else{
    return response()->json(['message'=> 'Error updating profile'],500);
   }

 
}

}
