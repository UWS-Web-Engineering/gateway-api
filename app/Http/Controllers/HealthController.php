<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Carbon\Carbon;

class HealthController extends Controller
{
  public function requestCount()
  {
    $last7Days = Carbon::now()->addDays(-7);
    $last14Days = Carbon::now()->addDays(-14);
    $logs7DaysAgo = Log::where('created_at', '>=', $last7Days)->get();
    $logs14DaysAgo = Log::where('created_at', '<', $last7Days)->where('created_at', '>=', $last14Days)->get();
    return response()->json([
      "7days" => sizeof($logs7DaysAgo),
      "14days" => sizeof($logs14DaysAgo)
    ]);
  }

  public function successRate()
  {
    $last7Days = Carbon::now()->addDays(-7);
    $last14Days = Carbon::now()->addDays(-14);
    $logs7DaysAgo = Log::where('created_at', '>=', $last7Days)->get();
    $logs14DaysAgo = Log::where('created_at', '<', $last7Days)->where('created_at', '>=', $last14Days)->get();

    $errors7days = 0;
    foreach ($logs7DaysAgo as &$value) {
      if ($value->statusCode < 200 || $value->statusCode > 299) {
        $errors7days++;
      }
    }

    $errors14days = 0;
    foreach ($logs14DaysAgo as &$value) {
      if ($value->statusCode < 200 || $value->statusCode > 299) {
        $errors14days++;
      }
    }

    return response()->json([
      "7days" => $errors7days === 0 ? 100 : 100 - ($errors7days / sizeof($logs7DaysAgo) * 100),
      "14days" => $errors14days === 0 ? 100 : 100 - ($errors14days / sizeof($logs14DaysAgo) * 100)
    ]);
  }

  public function avgResponseTime()
  {
    $last7Days = Carbon::now()->addDays(-7);
    $last14Days = Carbon::now()->addDays(-14);
    $logs7DaysAgo = Log::where('created_at', '>=', $last7Days)->get();
    $logs14DaysAgo = Log::where('created_at', '<', $last7Days)->where('created_at', '>=', $last14Days)->get();

    $sum7 = 0;
    foreach ($logs7DaysAgo as &$value) {
      $sum7 +=  $value->responseTime - $value->requestTime;
    }

    $sum14 = 0;
    foreach ($logs14DaysAgo as &$value) {
      $sum14 +=  $value->responseTime - $value->requestTime;
    }

    return response()->json([
      "7days" => $sum7 === 0 ? 0 : round($sum7 / sizeof($logs7DaysAgo)),
      "14days" => $sum14 === 0 ? 0 : round($sum14 / sizeof($logs14DaysAgo))
    ]);
  }

  public function getChartData()
  {
    $dateStart = Carbon::now()->setHour(0)->setMinute(0)->setSecond(0)->setMillisecond(0);
    $dateEnd = Carbon::now()->setHour(23)->setMinute(59)->setSecond(59)->setMicrosecond(999999);

    $data = [];
    for ($i = 0; $i < 14; $i++) {
      $dateStart = $dateStart->addDays(-$i);
      $dateEnd = $dateEnd->addDays(-$i);

      $logs = Log::where('created_at', '<=', $dateEnd)->where('created_at', '>=', $dateStart)->get();

      $failed = 0;
      foreach ($logs as &$value) {
        if ($value->statusCode < 200 || $value->statusCode > 299) {
          $failed++;
        }
      }

      $data["total"][$dateStart->toDateString()] = sizeof($logs);
      $data["failed"][$dateStart->toDateString()] = $failed;
      $data["success"][$dateStart->toDateString()] = sizeof($logs) - $failed;
    }

    return response()->json($data);
  }
}
