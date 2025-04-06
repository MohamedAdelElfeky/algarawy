<?php
namespace App\Domain\DTO\V2;

class SearchUserDTO
{
    public function __construct(
        public ?string $searchTerm = null,
        public ?int $regionId = null,
        public ?int $cityId = null,
        public ?int $neighborhoodId = null,
        public int $page = 1,
        public int $perPage = 15,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            $request->input('search'),
            $request->input('region_id'),
            $request->input('city_id'),
            $request->input('neighborhood_id'),
            $request->input('page', 1),
            $request->input('per_page', 15)
        );
    }
}
