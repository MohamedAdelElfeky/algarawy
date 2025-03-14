<?php

namespace App\Http\Controllers\Api;

use App\Domain\Services\ServiceService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Http\Resources\ServiceResource;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(private ServiceService $ServiceService)
    {
        $this->middleware('optional.auth')->only('index');
        $this->middleware('auth:sanctum')->except('index');
    }

    public function index(Request $request)
    {
        $perPage = $request->header('per_page', 10);
        $page = $request->header('page', 1);
        $Services = $this->ServiceService->getServices($perPage, $page);
        return response()->json($Services, 200);
    }

    public function show($id)
    {
        return new ServiceResource($this->ServiceService->getServiceById($id));
    }

    public function store(ServiceRequest $request)
    {
        $Service = $this->ServiceService->createService($request);
        return response()->json($Service, 201);
    }

    public function update(ServiceRequest $request, $id)
    {
        $Service = $this->ServiceService->getServiceById($id);
        $updatedService = $this->ServiceService->updateService($Service, $request);
        return response()->json($updatedService);
    }

    public function destroy($id)
    {
        return $this->ServiceService->deleteService($id);
    }

    public function search(Request $request)
    {
        return $this->ServiceService->searchService($request->get('search'));
    }
}