<?php

namespace App\Http\Controllers;

use App\Services\DonateService;
use Illuminate\Http\Request;

class DonateController extends Controller
{
    public function index()
    {
        $data = config('donate');
        return view('profile.donate.index', compact('data'));
    }

    public function show($method)
    {
        $config = config("donate.{$method}");

        if (!$config || !$config['enabled']) {
            return redirect()->back()->withErrors('Payment method not found or disabled.');
        }

        if (!view()->exists("profile.donate.{$method}")) {
            return redirect()->back()->withErrors(['error' => 'View file for the payment method is missing.']);
        }

        return view("profile.donate.{$method}", ['data' => $config]);
    }

    public function process($method, Request $request, DonateService $donateService)
    {
        $config = config("donate.{$method}");
        if (!$config || !$config['enabled']) {
            return redirect()->back()->withErrors('Payment method not found or disabled.');
        }

        $methodName = "process" . ucfirst($method);
        if (!method_exists($donateService, $methodName)) {
            return redirect()->back()->withErrors('Invalid payment method.');
        }

        return $donateService->$methodName($request);
    }

    public function callback($method, Request $request, DonateService $donateService)
    {
        $config = config("donate.{$method}");
        if (!$config || !$config['enabled']) {
            return redirect()->back()->withErrors('Payment method not found or disabled.');
        }

        $methodName = "callback" . ucfirst($method);
        if (!method_exists($donateService, $methodName)) {
            return redirect()->back()->withErrors('Invalid payment method.');
        }

        return $donateService->$methodName($request);
    }

    public function webhook($method, Request $request, DonateService $donateService)
    {
        $config = config("donate.{$method}");
        if (!$config || !$config['enabled']) {
            return response('Payment method not found or disabled.', 403);
        }

        $methodName = "webhook" . ucfirst($method);
        if (!method_exists($donateService, $methodName)) {
            return response('Invalid payment method.', 403);
        }

        return $donateService->$methodName($request);
    }
}
