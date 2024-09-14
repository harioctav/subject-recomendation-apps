<?php

namespace App\Repositories\Activity;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Activity;

class ActivityRepositoryImplement extends Eloquent implements ActivityRepository{

    /**
    * Model class to be used in this repository for the common methods inside Eloquent
    * Don't remove or change $this->model variable name
    * @property Model|mixed $model;
    */
    protected $model;

    public function __construct(Activity $model)
    {
        $this->model = $model;
    }

    // Write something awesome :)
}
