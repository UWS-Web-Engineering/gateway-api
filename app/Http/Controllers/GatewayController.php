<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Path;
use App\Models\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GatewayController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  public function index(Request $request)
  {
    $log = new Log;

    $uriPaths = explode("/", $request->path());
    if ($uriPaths == false) {
      return "error";
    }

    $serviceKey = array_shift($uriPaths);

    $service = Service::where('key', $serviceKey)->first();

    if (!$service || !$service->active) {
      return response("Not Found", 404);
    }

    if ($service->private) {
      $authUrl = rtrim(env('AUTH_URL'), '/');
      $http = Http::withHeaders([
        "Accept" => "application/json",
        "Content-Type" => "application/json",
        "Authorization" => $request->header("authorization", ""),
      ])->send("GET", $authUrl . "/api/user/me", []);
      if ($http->status() !== 200) {
        return response("Unauthorized", 401);
      }
    }

    $log->serviceId = $service->id;

    $route = join("/", $uriPaths);
    $query = $request->getQueryString();
    if ($query) {
      $route = sprintf("%s?%s", $route, $query);
    }

    $log->path = "/" . $route;
    $log->method = $request->method();

    $serviceUrl = rtrim($service->url, '/');
    $url = "{$serviceUrl}/{$route}";

    $options = [];

    if ($request->post()) {
      $contentType = $request->header("content-type", "application/json");
      if ($contentType === "application/json") {
        $options["json"] = $request->post();
      }
      if ($contentType === "application/x-www-form-urlencoded") {
        $options["form_params"] = $request->post();
      }
      if ($contentType === "multipart/form-data") {
        $options["multipart"] = $request->post();
      }
    }

    $log->requestTime = Carbon::now()->getPreciseTimestamp(3);
    $http = Http::withHeaders([
      "Accept" => $request->header("accept", "application/json"),
      "Content-Type" => $request->header("content-type", "application/json"),
      "Authorization" => $request->header("authorization", ""),
    ])->send($request->method(), rtrim($url, '/'), $options);
    $log->responseTime = Carbon::now()->getPreciseTimestamp(3);

    $log->statusCode = $http->status();
    $log->save();

    $responseHeaders = ["content-type" => $http->header("content-type")];

    if ($http->header("set-cookie")) {
      $responseHeaders["set-cookie"] = $http->header("set-cookie");
    }

    return response($http, $http->status(), $responseHeaders);
  }
}
