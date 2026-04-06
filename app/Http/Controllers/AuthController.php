<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;


class AuthController extends Controller
{
    // public function register(Request $request)
    // {
    //     return response()->json($request->all());
    // }
    public function register(Request $request)
    {
        try {

            Log::info('Register API hit', [
                'payload' => $request->all()
            ]);

            // Validation
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6'
            ]);

            Log::info('Validation passed', [
                'email' => $validated['email']
            ]);

            // Create User
            $user = User::create($validated);

            Log::info('User created successfully', [
                'user_id' => $user->id
            ]);

            // Create Token
            $token = $user->createToken('api-token')->plainTextToken;

            Log::info('Token generated successfully', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'token' => $token
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {

            Log::warning('Validation failed', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {

            Log::error('Register API error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }


    public function login(Request $request)
    {
        try {
            // do not use this lines, because this can print users password and user id as well and it makes authentication insecure
            // Log::info('Login API hit', ['payload' => $request->all()]);

            // this is scure because it print users email and ip only
            Log::info('Login API hit', [
                'email' => $request->input('email'),
                'ip' => $request->ip()
            ]);

            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            Log::info('Validation passed', ['email' => $validated['email']]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                Log::warning('User not found');
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            if (!Hash::check($validated['password'], $user->password)) {
                Log::warning('Invalid password');
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            Log::info('Login successful', ['user' => $user->name]);

            return response()->json([
                'success' => true,
                'token' => $token
            ]);

        } catch (\Exception $e) {
            Log::error('Login error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Server Error'
            ], 500);
        }
    }


    public function logout(Request $request)
    {
        try {

            Log::info('Logout API hit', [
                'user_id' => $request->user()?->id
            ]);

            $user = $request->user();

            if (!$user) {
                Log::warning('Logout attempt without authenticated user');
                
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }

            $token = $user->currentAccessToken();

            if ($token) {
                $token->delete();

                Log::info('User logged out successfully', [
                    'user_id' => $user->id
                ]);
            } else {
                Log::warning('Logout called but no active token found', [
                    'user_id' => $user->id
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ], 200);

        } catch (Throwable $e) {

            Log::error('Logout error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server Error'
            ], 500);
        }
    }
}
