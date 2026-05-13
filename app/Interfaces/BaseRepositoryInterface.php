<?php

namespace App\Interfaces;

interface BaseRepositoryInterface
{
    public function all();

    public function find(int $id);

    public function findWhere(array $conditions);

    public function findWhereFirst(array $conditions);

    public function paginate(int $perPage = 10);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function restore(int $id);

    public function forceDelete(int $id);
}
