<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Services\ServiceService;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{

    public function __construct(private ServiceService $serviceService)
    {
        $this->middleware('optional.auth')->only('index');
        $this->middleware('auth:sanctum')->except('index');
    }
    public function index(Request $request)
    {
        $perPage = $request->header('per_page', 10);
        $page = $request->header('page', 1);
        $services = $this->serviceService->getServices($perPage, $page);
        return response()->json($services, 200);
    }

    public function show($id)
    {
        $service = $this->serviceService->getServiceById($id);
        return new ServiceResource($service);
    }

    public function store(ServiceRequest $request)
    {
        $result = $this->serviceService->createService($request);
        return new ServiceResource($result['data']);
    }

    public function update(ServiceRequest $request, $id)
    {
        $service = $this->serviceService->getServiceById($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }
        $result = $this->serviceService->updateService($service, $request);
        return new ServiceResource($result['data']);
    }

    public function destroy($id)
    {
        $service = $this->serviceService->getServiceById($id);
        if (!$service) {
            return response()->json(['message' => 'الخدمة غير موجودة'], 404);
        }

        $this->serviceService->deleteService($service);

        return response()->json(['message' => 'تم حذف الخدمة بنجاح']);
    }
    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $results = $this->serviceService->searchService($searchTerm);
        return response()->json(['data' => $results]);
    }
}
