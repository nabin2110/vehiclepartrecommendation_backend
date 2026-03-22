<?php
namespace App\Http\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository{
    public function __construct(protected Model $model)
    {
    }

    public function findById(string $id){
        return $this->model->find($id);
    }

    public function getAllRecords(){
        return $this->model->all();
    }
}