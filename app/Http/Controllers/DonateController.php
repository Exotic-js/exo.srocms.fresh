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
            redirect()->back()->withErrors('Payment method not found or disabled.')->send();
            exit;
        }

        $viewPath = "profile.donate.{$method}";
        if (!view()->exists($viewPath)) {
            return redirect()->back()->withErrors(['error' => 'View file for the payment method is missing.']);
        }

        return view($viewPath, ['data' => $config]);
    }

    public function process($method, Request $request, DonateService $donateService)
    {
        $methodName = "process" . ucfirst($method);
        if (!method_exists($donateService, $methodName)) {
            return redirect()->back()->withErrors('Invalid payment method.');
        }

        return $donateService->$methodName($request);
    }

    public function callback($method, Request $request, DonateService $donateService)
    {
        $methodName = "callback" . ucfirst($method);
        if (!method_exists($donateService, $methodName)) {
            return redirect()->back()->withErrors('Invalid payment method.');
        }

        return $donateService->$methodName($request);
    }

    public function webhook($method, Request $request, DonateService $donateService)
    {
        $methodName = "webhook" . ucfirst($method);
        if (!method_exists($donateService, $methodName)) {
            return redirect()->back()->withErrors('Invalid payment method.');
        }

        return $donateService->$methodName($request);
    }
}
