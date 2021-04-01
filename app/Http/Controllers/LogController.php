<?php

namespace App\Http\Controllers;

use App\Models\Log;

class LogController extends Controller
{
  public function index()
  {
    $logs = Log::join("paths", "paths.id", "=", "logs.pathId")->select("paths.method", "paths.path", "logs.*")->orderBy('created_at', 'DESC')->paginate();

    return response()->json($logs);
  }

  public function logsForService($serviceId)
  {
    $service = Log::join("paths", "paths.id", "=", "logs.pathId")->select("paths.method", "paths.path", "logs.*")->orderBy('created_at', 'DESC')->where('logs.serviceId', $serviceId)->paginate();

    return response()->json($service);
  }
}
