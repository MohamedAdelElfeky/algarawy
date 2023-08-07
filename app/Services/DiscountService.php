<?php

namespace App\Services;

use App\Models\Discount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\DiscountResource;
use App\Models\FilePdf;
use App\Models\Image;

class DiscountService
{
    protected $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }

    public function getAllDiscounts($perPage = 10, $page = 1)
    {
        $discounts = Discount::paginate($perPage, ['*'], 'page', $page);
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
            'description' => 'required',
            'images_or_video.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:2048',
            'files_pdf.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,mp4|max:2048',
            'location' => 'string|location',
            'discount' => 'nullable',
            'price' => 'required|numeric',
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

        // Handle images/videos
        if (request()->hasFile('files_pdf')) {
            foreach (request()->file('files_pdf') as $key => $item) {
                $pdf = $data['files_pdf'][$key];
                $pdfType = $pdf->getClientOriginalExtension();
                $mimeType = $pdf->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_discount.' . $pdf->getClientOriginalExtension();
                $pdf->move(public_path('discount/pdf/'), $file_name);
                $pdfPath = "discount/pdf/" . $file_name;
                $pdfObject = new FilePdf([
                    'url' => $pdfPath,
                    'mime' => $mimeType,
                    'type' => $pdfType,
                ]);
                $discount->pdfs()->save($pdfObject);
            }
        }
        return [
            'message' => 'Discount created successfully',
            'data' => new DiscountResource($discount),
        ];
    }

    public function updateDiscount(Discount $discount, array $data): array
    {
        $validator = Validator::make($data, [
            'description' => 'sometimes|required',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:2048',
            'files' => 'nullable|array',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,mp4|max:2048',
            'location' => 'required|string|location',
            'discount' => 'nullable',
            'price' => 'sometimes|required|numeric',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }

        $data['user_id'] = Auth::id();
        $discount->update($data);

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

    public function deleteDiscount(string $id)
    {
        $discount = Discount::findOrFail($id);

        if (!$discount) {
            return response()->json(['message' => 'الخصم غير موجود'], 404);
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
