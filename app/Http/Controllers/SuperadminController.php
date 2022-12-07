<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Validator;
use Illuminate\Support\Facades\Hash;

class SuperadminController extends Controller
{
    public function userCreate(Request $request)
    {
        $userLogin = auth()->user();
        if($userLogin['role'] !== 'superadmin'){
            return response()->json([
                'status' => 404,
                'message' => "Anda bukan superadmin"
            ], 404);
        }
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
         ]);

        return response()
            ->json(['data' => $user]);
    }

    public function getAll()
    {
        $userLogin = auth()->user();
        if($userLogin['role'] !== 'superadmin'){
            return response()->json([
                'status' => 404,
                'message' => "Anda bukan superadmin"
            ], 404);
        }
        $User = User::all();
        if(count($User)-1 < 0){
            return response()->json([
                'status' => 200,
                'message' => 'Data masih kosong'
            ], 200);
        }else{
            return $User;
        }
    }

    public function deleteById($id)
    {
        $userLogin = auth()->user();
        if($userLogin['role'] !== 'superadmin'){
            return response()->json([
                'status' => 404,
                'message' => "Anda bukan superadmin"
            ], 404);
        }
        $user = User::where('id', $id)->first();
        if($user){
            $user->delete();
            return response()->json([
                'status' => 200, 
                'message'=> 'id ' . $id . ' berhasil di hapus',
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'id' . $id . ' tidak ditemukan'
            ], 404);
        }
    }

    public function getById($id)
    {
        $userLogin = auth()->user();
        if($userLogin['role'] !== 'superadmin'){
            return response()->json([
                'status' => 404,
                'message' => "Anda bukan superadmin"
            ], 404);
        }
        $findUser = User::find($id);
        if($findUser === null){
            return response()->json([
                'status' => 404,
                'message' => 'id ' . $id . ' tidak ditemukan'
            ], 404);
        }
        return $findUser;
    }
}
