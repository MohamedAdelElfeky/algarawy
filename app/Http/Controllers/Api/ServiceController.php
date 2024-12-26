<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ServiceService;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    protected $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }
    public function index(Request $request)
    {
        $perPage = $request->header('per_page', 10);
        $page = $request->header('page', 1);

        $user = Auth::guard('sanctum')->user();

        if ($user) {
            return redirect()->route('services.authenticated', ['perPage' => $perPage, 'page' => $page]);
        } else {
            $services = $this->serviceService->getAllServicesPublic($perPage, $page);
        }

        return response()->json($services, 200);
    }

    public function getAuthenticatedServices(Request $request)
    {
        $perPage = $request->query('perPage', 10);
        $page = $request->query('page', 1);

        $user = Auth::user();

        if ($user) {
            $services = $this->serviceService->getAllServices($perPage, $page);
            return response()->json($services, 200);
        } else {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    }

    // public function index(Request $request)
    // {
    //     $perPage = $request->header('per_page');
    //     $page = $request->header('page');
    //     $services = $this->serviceService->getAllServices($perPage, $page);
    //     return response()->json($services, 200);
    // }

    public function getServices(Request $request)
    {
        $perPage = $request->header('per_page');
        $page = $request->header('page');
        $services = $this->serviceService->getAllServicesPublic($perPage, $page);
        return response()->json($services, 200);
    }

    public function show($id)
    {
        $service = $this->serviceService->getServiceById($id);
        return new ServiceResource($service);
    }

    public function store(Request $request)
    {
        $result = $this->serviceService->createService($request->all());
        return new ServiceResource($result['data']);
    }

    public function update(Request $request, $id)
    {
        $service = $this->serviceService->getServiceById($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }
        $result = $this->serviceService->updateService($service, $request->all());
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
