<?php
namespace App\Traits;

trait ParamsTrait{

    public function extractQueryParams():array{
        return [
            'per_page' => request('perPage',10),
            'sort'=>[
                'order' => request('sort_order','desc'),
                'by' => request('order_by','')
            ],
            'filter' => request('filter',''),
            'search' => request('search','')
        ];
    }
}