<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\Request;

class FcmController extends Controller
{
    //
    // public function saveToken(Request $request)
    // {
    //     $user = $request->user(); // get the authenticated user

    //     if (!$user) {
    //         return response()->json(['error' => 'Unauthenticated'], 401);
    //     }

    //     $request->validate([
    //         'token' => 'required|string',
    //         'device_name' => 'nullable|string',
    //     ]);

    //     // Save or update token
    //     // FcmToken::updateOrCreate(
    //     //     [
    //     //         'user_id' => $user->id,
    //     //         'token' => $request->token,
    //     //     ],
    //     //     [
    //     //         'device_name' => $request->device_name ?? 'unknown device',
    //     //         'updated_at' => now(),
    //     //     ]
    //     // );
    //     // If token exists for another user, reassign it
    //     $fcmToken = FcmToken::where('token', $request->token)->first();

    //     if ($fcmToken) {
    //         $fcmToken->user_id = $user->id; // reassign to current user
    //         $fcmToken->device_name = $request->device_name ?? $fcmToken->device_name ?? 'unknown device';
    //         $fcmToken->updated_at = now();
    //         $fcmToken->save();
    //     } else {
    //         // Create new token
    //         FcmToken::create([
    //             'user_id' => $user->id,
    //             'token' => $request->token,
    //             'device_name' => $request->device_name ?? 'unknown device',
    //         ]);
    //     }

    //     return response()->json(['success' => true]);
    // }

    public function saveToken(Request $request)
    {
        $user = $request->user();
        // dd($user);

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'token' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        // Generate or get unique device ID for this browser/device
        if (!session()->has('device_id')) {
            session(['device_id' => uniqid()]);
        }
        $deviceId = session('device_id');
        $userAgent = $request->header('User-Agent');

        // dd($deviceId, $userAgent);
        // dd($request->token);

        // Check if token exists (including soft-deleted)
        $fcmToken = FcmToken::withTrashed()->where('token', $request->token)->first();
        // dd("test");
        // dd($fcmToken);

        if ($fcmToken) {
            // Reassign token to current user and device
            $fcmToken->user_id = $user->id;
            $fcmToken->device_id = $deviceId;
            $fcmToken->device_name = $request->device_name ?? $fcmToken->device_name ?? 'unknown device';
            $fcmToken->user_agent = $userAgent;
            $fcmToken->deleted_at = null; // restore if soft-deleted
            $fcmToken->updated_at = now();
            $fcmToken->save();
        } else {
            // dd("test");
            // Create new token
            FcmToken::create([
                'user_id' => $user->id,
                'token' => $request->token,
                'device_id' => $deviceId,
                'device_name' => $request->device_name ?? 'unknown device',
                'user_agent' => $userAgent,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
