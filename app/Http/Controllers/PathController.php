<?php

namespace App\Http\Controllers;

use App\Models\Path;

class PathController extends Controller
{
  public function pathsForService($serviceId)
  {
    $service = Path::orderBy('created_at', 'DESC')->where('serviceId', $serviceId)->get();

    return response()->json($service);
  }
}
