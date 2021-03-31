<?php

namespace App\Http\Controllers;

use App\Models\Service;
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
    $service = new Service;

    $service->name = $request->input('name');
    $service->url = $request->input('url');
    $service->active = $request->input('active');

    $service->save();

    return response()->json($service);
  }

  public function show($id)
  {
    $service = Service::find($id);

    return response()->json($service);
  }

  public function update(Request $request, $id)
  {
    $service = Service::find($id);

    $service->name = $request->input('name');
    $service->url = $request->input('url');
    $service->active = $request->input('active');
    $service->save();
    return response()->json($service);
  }

  public function destroy($id)
  {
    $service = Service::find($id);
    $service->delete();

    return response()->json('service removed successfully');
  }
}
