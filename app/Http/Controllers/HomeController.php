<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Event;
use App\Page;
use App\User;
use Auth;
use App\Mailers\AppMailer;
use File;
use Image;
use DB;
use Carbon\Carbon;
use App\Report;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['postReportPage']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Show the initial create page.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){
        if($request->user()){
            return view('app.create');
        }
        return view('app.createGuest');
    }

    public function appSearch(Request $request){
        if($request->term){
            $userPrivateEventAccess = [];
            $accessibleEvents = 'public';

            if(Auth::check()){
                $userc = new \App\Http\Controllers\UserController;
                $userPrivateEventAccess = $userc->privateEventsWithAccess(Auth::user());
                $accessibleEvents       = 'accessibleToUser';
            }

            $search = preg_replace('/[+\-><\(\)~*\"@]+/', ' ', $request->term);
            $term = array($search.'*');
            $events = Event::whereRaw("MATCH(title) AGAINST(? IN BOOLEAN MODE)", $term)->$accessibleEvents($userPrivateEventAccess)->take(5)->get();
            $pages = Page::whereRaw("MATCH(name) AGAINST(? IN BOOLEAN MODE)", $term)->take(5)->get();
            $profiles = User::whereRaw("MATCH(name) AGAINST(? IN BOOLEAN MODE)", $term)->take(5)->get();
            return response()->view('layouts.main_search_results', compact('events', 'pages', 'profiles'));
        }
        return response('error', 404);
    }

    /* Static pages */
    public function getPrivacyPage(){
        return view('static.privacy');
    }
    public function getTermsPage(){
        return view('static.terms');
    }
    public function getContactPage(){
        $name = $email = '';
        if(Auth::check())
        {
            $name  = Auth::user()->name;
            $email = Auth::user()->email;
        }
        return view('static.contact', compact('name', 'email'));
    }
    public function postContactPage(Request $request, AppMailer $mailer)
    {
        $this->validate($request, [
            'name'    => 'required|max:255',
            'email'   => 'required|email',
            'message' => 'required|max:2000'
            ]);

        $name     = $request->name;
        $email    = $request->email;
        $message  = $request->message;

        if(Auth::check())
        {
            $name     = Auth::user()->name;
            $email    = Auth::user()->email;
        }

        $data = [
        'name'    => $name,
        'email'   => $email,
        'message' => $message
        ];

        $mailer->sendContactFormMessage($data);

        $request->session()->flash('success', 'Your message was sent successfully.');
        return back();
    }

    public function serveImages(Request $request, $size, $entity, $id, $group, $filename)
    {
        if($entity == 'default' 
            && ($id == 'user' || $id == 'page' || $id == 'event') 
            && $group == 'all' 
            && ($filename == 'avatar.png' || $filename == 'male_avatar.png' || $filename == 'female_avatar.png'))
        {
            $imageFolder = base_path().'/public/uploads/' . $entity . '/' . $id . '/' . $group . '/';
            $filePath = $imageFolder.'/'.$size.'_'.$filename;
        }
        else{
            $imageFolder = base_path().'/public/uploads/' . $entity . '/' . $id . '/' . $group . '/';
            $filePath = $imageFolder.'/'.$size.'_'.$filename;
        }

        if(File::isFile($filePath)){
            $response = response(File::get($filePath), 200);
            $response->header('Content-Type', File::type($filePath));
            return $response;
        }

        $sizeArray = config('common.images.'.$size);

        if($sizeArray && File::isFile($imageFolder.'/'.$filename)){
            $img = Image::make($imageFolder.'/'.$filename)->fit($sizeArray['width'], $sizeArray['height'])->save($filePath);
            $response = response(File::get($filePath), 200);
            $response->header('Content-Type', File::type($filePath));
            return $response;
        }

        abort(404);
    }



    public function postFeedback(Request $request)
    {
        if(Auth::check()){
            $rules = ['comments' => 'required|max:5000'];
        }
        else{
            $rules = [
            'name'     => 'required|max:70',
            'email'    => 'required|email|max:254',
            'comments' => 'required|max:5000'
            ];
        }
        $this->validate($request, $rules);

        if(Auth::check()){
            $name = Auth::user()->name;
            $email = Auth::user()->email;
            $comments = $request->comments;
        }
        else{
            $name = $request->name;
            $email = $request->email;
            $comments = $request->comments;
        }

        $insert = DB::table('feedbacks')->insert([
            'name' => $name,
            'email' => $email,
            'comments' => $comments,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ]);

        if($insert){
            return response()->json(['success' => true]);
        }
        else{
            return response()->json(['success' => false]);
        }
    }


    public function postReportPage(Page $page, Request $request){
        $rules = [
        'comments' => 'required|max:5000'
        ];

        $this->validate($request, $rules);

        $report = new Report;
        $report->user_id = Auth::id();
        $report->comments = $request->comments;

        if($page->reports()->save($report)){
            return response()->json(['success' => true]);
        }
        else{
            return response()->json(['success' => false]);
        }
    }


    public function postReportUser(User $user, Request $request){
        $rules = [
        'comments' => 'required|max:5000'
        ];

        $this->validate($request, $rules);

        $report = new Report;
        $report->user_id = Auth::id();
        $report->comments = $request->comments;

        if($user->reports()->save($report)){
            return response()->json(['success' => true]);
        }
        else{
            return response()->json(['success' => false]);
        }
    }

    public function getLandingPage(){
        return view('app.landingPage');
    }
}
