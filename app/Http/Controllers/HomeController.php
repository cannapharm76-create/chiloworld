<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Public website pages. Page markup lives in resources/views/front,
 * shared header / cookie notice in resources/views/front/layout.
 */
class HomeController extends Controller
{
    /** brand landing pages: url slug => view */
    public const BRANDS = ['superdope', 'tedsbudz', 'omakase', 'hitech', 'thebreadfactory', 'dojaexclusive', 'chrissmokes'];

    public function index()
    {
        return view('front.index');
    }

    public function products()
    {
        return view('front.products');
    }

    public function brands()
    {
        return view('front.brands');
    }

    public function brand(string $brand)
    {
        abort_unless(in_array($brand, self::BRANDS, true), 404);

        return view('front.'.$brand);
    }

    public function strain(string $brand, string $strain)
    {
        $view = 'front.strains.'.$brand.'.'.$strain;
        abort_unless(preg_match('/^[a-z0-9-]+$/', $brand.$strain) && view()->exists($view), 404);

        return view($view);
    }

    public function reviews()
    {
        return view('front.reviews');
    }

    public function shop()
    {
        return view('front.shop');
    }

    public function leaderboard()
    {
        return view('front.leaderboard');
    }

    public function about()
    {
        return view('front.about');
    }

    /** Chilo Exotics private strain index (from Chilo-Website/catalog.html) */
    public function strainIndex()
    {
        return view('front.strain-index');
    }

    public function terms()
    {
        return view('front.terms');
    }

    /* ---------------- account pages ---------------- */

    public function login(Request $request)
    {
        if (auth()->check()) {
            return redirect()->route('home');
        }

        if ($request->isMethod('post')) {
            $request->validate(['username' => 'required', 'password' => 'required'], ['username.required' => 'Please enter your username or email.']);

            return back()->with('status', 'Login is not connected yet.');
        }

        return view('front.login');
    }

    public function signup(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate(['email' => 'required|email']);

            return back()->with('status', 'Thanks! Account sign up will be available soon.');
        }

        return view('front.signup');
    }

    public function forgotPassword(Request $request)
    {
        if ($request->isMethod('post')) {
            return back()->with('status', 'If that account exists, a reset link is on its way.');
        }

        return view('front.forgot-password');
    }

    public function forgotUsername(Request $request)
    {
        if ($request->isMethod('post')) {
            return back()->with('status', 'If that email exists, your username is on its way.');
        }

        return view('front.forgot-username');
    }

    public function redeem(Request $request)
    {
        if ($request->isMethod('post')) {
            return redirect()->route('login')->with('status', 'Please sign in to claim your reward.');
        }

        return view('front.redeem');
    }
}
