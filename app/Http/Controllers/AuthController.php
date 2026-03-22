<?php

namespace App\Http\Controllers;

use App\Http\Repositories\AuthRepository;
use App\Http\Requests\Auth\InitiateLoginRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;

class AuthController extends ResponseController
{
    public function __construct(protected AuthRepository $authRepository)
    {
    }
    public function initiateLogin(InitiateLoginRequest $request){
        try{
            $response = $this->authRepository->initiateLogin($request->validated());
            return $this->success($response,'Login initiated successfully');
        }
        catch(\Exception $e){
            webLog('Unable to initiate login at this moment. | '.__CLASS__.' | ' .__FUNCTION__.' | '.$e->getMessage());
            $this->error('Unable to initiate login at this moment.');
        }
    }
    public function login(LoginRequest $request){
        try{
            return $this->authRepository->login($request->validated());
        }
        catch(\Exception $e){
            webLog('Unable to login at this moment. | '.__CLASS__.' | ' .__FUNCTION__.' | '.$e->getMessage());
            $this->error('Unable to login at this moment.');
        }
    }
    public function refresh(){
        try{
            return $this->authRepository->refresh();
        }
        catch(\Exception $e){
            webLog('Unable to refresh token at this moment. | '.__CLASS__.' | ' .__FUNCTION__.' | '.$e->getMessage());
            $this->error('Unable to refresh token at this moment.');
        }
    }
    public function logout(){
        try{
            return $this->authRepository->logout();
        }
        catch(\Exception $e){
            webLog('Unable to logout at this moment. | '.__CLASS__.' | ' .__FUNCTION__.' | '.$e->getMessage());
            $this->error('Unable to logout at this moment.');
        }
    }

    public function me(){
        return $this->success(auth()->user(),'User record retrieved successfully');
    }
}
