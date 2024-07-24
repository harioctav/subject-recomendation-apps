<?php

namespace App\Http\Controllers\Locations;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Services\Locations\Regency\RegencyService;
use App\Services\Locations\Village\VillageService;
use App\Services\Locations\District\DistrictService;
use App\Services\Locations\Province\ProvinceService;

class LocationController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(
    protected ProvinceService $provinceService,
    protected RegencyService $regencyService,
    protected DistrictService $districtService,
    protected VillageService $villageService
  ) {
    // 
  }

  public function provinces()
  {
    $provinces = $this->provinceService->getWhere(
      orderBy: 'id',
      orderByType: 'ASC'
    )->get();

    return response()->json($provinces);
  }

  public function regencies($province_id)
  {
    $regencies = $this->regencyService->getWhere(
      wheres: [
        'province_id' => $province_id
      ]
    )->get();

    return response()->json($regencies);
  }

  public function districts($regency_id)
  {
    $districts = $this->districtService->getWhere(
      wheres: [
        'regency_id' => $regency_id
      ]
    )->get();

    return response()->json($districts);
  }

  public function villages($district_id)
  {
    $villages = $this->villageService->getWhere(
      wheres: [
        'district_id' => $district_id
      ]
    )->get();

    return response()->json($villages);
  }

  public function postCode($village_id)
  {
    $village = $this->villageService->findOrFail($village_id);
    if ($village) {
      return response()->json(['post_code' => $village->pos_code]);
    }
    return response()->json(['post_code' => null], 404);
  }
}
