<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;

class HotelControllerNoResource extends Controller
{
    public function getAll()
    {
        $hotel = Hotel::all();
        if(count($hotel)-1 < 0){
            return response()->json([
                'status' => 200,
                'message' => 'Data masih kosong'
            ], 200);
        }else{
            return $hotel;
        }
    }

    public function deleteById($id)
    {
        $userLogin = auth()->user();
        if($userLogin['role'] !== 'admin' && $userLogin['role'] !== 'superadmin'){
            return response()->json([
                'status' => 404,
                'message' => "Anda bukan admin",
                'dataUser' => $userLogin
            ], 404);
        }
        $hotel = Hotel::where('id', $id)->first();
        if($hotel['pemilik'] !== $userLogin['email']){
            return response()->json([
                'status' => 404,
                'message' => 'Anda tidak punya akses'
            ], 404);
        }
        
        if($hotel){
            $hotel->delete();
            return response()->json([
                'status' => 200, 
                'message'=> 'id ' . $id . ' berhasil di hapus',
                'data' => $hotel
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
        $findHotel = Hotel::find($id);
        if($findHotel === null){
            return response()->json([
                'status' => 404,
                'message' => 'id ' . $id . ' tidak ditemukan'
            ], 404);
        }
        return $findHotel;
    }

    public function updateById(Request $request, $id)
    {
        $userLogin = auth()->user();
        if($userLogin['role'] !== 'admin'){
            return response()->json([
                'status' => 404,
                'message' => "Anda bukan admin",
                'dataUser' => $userLogin
            ], 404);
        }
        $hotel = Hotel::find($id);
        if($hotel['pemilik'] !== $userLogin['email']){
            return response()->json([
                'status' => 404,
                'message' => 'Anda tidak punya akses'
            ], 404);
        }

        if($hotel === null){
            return response()->json([
                'status' => 404,
                'message' => 'id ' . $id . ' tidak ditemukan'
            ], 404);
        }

        $hotel->hotel = $request->input('hotel') === null ? $hotel->hotel : $request->input('hotel');
        $hotel->title = $request->input('title') === null ? $hotel->title : $request->input('title');
        $hotel->alamat = $request->input('alamat') === null ? $hotel->alamat : $request->input('alamat') ;
        $hotel->harga = $request->input('harga') === null ? $hotel->harga : $request->input('harga');
        $hotel->deskripsi = $request->input('deskripsi') === null ? $hotel->deskripsi : $request->input('deskripsi');
        $hotel->fasilitas = $request->input('fasilitas') === null ? $hotel->fasilitas : $request->input('fasilitas');
        $hotel->save();

        return response()->json([
            'status' => 201, 
            'data' => $hotel,
            'message' => "Success update data"
        ], 201);
    }

    public function postHotel(Request $request)
    {
        $userLogin = auth()->user();
        if($userLogin['role'] !== 'admin' && $userLogin['role'] !== 'superadmin'){
            return response()->json([
                'status' => 404,
                'message' => "Anda bukan admin ataupun superadmin",
                'dataUser' => $userLogin
            ], 404);
        }
        $hotel = new Hotel();
        $hotel->hotel = $request->input('hotel');
        $hotel->title = $request->input('title');
        $hotel->type = $request->input('type');
        $hotel->alamat = $request->input('alamat');
        $hotel->harga = $request->input('harga');
        $hotel->deskripsi = $request->input('deskripsi') ? $request->input('deskripsi') : "-";
        $hotel->fasilitas = $request->input('fasilitas') ? $request->input('fasilitas') : "-";
        $hotel->status = $request->input('status') ? $request->input('status'): "Tersedia";
        $hotel->pemilik = $userLogin['email'];
        if($request->input('hotel') === null || $request->input('title') === null || $request->input('alamat') === null || $request->input('harga') === null){
            return response()->json([
                'status' => 400, 
                'message' => "Failed create data hotel"
            ], 400);
        }
        $hotel->save();
        return response()->json([
            'status' => 201, 
            'data' => $hotel
        ], 201);
    }
}
