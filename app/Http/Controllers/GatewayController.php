<?php

namespace App\Http\Controllers;

use App\Models\Service;
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
    $serviceName = array_shift($uriPaths);

    $service = Service::where('name', $serviceName)->first();

    if (!$service) {
      return response("Not Found", 404);
    }

    $log->serviceId = $service->id;

    $route = join("/", $uriPaths);
    $query = $request->getQueryString();
    if ($query) {
      $route = sprintf("%s?%s", $route, $query);
    }

    $log->url = $route;
    $url = "{$service->url}/{$route}";

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

    return response($http, $http->status(), $http->headers());
  }
}
