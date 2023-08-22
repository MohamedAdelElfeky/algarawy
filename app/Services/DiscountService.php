<?php

namespace App\Services;

use App\Models\Discount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\DiscountResource;
use App\Models\FilePdf;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class DiscountService
{
    protected $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }

    public function getAllDiscounts($perPage = 10, $page = 1)
    {
        $discountQuery = Discount::orderBy('created_at', 'desc');
        $discounts = $discountQuery->paginate($perPage, ['*'], 'page', $page);
        $discountResource = DiscountResource::collection($discounts);
        $paginationData = $this->paginationService->getPaginationData($discounts);
        return [
            'data' => $discountResource,
            'metadata' => $paginationData,
        ];
    }

    public function createDiscount(array $data): array
    {
        $validator = Validator::make($data, [
            'description' => 'nullable',
            'images_or_video' => 'nullable',
            'images_or_video.*' => 'file|mimes:jpeg,png,jpg,gif,mp4',
            'location' => 'nullable|string|location',
            'discount' => 'nullable',
            'price' => 'nullable|numeric',
            'link' => 'nullable|url',
        ]);
        if ($validator->fails()) {
            return [
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }

        $data['user_id'] = Auth::id();
        $discount = Discount::create($data);
        if (request()->hasFile('images_or_video')) {
            foreach (request()->file('images_or_video') as $key => $item) {
                $image = $data['images_or_video'][$key];
                $imageType = $image->getClientOriginalExtension();
                $mimeType = $image->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_discount.' . $image->getClientOriginalExtension();
                $image->move(public_path('discount/images'), $file_name);
                $imagePath = "discount/images/" . $file_name;
                $imageObject = new Image([
                    'url' => $imagePath,
                    'mime' => $mimeType,
                    'image_type' => $imageType,
                ]);
                $discount->images()->save($imageObject);
            }
        }
        return [
            'success' => true,
            'message' => 'Discount created successfully',
            'data' => new DiscountResource($discount),
        ];
    }

    public function updateDiscount(Discount $discount, array $data): array
    {
        if ($discount->user_id != Auth::id()) {
            return response()->json([
                'message' => 'هذا الخصم ليس من إنشائك',
            ], 200);
        }
        $validator = Validator::make($data, [
            'description' => 'nullable',
            'images_or_video' => 'nullable',
            'images_or_video.*' => 'file|mimes:jpeg,png,jpg,gif,mp4|max:2048',
            'location' => 'nullable|string|location',
            'discount' => 'nullable',
            'price' => 'nullable|numeric',
            'deleted_images_and_videos' => 'nullable',
            'link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }
        $deletedImagesAndVideos = $data['deleted_images_and_videos'] ?? [];
        foreach ($deletedImagesAndVideos as $imageId) {
            $image = Image::find($imageId);
            if ($image) {
                // Delete from storage
                Storage::delete($image->url);
                // Delete from database
                $image->delete();
            }
        }
        $data['user_id'] = Auth::id();
        $discount->update($data);
        if (request()->hasFile('images_or_video')) {
            foreach (request()->file('images_or_video') as $key => $item) {
                $image = $data['images_or_video'][$key];
                $imageType = $image->getClientOriginalExtension();
                $mimeType = $image->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_discount.' . $image->getClientOriginalExtension();
                $image->move(public_path('discount/images'), $file_name);
                $imagePath = "discount/images/" . $file_name;
                $imageObject = new Image([
                    'url' => $imagePath,
                    'mime' => $mimeType,
                    'image_type' => $imageType,
                ]);
                $discount->images()->save($imageObject);
            }
        }
        return [
            'success' => true,
            'message' => 'تم تحديث الخصم بنجاح',
            'data' => new DiscountResource($discount),
        ];
    }



    public function getDiscountById($id): Discount
    {
        $discount = Discount::find($id);
        if (!$discount) {
            abort(404, 'الخصم غير موجود');
        }
        return $discount;
    }

    public function deleteDiscount(Discount $discount)
    {
        if ($discount->user_id != Auth::id()) {
            return response()->json([
                'message' => 'هذا الخصم ليس من إنشائك',
            ], 200);
        }
        $discount->delete();
        return response()->json(['message' => 'تم حذف الخصم بنجاح']);
    }
    public function searchDiscount($searchTerm)
    {
        $discounts = Discount::where(function ($query) use ($searchTerm) {
            $fields = ['description'];
            foreach ($fields as $field) {
                $query->orWhere($field, 'like', '%' . $searchTerm . '%');
            }
        })->get();
        return  DiscountResource::collection($discounts);
    }
}
