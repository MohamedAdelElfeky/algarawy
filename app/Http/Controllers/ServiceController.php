<?php

namespace App\Http\Controllers;

use App\Domain\Models\Service;
use App\Services\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    public function index()
    {
        $services = Service::with([
            'user',
            'images',
            'pdfs',
            'likes',
            'favorites',
        ])->orderBy('created_at', 'desc')->paginate(25);
        return view('pages.dashboards.service.index', compact('services'));
    }

    public function show($id)
    {
        $service = $this->serviceService->getServiceById($id);
        return view('services.show', compact('service'));
    }

    public function create()
    {
        return view('services.create');
    }


    public function destroy($id)
    {
        $service = $this->serviceService->getServiceById($id);

        if (!$service) {
            return back()->with('error', 'Service not found');
        }

        $this->serviceService->deleteService($service);

        return redirect()->route('services.index')
            ->with('success', 'Service deleted successfully.');
    }
}
