<?php

namespace App\Http\Controllers;

use App\Models\Service;
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
      'url' => 'required',
      'active' => 'required',
    ]);

    $service = new Service;

    $service->name = $request->input('name');
    $service->description = $request->input('description');
    $service->key = $request->input('key');
    $service->url = $request->input('url');
    $service->active = $request->input('active');
    $service->private = $request->input('private');

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
      'url' => 'required',
      'active' => 'required',
    ]);

    $service->name = $request->input('name');
    $service->key = $request->input('key');
    $service->description = $request->input('description');
    $service->url = $request->input('url');
    $service->active = $request->input('active');
    $service->private = $request->input('private');
    $service->save();
    return response()->json($service);
  }

  public function destroy($id)
  {
    $service = Service::find($id);
    if (!$service) {
      return response("Not found", 404);
    }

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

    if (sizeof($logs) === 0) {
      return 100;
    }

    $errors = 0;
    foreach ($logs as &$value) {
      if ($value->statusCode < 200 || $value->statusCode > 299) {
        $errors++;
      }
    }

    return 100 - ($errors / sizeof($logs) * 100);
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
