<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Auth;

class StorePageRequest extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        'name'             => 'required|max:255',
        'slug'             => 'required|regex:/^[a-zA-Z0-9]+$/|max:255|unique:pages,slug',
        'address'          => 'max:255',
        'zip_code'         => 'max:30',
        'story'            => 'required|max:5000',
        'type'             => 'required|exists:attributes,id,type,page.type',
        'status'           => "max:500",
        'year_founded'     => 'exists:attributes,id,type,page.year',
        'activity_period'  => 'exists:attributes,id,type,page.activity_period',
        'season'           => 'exists:attributes,id,type,page.season',
        'country'          => 'required|exists:countries,id',
        'region'           => 'required|exists:regions,id,country_id,'.Request::input('country'),
        'city'             => 'required|exists:cities,id,region_id,'.Request::input('region'),
        'key_people'       => 'array',
        'key_people.*'     => 'required_with:key_people|exists:users,id',
        'admins'           => 'array',
        'admins.*'         => 'required_with:admins|exists:users,id|not_in:'.Auth::id(),
        ];
    }

    public function messages(){
        return [
        'key_people.*.exists' => 'Selected key person is invalid.'
        ];
    }
}
