<?php

namespace Aplr\Lectern;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Container\Container;
use Illuminate\Pagination\Paginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Repository implements RepositoryInterface
{
    /**
     * The model handled by the repository
     *
     * @var string|null
     */
    private $model;

    /**
     * The query result transformer
     *
     * @var \Closure|null
     */
    private $transformer;

    /**
     * The repository constructor.
     *
     * @param string $model
     * @param \Closure $transformer
     */
    public function __construct(string $model = null, Closure $transformer = null)
    {
        $this->model = $model;
        $this->transformer = $transformer;
    }

    public function all($columns = ['*'])
    {
        return $this->applyTransform(
            $this->model()->get($columns)
        );
    }

    /**
     * @inheritDoc
     */
    public function find($id, $columns = ['*'])
    {
        return $this->applyTransform(
            $this->model()->find($id, $columns)
        );
    }

    /**
     * @inheritDoc
     */
    public function findMany($ids, $columns = ['*'])
    {
        return $this->applyTransform(
            $this->model()->findMany($ids, $columns)
        );
    }

    /**
     * @inheritDoc
     */
    public function findOrFail($id, $columns = ['*'])
    {
        return $this->applyTransform(
            $this->model()->findOrFail($id, $columns)
        );
    }

    /**
     * @inheritDoc
     */
    public function firstOrCreate(array $attributes, array $values = [])
    {
        return $this->applyTransform(
            $this->model()->firstOrCreate($attributes, $values)
        );
    }

    /**
     * @inheritDoc
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        return $this->applyTransform(
            $this->model()->updateOrCreate($attributes, $values)
        );
    }

    /**
     * @inheritDoc
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $paginator = $this->model()->paginate($perPage, $columns, $pageName, $page);

        $options = [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName
        ];

        $transformedItems = $this->applyTransform($paginator->items());

        return Container::getInstance()->makeWith(LengthAwarePaginator::class, [
            $transformedItems,
            $paginator->total(),
            $paginator->perPage(),
            $paginator->currentPage(),
            $options
        ]);
    }

    /**
     * @inheritDoc
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $paginator = $this->model()->simplePaginate($perPage, $columns, $pageName, $pageName);

        $options = [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName
        ];

        $transformedItems = $this->applyTransform($paginator->items());

        return Container::getInstance()->makeWith(Paginator::class, [
            $transformedItems,
            $paginator->perPage(),
            $paginator->currentPage(),
            $options
        ]);
    }

    /**
     * @inheritDoc
     */
    public function create(array $attributes = [])
    {
        return $this->applyTransform(
            $this->model()->create($attributes)
        );
    }

    /**
     * @inheritDoc
     */
    public function forceCreate(array $attributes)
    {
        return $this->applyTransform(
            $this->model()->forceCreate($attributes)
        );
    }

    /**
     * @inheritDoc
     */
    public function update(string $id, array $attributes = [], array $options = [])
    {
        if (null === ($model = $this->find($id))) {
            return null;
        }

        return $model->update($attributes, $options);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $id)
    {
        if (null === ($model = $this->find($id))) {
            return null;
        }

        return $model->delete();
    }

    /**
     * @inheritDoc
     */
    public function forceDelete(string $id)
    {
        if (null === ($model = $this->find($id))) {
            return null;
        }

        return $model->forceDelete();
    }

    /**
     * @inheritDoc
     */
    public function query(Closure $callback)
    {
        return $this->applyTransform(
            $callback($this->model())
        );
    }

    /**
     * Returns the model the repository handles
     *
     * @return string
     */
    protected function getModelClass(): string
    {
        return $this->model;
    }

    /**
     * Set the model class
     *
     * @param  string  $modelClass
     * @return void
     */
    protected function setModelClass(string $modelClass): void
    {
        $this->model = $modelClass;
    }

    /**
     * Create and return an un-saved model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    protected function model()
    {
        $modelClass = $this->getModelClass();
        return new $modelClass;
    }

    /**
     * Transform the given single model
     *
     * @param \Illuminate\Database\Eloquent\Model  $value
     * @return mixed
     */
    protected function transform($value)
    {
        if ($this->transformer) {
            return ($this->transformer)($value);
        }

        return $value;
    }

    /**
     * Transform the result of a query which is either
     * a single model, a collection of models or null
     *
     * @param  \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|array|null  $result
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|mixed
     */
    protected function applyTransform($result)
    {
        if (is_null($result)) {
            return null;
        }

        if (is_array($result) || $result instanceof Collection) {
            return collect($result)->map(function ($value) {
                return $this->transform($value);
            });
        }

        return $this->transform($result);
    }
}
