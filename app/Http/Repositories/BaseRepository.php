<?php
namespace App\Http\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository{
    public function __construct(protected Model $model)
    {
    }
}