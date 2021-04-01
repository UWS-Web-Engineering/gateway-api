<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Path;
use App\Models\Log;
use Illuminate\Http\Request;


class ServiceController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function index()
  {
    $services = Service::all();

    return response()->json($services);
  }

  public function create(Request $request)
  {
    $this->validate($request, [
      'name' => 'required',
      'key' => 'required|unique:services',
      'secure' => 'required',
      'domain' => 'required',
      'port' => 'required',
      'active' => 'required',
    ]);

    $service = new Service;

    $service->name = $request->input('name');
    $service->description = $request->input('description');
    $service->key = $request->input('key');
    $service->secure = $request->input('secure');
    $service->domain = $request->input('domain');
    $service->port = $request->input('port');
    $service->path = $request->input('path');
    $service->active = $request->input('active');

    $service->save();

    return response()->json($service);
  }

  public function show($id)
  {
    $service = Service::find($id);

    if (!$service) {
      return response("Not found", 404);
    }

    return response()->json($service);
  }

  public function update(Request $request, $id)
  {
    $service = Service::find($id);

    if (!$service) {
      return response("Not found", 404);
    }

    $this->validate($request, [
      'name' => 'required',
      'secure' => 'required',
      'domain' => 'required',
      'port' => 'required',
      'active' => 'required',
    ]);

    $service->name = $request->input('name');
    $service->description = $request->input('description');
    $service->secure = $request->input('secure');
    $service->domain = $request->input('domain');
    $service->port = $request->input('port');
    $service->path = $request->input('path');
    $service->active = $request->input('active');
    $service->save();
    return response()->json($service);
  }

  public function destroy($id)
  {
    $service = Service::find($id);
    if (!$service) {
      return response("Not found", 404);
    }

    Path::where('serviceId', $id)->delete();
    Log::where('serviceId', $id)->delete();

    $service->delete();

    return response()->json('service removed successfully');
  }

  private function health($id)
  {
    $service = Service::find($id);
    if (!$service) {
      return response("Not found", 404);
    }

    $logs = Log::orderBy('created_at', 'DESC')->where('serviceId', $id)->select('statusCode')->take(100)->get();

    $errors = 0;
    foreach ($logs as &$value) {
      if ($value->statusCode < 200 || $value->statusCode > 299) {
        $errors++;
      }
    }

    return $errors / sizeof($logs) * 100;
  }

  public function healths()
  {
    $services = Service::select('id')->get();
    $healths = [];
    foreach ($services as &$value) {
      $healths[$value->id] = $this->health($value->id);
    }

    return response()->json($healths);
  }
}
