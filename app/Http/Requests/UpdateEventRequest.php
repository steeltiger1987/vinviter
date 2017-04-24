<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Validation\Factory;
use Session;
use App\Page;
use Carbon\Carbon;
use Validator;
use App\Event;

class UpdateEventRequest extends Request
{

    public function __construct(Factory $factory){
        parent::__construct();
        $factory->extend('page_exists_with_type', function($attribute, $value, $parameters){
            return Page::where('id', $value)->wherePageType($parameters[0])->exists();
        },
        'The selected page is invalid.'
        );

        $factory->extend('equal_after', function($attribute, $value, $parameters){
            return strtotime($value) >= strtotime($parameters[0]);
        },
        'Starting time must be equal to or after the original starting time.'
        );

        $this->orderImages();
    }

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
        $event         = Event::withoutGlobalScope('published')->where('id', $this->route('event'))->firstOrFail();
        $timezone      = $event->timezone;
        if(\Request::has('timezone')){
            $validTimezone = Validator::make(['timezone' => Request::get('timezone')], ['timezone' => 'required|timezone']);
            if($validTimezone->passes()){
                $timezone = Request::get('timezone');
            }
        }

        $originalTime  = $event->starts_at->timezone($timezone);
        $time          = $originalTime->toDateTimeString();

        $startsAtRules = 'required|date_format:"Y-m-d H:i"|equal_after:'.$time;
        $endsAtRules   = 'required|date_format:"Y-m-d H:i"|after:starting_time';
        $timezoneRule  = 'required|timezone';
        
        if($event->isHistory){
            $startsAtRules  = '';
            $endsAtRules    = '';
            $timezoneRule   = '';
        }

        return [
        'title'         => 'required|max:255',
        'visibility'    => 'required|in:0,1',
        'type'          => 'required|exists:attributes,id,type,event.type',
        'country'       => 'required|exists:countries,id',
        'region'        => 'required|exists:regions,id,country_id,'.Request::input('country'),
        'city'          => 'required|exists:cities,id,region_id,'.Request::input('region'),
        'address'       => 'max:255',
        'zip_code'      => 'max:30',
        'hide_location' => 'in:on',
        'venue_page'    => 'page_exists_with_type:venue',
        'creator_page'  => 'page_exists_with_type:organization',
        'timezone'      => $timezoneRule,
        'starting_time' => $startsAtRules,
        'ending_time'   => $endsAtRules,
        'details'       => 'required|max:5000',

        'entrance'      => 'exists:attributes,id,type,event.entrance',
        'dress_code'    => 'exists:attributes,id,type,event.dress_code',
        'age_limit'     => 'exists:attributes,id,type,event.age_limit',
        'music'         => 'exists:attributes,id,type,event.music',
        'document'      => 'exists:attributes,id,type,event.document',

        'admins'        => 'array',
        'admins.*'      => 'required_with:admins|exists:users,id',
        'mainImage'     => 'string'
        ];
    }
    
    public function orderImages(){
        // order images in session
        if(Session::has('edit_event_images') && \Request::has('mainImage')){
            $eventImages = Session::get('edit_event_images');
            $firstImage = \Request::get('mainImage');
            $newArray = [];
            $newArray[0] = 'reserved';

            foreach($eventImages as $image){
                if($image[0] == $firstImage){
                    $newArray[0] = $image;
                }
                else{
                    $newArray[] = $image;
                }
            }
            if($newArray[0] == 'reserved'){
                unset($newArray[0]);
            }
            Session::put('edit_event_images', array_values($newArray));
        }
    }

}
