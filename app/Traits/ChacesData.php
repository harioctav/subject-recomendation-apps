<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait ChacesData
{
  /**
   * cacheData
   */
  public function cacheData(string $cacheKey, \Closure $dataCallback, int $ttl = 3600)
  {
    return Cache::remember($cacheKey, $ttl, $dataCallback);
  }
}
