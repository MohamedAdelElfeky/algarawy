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
        ])->orderBy('created_at', 'desc')->get();
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

    public function store(Request $request)
    {
        $result = $this->serviceService->createService($request->all());

        if ($result['success']) {
            return redirect()->route('services.show', $result['data']->id)
                ->with('success', 'Service created successfully.');
        } else {
            return back()->withErrors($result['errors'])->withInput();
        }
    }

    public function edit($id)
    {
        $service = $this->serviceService->getServiceById($id);
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $service = $this->serviceService->getServiceById($id);

        if (!$service) {
            return back()->with('error', 'Service not found');
        }

        $result = $this->serviceService->updateService($service, $request->all());

        if ($result['success']) {
            return redirect()->route('services.show', $service->id)
                ->with('success', 'Service updated successfully.');
        } else {
            return back()->withErrors($result['errors'])->withInput();
        }
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

    public function changeStatus(Request $request, Service $service)
    {
        $request->validate([
            'status' => 'in:public,private',
        ]);

        $service->update(['status' => $request->status]);

        return back()->with('status', 'Service status updated successfully!');
    }
}
