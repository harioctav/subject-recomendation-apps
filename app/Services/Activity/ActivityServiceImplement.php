<?php

namespace App\Services\Activity;

use LaravelEasyRepository\Service;
use App\Repositories\Activity\ActivityRepository;

class ActivityServiceImplement extends Service implements ActivityService{

     /**
     * don't change $this->mainRepository variable name
     * because used in extends service class
     */
     protected $mainRepository;

    public function __construct(ActivityRepository $mainRepository)
    {
      $this->mainRepository = $mainRepository;
    }

    // Define your custom methods :)
}
