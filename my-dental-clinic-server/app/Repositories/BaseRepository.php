<?php

namespace App\Repositories;

abstract class BaseRepository
{
    protected $model;
    public function __construct()
    {
        $this->model = $this->setModel();
    }
    abstract public function  getModel();
    public function setModel()
    {
        return app()->make($this->getModel());
    }
    public function create($data)
    {
        return $this->model->create($data);
    }
    public function getAll()
    {
        return $this->model->all();
    }
    public function getById($id)
    {
        return $this->model->where('id', $id)->first();
    }
    public function update($id, array $data = [])
    {
        return $this->model->find($id)->update($data);
    }

    public function destroy($id)
    {
        return $this->model->destroy($id);
    }
    public function paging( int $pageSize, ?array $conditions)
{
    $query = $this->model; 
    if (!empty($conditions)) {
        $query = $query->where(function ($q) use ($conditions) {
            foreach ($conditions as $item) {
                $q->Where($item[0], $item[1], $item[2]); 
            }
        });
    }
    return $query->paginate($pageSize);
}

    
}
