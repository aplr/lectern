<?php

namespace Aplr\Tests\Lectern;

use Aplr\Lectern\Repository;
use Illuminate\Database\Eloquent\Model;

class TestRepository extends Repository
{
    /**
     * The test model instance
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $testModel;

    public function setTestModel(Model $testModel)
    {
        $this->testModel = $testModel;
    }

    protected function model()
    {
        return $this->testModel;
    }
}