<?php

namespace App\Http\Repositories;

use App\Traits\ParamsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class BaseRepository
{
    use ParamsTrait;

    public function __construct(protected Model $model) {}

    public function findById(string $id): mixed
    {
        return $this->model->find($id);
    }

    public function getAllRecords(): mixed
    {
        return $this->model->all();
    }

    public function getRecordsByPagination(
        array $relations   = [],
        ?array $selectable = [],
        ?array $searchable = []
    ): LengthAwarePaginator {

        $params = $this->extractQueryParams();

        $query = $this->model->newQuery();
        if (!empty($relations)) {
            $query->with($relations);
        }

        $search = trim($params['search']);

        if ($search !== '' && !empty($searchable)) {
            Log::info($search. '|| '. json_encode($searchable));
            $query->where(function ($q) use ($search, $searchable) {
                foreach ($searchable as $column) {
                    $q->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
        }

        $sortOrder = $params['sort']['order'];
        $sortBy  = $params['sort']['by'];

        $query->orderBy($sortBy, $sortOrder);

        if (!empty($selectable)) {
            $query->select($selectable);
        } else {
            $query->select(['*']);
        }

        return $query->paginate($params['per_page']);
    }
}
