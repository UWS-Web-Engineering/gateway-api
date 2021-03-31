<?php

namespace App\Http\Controllers;

use App\Models\Log;

class LogController extends Controller
{
  public function index()
  {
    $logs = Log::orderBy('created_at', 'DESC')->paginate();

    return response()->json($logs);
  }

  public function logsForService($serviceId)
  {
    $service = Log::orderBy('created_at', 'DESC')->where('serviceId', $serviceId)->paginate();

    return response()->json($service);
  }
}
