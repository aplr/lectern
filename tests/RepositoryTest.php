<?php

namespace Aplr\Tests\Lectern;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    /**
     * Transformer closure
     *
     * @var \Closure
     */
    protected $transformer;

    /**
     * @before
     */
    protected function setUpTransformer()
    {
        $this->transformer = function ($value) {
            return strlen($value);
        };
    }

    protected function transform($value)
    {
        return ($this->transformer)($value);
    }

    public function testQueryAll()
    {
        /** @var \Mockery\MockInterface|\Illuminate\Database\Eloquent\Model */
        $mockModel = Mockery::mock(Model::class);
        $repository = new TestRepository();
        $repository->setTestModel($mockModel);

        $columns = ["test"];
        $expected = collect(["test"]);

        $mockModel->shouldReceive("get")
            ->with($columns)
            ->andReturn($expected);

        $actual = $repository->all($columns);

        $this->assertEquals($expected, $actual);
    }

    public function testQueryAllTransform()
    {
        /** @var \Mockery\MockInterface|\Illuminate\Database\Eloquent\Model */
        $mockModel = Mockery::mock(Model::class);
        $repository = new TestRepository(null, $this->transformer);
        $repository->setTestModel($mockModel);

        $columns = ["test"];
        $returnValue = collect(["test"]);
        $expected = $returnValue->map(function ($value) {
            return $this->transform($value);
        });

        $mockModel->shouldReceive("get")
            ->with($columns)
            ->andReturn($returnValue);

        $actual = $repository->all($columns);

        $this->assertEquals($expected, $actual);
    }

    public function testQueryFind()
    {
        /** @var \Mockery\MockInterface|\Illuminate\Database\Eloquent\Model */
        $mockModel = Mockery::mock(Model::class);
        $repository = new TestRepository();
        $repository->setTestModel($mockModel);

        $id = "testId";
        $columns = ["test"];
        $expected = "ResultModel";

        $mockModel->shouldReceive("find")
            ->with($id, $columns)
            ->andReturn($expected);

        $actual = $repository->find($id, $columns);

        $this->assertEquals($expected, $actual);
    }

    public function testQueryFindNotFound()
    {
        /** @var \Mockery\MockInterface|\Illuminate\Database\Eloquent\Model */
        $mockModel = Mockery::mock(Model::class);
        $repository = new TestRepository();
        $repository->setTestModel($mockModel);

        $id = "testId";
        $expected = null;

        $mockModel->shouldReceive("find")
            ->with($id, Mockery::any())
            ->andReturn($expected);

        $actual = $repository->find($id);

        $this->assertEquals($expected, $actual);
    }

    public function testQueryFindTransform()
    {
        /** @var \Mockery\MockInterface|\Illuminate\Database\Eloquent\Model */
        $mockModel = Mockery::mock(Model::class);
        $repository = new TestRepository(null, $this->transformer);
        $repository->setTestModel($mockModel);

        $id = "testId";
        $columns = ["test"];
        $returnValue = "ResultModel";
        $expected = $this->transform($returnValue);

        $mockModel->shouldReceive("find")
            ->with($id, $columns)
            ->andReturn($returnValue);

        $actual = $repository->find($id, $columns);

        $this->assertEquals($expected, $actual);
    }

    public function testFindMany()
    {
        /** @var \Mockery\MockInterface|\Illuminate\Database\Eloquent\Model */
        $mockModel = Mockery::mock(Model::class);
        $repository = new TestRepository();
        $repository->setTestModel($mockModel);

        $ids = ["testId1", "testId2"];
        $columns = ["test"];
        $expected = collect(["ResultModel1", "ResultModel2"]);

        $mockModel->shouldReceive("findMany")
            ->with($ids, $columns)
            ->andReturn($expected);

        $actual = $repository->findMany($ids, $columns);

        $this->assertEquals($expected, $actual);
    }

    public function testFindManyEmpty()
    {
        /** @var \Mockery\MockInterface|\Illuminate\Database\Eloquent\Model */
        $mockModel = Mockery::mock(Model::class);
        $repository = new TestRepository();
        $repository->setTestModel($mockModel);

        $ids = ["testId1"];
        $columns = ["test"];
        $expected = collect([]);

        $mockModel->shouldReceive("findMany")
            ->with($ids, $columns)
            ->andReturn($expected);

        $actual = $repository->findMany($ids, $columns);

        $this->assertEquals($expected, $actual);
    }

    public function testFindManyTransform()
    {
        /** @var \Mockery\MockInterface|\Illuminate\Database\Eloquent\Model */
        $mockModel = Mockery::mock(Model::class);
        $repository = new TestRepository(null, $this->transformer);
        $repository->setTestModel($mockModel);

        $ids = ["testId1", "testId2"];
        $columns = ["test"];
        $returnValue = collect(["ResultModel1", "ResultModel2"]);
        $expected = $returnValue->map(function ($value) {
            return $this->transform($value);
        });

        $mockModel->shouldReceive("findMany")
            ->with($ids, $columns)
            ->andReturn($returnValue);

        $actual = $repository->findMany($ids, $columns);

        $this->assertEquals($expected, $actual);
    }

    public function testFindOrFail()
    {
        /** @var \Mockery\MockInterface|\Illuminate\Database\Eloquent\Model */
        $mockModel = Mockery::mock(Model::class);
        $repository = new TestRepository();
        $repository->setTestModel($mockModel);

        $id = "testId1";
        $columns = ["test"];
        $expected = collect(["ResultModel"]);

        $mockModel->shouldReceive("findOrFail")
            ->with($id, $columns)
            ->andReturn($expected);

        $actual = $repository->findOrFail($id, $columns);

        $this->assertEquals($expected, $actual);
    }

    public function testFindOrFailNotFound()
    {
        /** @var \Mockery\MockInterface|\Illuminate\Database\Eloquent\Model */
        $mockModel = Mockery::mock(Model::class);
        $repository = new TestRepository();
        $repository->setTestModel($mockModel);

        $id = "testId1";
        $columns = ["test"];
        $expected = collect([]);

        $mockModel->shouldReceive("findOrFail")
            ->andThrow(ModelNotFoundException::class);

        $this->expectException(ModelNotFoundException::class);

        $repository->findOrFail($id, $columns);
    }

    public function testFindOrFailTransform()
    {
        /** @var \Mockery\MockInterface|\Illuminate\Database\Eloquent\Model */
        $mockModel = Mockery::mock(Model::class);
        $repository = new TestRepository(null, $this->transformer);
        $repository->setTestModel($mockModel);

        $id = "testId1";
        $columns = ["test"];
        $returnValue = collect(["ResultModel"]);
        $expected = $returnValue->map(function ($value) {
            return $this->transform($value);
        });

        $mockModel->shouldReceive("findOrFail")
            ->with($id, $columns)
            ->andReturn($returnValue);

        $actual = $repository->findOrFail($id, $columns);

        $this->assertEquals($expected, $actual);
    }

    public function testFirstOrCreate()
    {
        /** @var \Mockery\MockInterface|\Illuminate\Database\Eloquent\Model */
        $mockModel = Mockery::mock(Model::class);
        $repository = new TestRepository();
        $repository->setTestModel($mockModel);

        $attributes = ["testId1"];
        $values = ["testValue1"];
        $expected = "ResultModel";

        $mockModel->shouldReceive("firstOrCreate")
            ->with($attributes, $values)
            ->andReturn($expected);

        $actual = $repository->firstOrCreate($attributes, $values);

        $this->assertEquals($expected, $actual);
    }

    public function testFirstOrCreateTransform()
    {
        /** @var \Mockery\MockInterface|\Illuminate\Database\Eloquent\Model */
        $mockModel = Mockery::mock(Model::class);
        $repository = new TestRepository(null, $this->transformer);
        $repository->setTestModel($mockModel);

        $attributes = ["testId1"];
        $values = ["testValue1"];
        $returnValue = "ResultModel";
        $expected = $this->transform($returnValue);

        $mockModel->shouldReceive("firstOrCreate")
            ->with($attributes, $values)
            ->andReturn($returnValue);

        $actual = $repository->firstOrCreate($attributes, $values);

        $this->assertEquals($expected, $actual);
    }

    public function testUpdateOrCreate()
    {
        /** @var \Mockery\MockInterface|\Illuminate\Database\Eloquent\Model */
        $mockModel = Mockery::mock(Model::class);
        $repository = new TestRepository();
        $repository->setTestModel($mockModel);

        $attributes = ["testId1"];
        $values = ["testValue1"];
        $expected = "ResultModel";

        $mockModel->shouldReceive("updateOrCreate")
            ->with($attributes, $values)
            ->andReturn($expected);

        $actual = $repository->updateOrCreate($attributes, $values);

        $this->assertEquals($expected, $actual);
    }

    public function testUpdateOrCreateTransform()
    {
        /** @var \Mockery\MockInterface|\Illuminate\Database\Eloquent\Model */
        $mockModel = Mockery::mock(Model::class);
        $repository = new TestRepository(null, $this->transformer);
        $repository->setTestModel($mockModel);

        $attributes = ["testId1"];
        $values = ["testValue1"];
        $returnValue = "ResultModel";
        $expected = $this->transform($returnValue);

        $mockModel->shouldReceive("updateOrCreate")
            ->with($attributes, $values)
            ->andReturn($returnValue);

        $actual = $repository->updateOrCreate($attributes, $values);

        $this->assertEquals($expected, $actual);
    }
}
