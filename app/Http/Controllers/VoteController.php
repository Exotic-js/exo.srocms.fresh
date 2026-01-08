<?php

namespace App\Http\Controllers;

use App\Models\VoteLog;
use App\Services\VoteService;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function index(Request $request)
    {
        $data = VoteLog::getVoteStatus($request, session('fingerprint'));
        return view('profile.vote', ['data' => $data]);
    }

    public function voting(string $site, Request $request)
    {
        $config = config("vote.$site");
        abort_if(!$config || !$config['enabled'], 404);

        $user = $request->user();

        $fingerprint = $request->query('fingerprint') ?? session('fingerprint');
        if ($fingerprint && session('fingerprint') !== $fingerprint) {
            session(['fingerprint' => $fingerprint]);
        }

        if (!$fingerprint) {
            return back()->with('error', 'Fingerprint not detected.');
        }

        $voteLog = VoteLog::activeVote($config['route'], $request->ip(), $fingerprint);
        if ($voteLog) {
            return back()->with('error', "You have already voted. Please wait until {$voteLog->expire} to vote again for {$config['name']}.");
        }

        VoteLog::updateOrCreate(
            ['jid' => $user->jid, 'site' => $config['route'],],
            ['ip' => $request->ip(), 'fingerprint' => $fingerprint,]
        );

        $url = str_replace('{JID}', $user->jid, $config['url']);

        return redirect()->away($url);
    }

    public function postback($site, Request $request, VoteService $voteService)
    {
        $config = config("vote.{$site}");

        if (!$config || !$config['enabled']) {
            return redirect()->back()->withErrors('Vote Site not found or disabled.');
        }

        if (!method_exists($voteService, "postback" . ucfirst($site))) {
            return redirect()->back()->withErrors('Invalid postback method.');
        }

        return $voteService->{"postback" . ucfirst($site)}($request);
    }
}
