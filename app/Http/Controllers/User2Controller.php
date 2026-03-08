<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // DB component
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use App\Services\User2Service;

class User2Controller extends Controller
{
    private $request;
    use ApiResponser;

    /**
     * The service to consume the User1 Microservice
     * @var User2Service
     */
    public $user2Service;

    /**
     * Create a new controller instance 
     *  @return void
     */

    public function __construct(User2Service $user2Service)
    {
        $this->user2Service = $user2Service;
    }


    public function index()
    {
        //
        return $this->successResponse($this->user2Service->obtainUsers2());
    }

    public function add(Request $request)
    {
        return $this->successResponse($this->user2Service->createUser2(
            $request->all(),
            Response::HTTP_CREATED
        ));
    }



    /**
     * Update an existing user
     * @return Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->successResponse($this->user2Service->editUser2(
            $request->all(),
            $id
        ));
    }


    /**
     * Remove an existing user
     * @return Illuminate\Http\Response
     */
    public function delete($id)
    {
        return $this->successResponse($this->user2Service->deleteUser2($id));
    }
}
