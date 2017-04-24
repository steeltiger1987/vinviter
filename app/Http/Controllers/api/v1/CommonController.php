<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Region;
use App\City;

class CommonController extends Controller
{
    public function getCountryRegions($id){
        if(isset($id) && is_numeric($id)){
            $regions = Region::select('id', 'name')->where('country_id', $id)->orderBy('name', 'ASC')->get();
            if($regions->count() > 0){
                return $regions;
            }
        }
        return response('Error', 404);
    }
    public function getRegionCities($id){
        if(isset($id) && is_numeric($id)){
            $cities = City::select('id', 'name')->where('region_id', $id)->orderBy('name', 'ASC')->get();
            if($cities->count() > 0){
                return $cities;
            }
        }
        return response('Error', 404);
    }
}
