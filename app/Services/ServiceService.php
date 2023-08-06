<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ServiceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Collection;

class ServiceService
{
    public function createService(array $data): array
    {
        $validator = Validator::make($data, [
            'description' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:2048',
            'location' => ['string', function ($attribute, $value, $fail) {
                if (!preg_match('/^https:\/\/www\.google\.com\/maps\/.*$/', $value)) {
                    $fail($attribute . ' must be a valid Google Maps link.');
                }
            }],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }
        $data['user_id'] = Auth::id();
        $service = Service::create($data);

        return [
            'success' => true,
            'message' => 'Service created successfully',
            'data' => new ServiceResource($service),
        ];
    }

    public function updateService(Service $service, array $data): array
    {
        $validator = Validator::make($data, [
            'description' => 'sometimes|required|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:2048',
            'location' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }
        $data['user_id'] = Auth::id();
        $service->update($data);

        return [
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => new ServiceResource($service),
        ];
    }

    public function getAllServices()
    {
        $services = Service::all();
        return ServiceResource::collection($services);
    }

    public function getServiceById($id): Service
    {
        $service = Service::find($id);
        if (!$service) {
            abort(404, 'Service not found');
        }
        return $service;
    }

    public function deleteService(string $id)
    {
        $service = Service::findOrFail($id);

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $service->delete();

        return response()->json(['message' => 'Service deleted successfully']);
    }
}
