<?php

namespace App\Repositories;

use App\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findWhere(array $conditions): Collection
    {
        return $this->model->where($conditions)->get();
    }

    public function findWhereFirst(array $conditions): ?Model
    {
        return $this->model->where($conditions)->first();
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Model
    {
        $record = $this->model->find($id);
        if ($record) {
            $record->update($data);
            return $record->fresh();
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $record = $this->model->find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }

    public function restore(int $id): ?Model
    {
        $record = $this->model->onlyTrashed()->find($id);
        if ($record) {
            $record->restore();
            return $record->fresh();
        }
        return null;
    }

    public function forceDelete(int $id): bool
    {
        $record = $this->model->withTrashed()->find($id);
        if ($record) {
            return $record->forceDelete();
        }
        return false;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function newQuery()
    {
        return $this->model->newQuery();
    }
}
