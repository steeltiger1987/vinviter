<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Page;

class PageController extends Controller
{
    public function search($name, Request $request){
        $allowed_parameters = ['venue' => 1, 'organization' => 2];

        /* search based on page name and type */
        if($request->has('type') && isset($allowed_parameters[$request->input('type')])){
            $parent_id = $allowed_parameters[$request->input('type')];
            $pages = Page::whereHas('attributes', function($query) use($parent_id)
            {
                $query->where('type', '=', 'page.type')->where('parent_id', '=', $parent_id);
            })->like('name', $name)->select('id', 'name')->orderBy('name')->take(5)->get();

            return $pages;
        }

        /* search pages without any additional parameters */
        $pages = Page::whereHas('attributes', function($query)
        {
            $query->where('type', '=', 'page.type');
        })->like('name', $name)->select('id', 'name')->orderBy('name')->take(5)->get();

        return $pages;
    }
}
