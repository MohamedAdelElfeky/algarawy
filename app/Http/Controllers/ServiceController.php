<?php

namespace App\Http\Controllers;

use App\Domain\Models\Service;
use App\Domain\Services\ServiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(private ServiceService $service) {}

    public function index(): View
    {
        $services = $this->service->getPaginatedServices(25);
        return view('pages.dashboards.service.index', compact('services'));
    }


    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->service->deleteService($id, 'web');
        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'deleted successfully.' : 'Failed to delete.'
        ]);
    }
}
