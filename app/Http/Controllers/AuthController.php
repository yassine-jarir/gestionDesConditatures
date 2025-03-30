<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
 use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

 
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string',
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        $token = JWTAuth::fromUser($user);
    
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'token' => $token
        ], 201);
    }
    
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Successfully logged out']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout'], 500);
        }
    }  
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    
        return response()->json([
            'token' => $token,
            'user' => Auth::user()
        ]);
    }
    

    public function refresh()
    {
        try {
             $token = JWTAuth::getToken();
            
            if (!$token) {
                return response()->json(['error' => 'Token not provided'], 401);
            }
            
         $newToken = JWTAuth::refresh($token);
            
            return response()->json([
                'token' => $newToken,
                'user' => Auth::user()
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not refresh token: ' . $e->getMessage()], 401);
        }
    }

}