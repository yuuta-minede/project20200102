<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User; //追加
use App\Match_table;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');  //認証済みであれば表示、認証していなければ/loginにリダイレクト
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // return view('home');

        $users = User::all(); //追加

        return view('home', compact('users')); //追加

    }
    public function list()
    {
        $match = Match_table::all();
        //ddd($match);
        //$user = User::all();
        return view('list', ['user' => $user]);
        //return view('taskedit', ['task' => $task]);
    }
}
