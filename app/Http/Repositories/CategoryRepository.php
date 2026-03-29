<?php

namespace App\Http\Repositories;

use App\Helpers\CustomHelpers;
use App\Models\Category;

class CategoryRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Category());
    }

    public function store(array $data)
    {

        if (request()->hasFile('image_file')) {
            $data['image_url'] = CustomHelpers::saveFile(request(), $this->model->image_path, null);
        }

        return $this->model->create($data);
    }


    public function getCategories()
    {
        return $this->getRecordsByPagination(
            relations: [],
            selectable: ['name', 'image_url', 'created_by', 'created_at'],
            searchable:['name']
        );
    }
}
