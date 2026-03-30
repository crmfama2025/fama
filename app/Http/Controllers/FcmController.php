<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\Request;

class FcmController extends Controller
{
    //
    public function saveToken(Request $request)
    {
        $user = $request->user(); // get the authenticated user

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'token' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        // Save or update token
        // FcmToken::updateOrCreate(
        //     [
        //         'user_id' => $user->id,
        //         'token' => $request->token,
        //     ],
        //     [
        //         'device_name' => $request->device_name ?? 'unknown device',
        //         'updated_at' => now(),
        //     ]
        // );
        // If token exists for another user, reassign it
        $fcmToken = FcmToken::where('token', $request->token)->first();

        if ($fcmToken) {
            $fcmToken->user_id = $user->id; // reassign to current user
            $fcmToken->device_name = $request->device_name ?? $fcmToken->device_name ?? 'unknown device';
            $fcmToken->updated_at = now();
            $fcmToken->save();
        } else {
            // Create new token
            FcmToken::create([
                'user_id' => $user->id,
                'token' => $request->token,
                'device_name' => $request->device_name ?? 'unknown device',
            ]);
        }

        return response()->json(['success' => true]);
    }
}
