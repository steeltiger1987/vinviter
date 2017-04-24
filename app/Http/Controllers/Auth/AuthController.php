<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Country;
use App\Region;
use App\City;
use Validator;
use Input;
use App\Mailers\AppMailer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use File;
use DB;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/upcoming';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['logout', 'delete']]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data,[
            'name'     => 'required|max:255',
            'username' => 'required|regex:/^[a-zA-Z0-9]+$/|max:30|unique:users,username',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'country'  => 'required|exists:countries,id',
            'region'   => 'required|exists:regions,id,country_id,'.Input::get('country'),
            'city'     => 'required|exists:cities,id,region_id,'.Input::get('region'),
            'timezone' => 'required|timezone',
            'gender'   => 'required|in:male,female'
            ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'username'   => strtolower($data['username']),
            'password'   => bcrypt($data['password']),
            'country_id' =>  $data['country'],
            'region_id'  =>  $data['region'],
            'city_id'    =>  $data['city'],
            'timezone'   => $data['timezone'],
            'gender'     => $data['gender']
            ]);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm(Request $request)
    {
        if (property_exists($this, 'registerView')) {
            return view($this->registerView);
        }
        $timezones = DB::table('timezones')->get();
        $countries = Country::all();
        $regions = $cities = [];
        if($request->old('country') && is_numeric($request->old('country'))){
            $regions = Region::select('id', 'name')->where('country_id', $request->old('country'))->orderBy('name', 'ASC')->get();
        }
        if($request->old('region') && is_numeric($request->old('region'))){
            $cities = City::select('id', 'name')->where('region_id', $request->old('region'))->orderBy('name', 'ASC')->get();
        }

        return view('auth.register', compact('countries', 'regions', 'cities', 'timezones'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Mailers\AppMailer  $mailer
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request, AppMailer $mailer)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
                );
        }
        $user = $this->create($request->all());
        $mailer->sendEmailConfirmationTo($user);

        $userPath = base_path().'/public/uploads/users/'.$user->id;
        $avatarsPath = $userPath.'/avatars';
        $backgroundsPath = $userPath.'/backgrounds';

        File::makeDirectory($userPath);
        File::makeDirectory($avatarsPath);
        File::makeDirectory($backgroundsPath);

        $request->session()->flash('auth_verify_email', true);
        return redirect()->back();
    }

    /**
     * Confirm a user's email address.
     *
     * @param  string $token
     * @return mixed
     */
    public function confirmEmail(Request $request, $token)
    {
        User::withoutGlobalScope('verified')->whereToken($token)->firstOrFail()->confirmEmail();
        $request->session()->flash('auth_verified', true);
        return redirect()->route('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $lockedOut = $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->getCredentials($request);
        $credentials['verified'] = true;

        if (Auth::guard($this->getGuard())->attempt($credentials, $request->has('remember'))) {
            return $this->handleUserWasAuthenticated($request, $throttles);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles && ! $lockedOut) {
            $this->incrementLoginAttempts($request);
        }

        return $this->sendFailedLoginResponse($request);
    }
}
