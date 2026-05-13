<?php

namespace App\Services;

use App\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    protected BaseRepositoryInterface $repository;

    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function find(int $id): ?Model
    {
        return $this->repository->find($id);
    }

    public function findWhere(array $conditions): Collection
    {
        return $this->repository->findWhere($conditions);
    }

    public function findWhereFirst(array $conditions): ?Model
    {
        return $this->repository->findWhereFirst($conditions);
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): ?Model
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function restore(int $id): ?Model
    {
        return $this->repository->restore($id);
    }

    public function forceDelete(int $id): bool
    {
        return $this->repository->forceDelete($id);
    }
}
