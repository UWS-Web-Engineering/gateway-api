<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
  public function profile()
  {
    return response()->json(Auth::user(), 200);
  }

  public function allUsers()
  {
    return response()->json(User::all(), 200);
  }

  public function singleUser($id)
  {
    try {
      $user = User::findOrFail($id);

      return response()->json($user, 200);
    } catch (\Exception $e) {

      return response()->json(['message' => 'user not found!'], 404);
    }
  }

  public function removeUser($id)
  {
    try {
      $user = User::findOrFail($id);

      $user->delete();
      return response(true, 200);
    } catch (\Exception $e) {

      return response()->json(['message' => 'user not found!'], 404);
    }
  }
}
