<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use App\Models\User;
use Exception;

class UserController extends Controller
{
    use ApiResponser;

    /**
     * Create an instance of user
     * @return Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'fullname' => 'required|max:180',
            'email' => 'required|email|unique:users,email',
            'access_type' => 'required',
            'photography_filename' => 'max:300'
        ];
        $this->validate($request, $rules);

        $fields = $request->all();
        $fields['password'] = Hash::make($request->get('email') . env('APP_KEY'));

        $user = User::create($fields);

        return $this->successResponse($user, Response::HTTP_CREATED);
    }

    /**
     * Verify if user exists
     * @return Illuminate\Http\Response
     */
    public function exists(Request $request)
    {
        $user = User::select()
            ->where('email', $request->query('email'))
            ->orderBy('id', 'DESC')
            ->first();
        if ($user) {
            return $this->successResponse($user);
        } else {
            return $this->errorResponse('No existe un usuario con este email', 404);
        }
    }

}
