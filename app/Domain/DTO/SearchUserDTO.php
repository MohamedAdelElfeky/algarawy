<?php
namespace App\Domain\DTO;

class SearchUserDTO
{
    public function __construct(
        public ?string $searchTerm = null,
        public ?int $regionId = null,
        public ?int $cityId = null,
        public ?int $neighborhoodId = null
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            $request->input('search'),
            $request->input('region_id'),
            $request->input('city_id'),
            $request->input('neighborhood_id')
        );
    }
}
