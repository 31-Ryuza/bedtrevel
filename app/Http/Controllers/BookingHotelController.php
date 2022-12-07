<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Hotel;

class BookingHotelController extends Controller
{
    public function getAll()
    {
        $userLogin = auth()->user();
        if($userLogin['role'] !== 'superadmin' && $userLogin['role'] !== 'admin'){
            return response()->json([
                'status' => 404,
                'message' => "Anda bukan superadmin maupun admin",
                'dataUser' => $userLogin
            ], 404);
        }
        $booking = Booking::all();
        if(count($booking)-1 < 0){
            return response()->json([
                'status' => 200,
                'message' => 'Data masih kosong'
            ], 200);
        }else{
            return $booking;
        }
    }

    public function deleteById($id)
    {
        $userLogin = auth()->user();
        if($userLogin['role'] !== 'superadmin' && $userLogin['role'] !== 'admin'){
            return response()->json([
                'status' => 404,
                'message' => "Anda bukan superadmin maupun admin",
                'dataUser' => $userLogin
            ], 404);
        }
        $booking = Booking::where('id', $id)->first();
        if($booking){
            $booking->delete();
            return response()->json([
                'status' => 200, 
                'message'=> 'id ' . $id . ' berhasil di hapus',
                'data' => $booking
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
        $findHotel = Booking::find($id);
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
        $booking = Booking::find($id);
        if($booking === null){
            return response()->json([
                'status' => 404,
                'message' => 'id ' . $id . ' tidak ditemukan'
            ], 404);
        }

        function manipulasiTanggal($tgl,$jumlah=1,$format='days'){
            $currentDate = $tgl;
            return date('Y-m-d', strtotime($jumlah.' '.$format, strtotime($currentDate)));
        }

        $hotel = Hotel::where('id', $request['id_hotel'])->first();
        $booking->hotel = $request->input('hotel') === null ? $booking->hotel : $request->input('hotel');
        $booking->nama = $request->input('nama') === null ? $booking->nama : $request->input('nama');
        $booking->email = $request->input('email') === null ? $booking->email : $request->input('email') ;
        $booking->nomor_hp = $request->input('nomor_hp') === null ? $booking->nomor_hp : $request->input('nomor_hp');
        $booking->lama_tinggal = $request->input('lama_tinggal') === null ? $booking->lama_tinggal : $request->input('lama_tinggal');
        $booking->total_harga = $hotel->harga * $request->input('lama_tinggal');
        $booking->tgl_checkIn = $request->input('tgl_checkIn') === null ? $booking->tgl_checkIn : $request->input('tgl_checkIn');
        $booking->tgl_checkOut = manipulasiTanggal($booking->tgl_checkIn, $booking->lama_tinggal);
        $booking->code_booking = "BOK-" . $booking->id . $hotel->id . date('m'). date('s') . "-ING";
        $booking->save();

        return response()->json([
            'status' => 201, 
            'data' => $booking,
            'message' => "Success update data"
        ], 201);
    }

    public function postBooking(Request $request)
    {
        function manipulasiTanggal($tgl,$jumlah=1,$format='days'){
            $currentDate = $tgl;
            return date('Y-m-d', strtotime($jumlah.' '.$format, strtotime($currentDate)));
        }
        $hotel = Hotel::where('id', $request['id_hotel'])->first();
        $booking = new Booking();
        $booking->hotel = $request->input('hotel');
        $booking->nama = $request->input('nama');
        $booking->email = $request->input('email');
        $booking->nomor_hp = $request->input('nomor_hp');
        $booking->status = "Belum Lunas";
        $booking->lama_tinggal = $request->input('lama_tinggal');
        $booking->total_harga = $hotel->harga * $request->input('lama_tinggal');
        $booking->tgl_checkIn = $request->input('tgl_checkIn');
        $booking->tgl_checkOut = manipulasiTanggal($booking->tgl_checkIn, $booking->lama_tinggal);
        $booking->code_booking = "BOK-" . $booking->id . $hotel->id . date('m'). date('s') . "-ING";
        if($request->input('hotel') === null || $request->input('nama') === null || $request->input('email') === null || $request->input('nomor_hp') === null || $request->input('lama_tinggal') === null || $request->input('tgl_checkIn') === null){
            return response()->json([
                'status' => 400, 
                'message' => "Failed create data hotel"
            ], 400);
        }

        $booking->save();
        return response()->json([
            'status' => 201, 
            'data' => $booking
        ], 201);
    }

    public function updateStatus(Request $request)
    {
        $userLogin = auth()->user();
        if($userLogin['role'] !== 'superadmin' && $userLogin['role'] !== 'admin'){
            return response()->json([
                'status' => 404,
                'message' => "Anda bukan superadmin maupun admin",
                'dataUser' => $userLogin
            ], 404);
        }
        $booking = Booking::where('code_booking', $request['code_booking'])->first();
        if($booking === null){
            return response()->json([
                'status' => 404,
                'message' => 'id ' . $id . ' tidak ditemukan'
            ], 404);
        }
        $booking->status = $request->input('status');
        $booking->save();

        return response()->json([
            'status' => 201, 
            'data' => $booking,
            'message' => "Success update data"
        ], 201);
    }
}
