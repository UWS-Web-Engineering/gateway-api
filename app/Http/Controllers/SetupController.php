<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class SetupController extends Controller
{
  public function status()
  {
    $admin = User::where('role', 'admin')->first();

    return response()->json(['hasAdmin' => !!$admin], 200);
  }

  public function createAdmin(Request $request)
  {
    $admin = User::where('role', 'admin')->first();

    if ($admin) {
      return response()->json(['message' => 'Admin already created'], 400);
    }

    $this->validate($request, [
      'username' => 'required|unique:users',
      'password' => 'required',
    ]);

    try {

      $user = new User;
      $user->username = $request->input('username');
      $user->role = "admin";
      $plainPassword = $request->input('password');
      $user->password = app('hash')->make($plainPassword);

      $user->save();

      return response()->json(['user' => $user, 'message' => 'CREATED'], 201);
    } catch (\Exception $e) {
      return response()->json(['message' => $e], 409);
    }
  }
}
