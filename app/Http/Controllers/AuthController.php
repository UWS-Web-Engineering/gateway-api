<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
  public function register(Request $request)
  {
    $this->validate($request, [
      'username' => 'required|unique:users',
      'password' => 'required',
      'role' => 'required'
    ]);

    try {

      $user = new User;
      $user->username = $request->input('username');
      $user->role = $request->input('role');
      $plainPassword = $request->input('password');
      $user->password = app('hash')->make($plainPassword);

      $user->save();

      return response()->json(['user' => $user, 'message' => 'CREATED'], 201);
    } catch (\Exception $e) {
      return response()->json(['message' => $e], 409);
    }
  }

  /**
   * Get a JWT via given credentials.
   *
   * @param  Request  $request
   * @return Response
   */
  public function login(Request $request)
  {
    //validate incoming request 
    $this->validate($request, [
      'username' => 'required|string',
      'password' => 'required|string',
    ]);

    $credentials = $request->only(['username', 'password']);

    if (!$token = Auth::attempt($credentials)) {
      return response()->json(['message' => 'Unauthorized'], 401);
    }

    return $this->respondWithToken($token);
  }

  public function logout()
  {
    Auth::logout();
    return response("done", 204);
  }
}
