<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserDetailResource;

class UserController extends Controller
{
    // View list data user
    public function index()
    {
        //get data api to view
        // Using eager loading request data to database for efficiency data
        //in case calling data relation
        $data_user = User::with('partner')->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan List User',
            'data' => UserResource::collection($data_user)
        ]);
    }

    // Store data user to database
    public function store(Request $request)
    {
        // Data input validation
        $data = $request->validate([
            'bp_code' => 'required|string|max:25',
            'name' => 'required|string|max:25',
            'role' => 'required|string|max:25',
            'status' => 'required|string|max:25',
            'username' => 'required|string|max:25',
            'password' => 'required|string|max:25',
            'email' => 'required|unique|string|max:255'
        ]);

        // Create data
        $data_create = User::create([$data]);

        // Return value
        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menambahkan User \"'.$data_create->username."\"",
            'data' => UserResource::collection($data_create)
        ]);
    }

    //Show edit form user
    public function edit($user)
    {
        //variable $user to store the id of data
        $data_edit = User::findOrFail($user);
        return new UserDetailResource($data_edit);
    }

    // Update data to database
    public function update(Request $request, User $user)
    {
        //
        $data_edit = User::findOrFail($user);

        // Validate the request data
        $data = $request->validate([
            'bp_code' => 'required|string|max:25',
            'name' => 'required|string|max:25',
            'role' => 'required|string|max:25',
            'status' => 'required|string|max:25',
            'username' => 'required|string|max:25',
            'password' => 'required|string|max:25',
            'email' => 'required|unique:email|string|max:255'
            // Add other fields as necessary
        ]);

        // Update the user with the validated data
        $data_update = User::update([$data]);

        // Return value
        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menambahkan User \"'.$data_create->username."\"",
            'data' => new UserResource($data_update)
        ]);

    }
}
