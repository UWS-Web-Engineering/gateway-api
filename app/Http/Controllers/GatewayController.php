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

    array_shift($uriPaths);
    $serviceKey = array_shift($uriPaths);

    $service = Service::where('key', $serviceKey)->first();

    if (!$service || !$service->active) {
      return response("Not Found", 404);
    }

    $log->serviceId = $service->id;

    $route = join("/", $uriPaths);
    $query = $request->getQueryString();
    if ($query) {
      $route = sprintf("%s?%s", $route, $query);
    }

    $log->url = $route;

    $serviceUrl = "http://";
    if ($service->secure) {
      $serviceUrl = "https://";
    }
    $serviceUrl .= $service->domain;
    $serviceUrl .= ":" . $service->port;
    if ($service->path) {
      $serviceUrl .= "/" . $service->path;
    }

    $path = Path::where('serviceId', $service->id)->where('path', $route)->where('method', $request->method())->first();
    if ($service->manualRoutes) {
      return response("Not found", 404);
    }

    if (!$path) {
      $path = new Path;
      $path->serviceId = $service->id;
      $path->path = $route;
      $path->method = $request->method();
      $path->save();
    }

    $url = "{$serviceUrl}/{$route}";

    $options = ["headers" => $request->header()];

    if ($request->post()) {
      $contentType = $request->header("Content-Type", "application/json");
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

    $log->method = $request->method();

    $log->requestTime = Carbon::now()->timestamp . Carbon::now()->milli;
    $http = Http::send($request->method(), $url, $options);
    $log->responseTime = Carbon::now()->timestamp . Carbon::now()->milli;

    $log->statusCode = $http->status();
    $log->save();

    if (($http->status() === 401 || $http->status() === 403) && !$path->requireAuth) {
      $path->requireAuth = true;
      $path->save();
    }

    return response($http, $http->status(), $http->headers());
  }
}
