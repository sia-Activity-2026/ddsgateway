<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // DB component
use App\Traits\ApiResponser;
use Illuminate\Http\Response;

class UserController1 extends Controller
{
    use ApiResponser;
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        // $users = User::all(); // Eloquent style
        $users = User::all(); // Eloquent style

        return response()->json($users, 200);
    }


    public function getUsers()
    {
        $users = DB::connection('mysql')
            ->select("Select * from users2");  // use traditional SQL

        return $this->successResponse($users);
    }


    public function add(Request $request)
    {
        $rule = [
            // 'username' => 'required | string | unique:users2,username|max:20', // old
            'username' => 'required | max:20',
            'password' => 'required | max:20',
            'gender' => 'required | in:Male,Female', // new
        ];

        $this->validate($request, $rule);

        $user = User::create($request->all());
        return $this->successResponse($user, Response::HTTP_CREATED);
    }




    // or like this 
    public function add_2ndVersion(Request $request)
    {
        $rule = [
            'username' => 'required | string | unique:users2,username|max:20',
            'password' => 'required | string | min:6 | max:20',
        ];

        $this->validate($request, $rule);

        $user = User::create($request->all());

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }


    public function show($id)
    {
        $user = User::findOrFail($id);
        return $this->successResponse($user);

        // Old
        // $user = User::where('id', $id)->first();

        // if (!$user) {
        //     return response()->json([
        //         'message' => 'User not found'
        //     ], 404);
        // }

        // return response()->json($user, 200);
    }


    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return $this->errorResponse('User ID Does Not Exists', Response::HTTP_NOT_FOUND);

        // Old code
        // $user = User::where('id', $id)->first();

        // if ($user) {
        //     $user->delete();
        //     return response()->json([
        //         'message' => 'User deleted successfully'
        //     ], 200);
        // }

        // return response()->json([
        //     'message' => 'User not found'
        // ], 404);
    }


    public function update($id, Request $request)
    {
        $rules = [
            'username' => 'max:20',
            'password' => 'max:20',
            'gender' => 'in:Male,Female',
        ];

        $this->validate($request, $rules);
        $user = User::findOrFail($id);

        $user->fill($request->all());

        if ($user->isClean()) {
            return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->save();
        return $this->successResponse($user);


        // Old!
        // $user = User::where('id', $id)->first();

        // if (!$user) {
        //     return response()->json([
        //         'message' => 'User not found'
        //     ], 404);
        // }

        // $rule = [
        //     'username' => 'string | unique:users,username,' . $id . '|max:20',
        //     'password' => 'string | min:6 | max:20',
        // ];

        // $this->validate($request, $rule);

        // $user->update($request->all());
        // return response()->json($user, 200);
    }
    











    //=================================================================================================================================================================================
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $User
     * @return \Illuminate\Http\Response
     */
    public function oldshow(User $User)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $User
     * @return \Illuminate\Http\Response
     */
    public function edit(User $User)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $User
     * @return \Illuminate\Http\Response
     */
    public function oldupdate(Request $request, User $User)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $User
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $User)
    {
        //
    }
}
