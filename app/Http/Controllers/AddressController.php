<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\District;
use App\Wards;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getProvinces()
    {
        $provinces = Province::all();

        return response()->json(['provinces' => $provinces], 200);
    }

    public function getDistricts($provinceId)
    {
        $province = Province::find($provinceId);

        if (!$province) {
            return response()->json(['message' => 'Tỉnh không tồn tại'], 404);
        }

        $districts = $province->districts;

        return response()->json(['districts' => $districts], 200);
    }

    public function getWards($districtId)
    {
        $district = District::find($districtId);

        if (!$district) {
            return response()->json(['message' => 'Quận/Huyện không tồn tại'], 404);
        }

        $wards = $district->wards;

        return response()->json(['wards' => $wards], 200);
    }
}
