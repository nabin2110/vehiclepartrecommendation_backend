<?php

namespace App\Http\Controllers;

use App\Http\Repositories\CategoryRepository;
use App\Http\Requests\CategoryRequest;

class CategoryController extends ResponseController
{

    public function __construct(
        protected CategoryRepository $categoryRepository
    ){}

    public function store(CategoryRequest $request){
        try{
            $response = $this->categoryRepository->store($request->validated());
            return $this->success($response,'Category stored successfully');
        }
        catch(\Exception $e){
            webLog('Unable to store category at this moment. | '.__CLASS__.' | ' .__FUNCTION__.' | '. $e->getMessage());
            $this->error('Unable to store category at this moment.',400);
        }
    }
    public function index(){
        try{
            $response = $this->categoryRepository->getCategories();
            return $this->success($response,'Category lists retrieved successfully');
        }
        catch(\Exception $e){
            webLog('Unable to retrieve category at this moment. | '.__CLASS__.' | ' .__FUNCTION__.' | '. $e->getMessage());
            $this->error('Unable to retrieve category at this moment.',400);
        }
    }
}
