<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ServiceService;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;

class ServiceController extends Controller
{
    protected $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    public function index(Request $request)
    {
        $perPage = $request->header('per_page');
        $page = $request->header('page');
        $services = $this->serviceService->getAllServices($perPage, $page);
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
        if ($result['success']) {
            return new ServiceResource($result['data']);
        } else {
            return response()->json([
                'message' => 'فشل تحديث الخدمة',
                'errors' => $result['errors'],
            ], 422);
        }
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
