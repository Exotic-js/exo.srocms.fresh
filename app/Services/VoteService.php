<?php

namespace App\Services;

use App\Models\Donate;
use App\Models\SRO\Account\SkSilk;
use App\Models\SRO\Portal\AphChangedSilk;
use App\Models\User;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VoteService
{
    public function postbackXtremetop100(Request $request)
    {
        $config = config("vote.xtremetop100");
        $remoteIp = $request->server('HTTP_CF_CONNECTING_IP') ?? $request->ip();

        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps, true)) {
            Log::warning("Unauthorized {$config['name']} postback", ['ip' => $remoteIp]);
            return response("Unauthorized IP: {$remoteIp}", 401);
        }

        $jid = $request->input('custom');
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        $user = User::where('jid', (int)$jid)->first();
        if (!$user) {
            return response('User not found', 200);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 12;
        $rewardAmount = $config['reward'] ?? 0;

        $voteLog = Vote::where('jid', $jid)->where('site', $config['route'])->first();
        if ($voteLog && $voteLog->expire && $now->lessThan(Carbon::parse($voteLog->expire))) {
            return response("Cooldown active until {$voteLog->expire}", 200);
        }

        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 0, $rewardAmount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $rewardAmount);
        }

        Donate::DonateLog([
            'method' => "Vote [{$config['name']}]",
            'value' => $rewardAmount,
            'jid' => $user->jid,
        ]);

        Vote::updateOrCreate(['jid' => $jid, 'site' => $config['route']], ['ip' => $remoteIp, 'expire' => $now->addHours($timeout)]);

        return response("OK", 200);
    }

    public function postbackGtop100(Request $request)
    {
        $config = config("vote.gtop100");
        $remoteIp = $request->server('HTTP_CF_CONNECTING_IP') ?? $request->ip();

        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps, true)) {
            Log::warning("Unauthorized {$config['name']} postback", ['ip' => $remoteIp]);
            return response("Unauthorized IP: {$remoteIp}", 401);
        }

        $jid = $request->input('pingUsername');
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        if((int)$request->input('Successful') == 1) {
            return response($request->input('Reason') ?? 'Vote not successful', 200);
        }

        $user = User::where('jid', (int)$jid)->first();
        if (!$user) {
            return response('User not found', 200);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 12;
        $rewardAmount = $config['reward'] ?? 0;

        $voteLog = Vote::where('jid', $jid)->where('site', $config['route'])->first();
        if ($voteLog && $voteLog->expire && $now->lessThan(Carbon::parse($voteLog->expire))) {
            return response("Cooldown active until {$voteLog->expire}", 200);
        }

        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 0, $rewardAmount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $rewardAmount);
        }

        Donate::DonateLog([
            'method' => "Vote [{$config['name']}]",
            'value' => $rewardAmount,
            'jid' => $user->jid,
        ]);

        Vote::updateOrCreate(['jid' => $jid, 'site' => $config['route']], ['ip' => $remoteIp, 'expire' => $now->addHours($timeout)]);

        return response("OK", 200);
    }

    public function postbackTopg(Request $request)
    {
        $config = config("vote.topg");
        $remoteIp = $request->server('HTTP_CF_CONNECTING_IP') ?? $request->ip();

        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps, true)) {
            Log::warning("Unauthorized {$config['name']} postback", ['ip' => $remoteIp]);
            return response("Unauthorized IP: {$remoteIp}", 401);
        }

        $jid = $request->input('p_resp');
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        $user = User::where('jid', (int)$jid)->first();
        if (!$user) {
            return response('User not found', 200);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 12;
        $rewardAmount = $config['reward'] ?? 0;

        $voteLog = Vote::where('jid', $jid)->where('site', $config['route'])->first();
        if ($voteLog && $voteLog->expire && $now->lessThan(Carbon::parse($voteLog->expire))) {
            return response("Cooldown active until {$voteLog->expire}", 200);
        }

        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 0, $rewardAmount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $rewardAmount);
        }

        Donate::DonateLog([
            'method' => "Vote [{$config['name']}]",
            'value' => $rewardAmount,
            'jid' => $user->jid,
        ]);

        Vote::updateOrCreate(['jid' => $jid, 'site' => $config['route']], ['ip' => $remoteIp, 'expire' => $now->addHours($timeout)]);

        return response("OK", 200);
    }

    public function postbackTop100arena(Request $request)
    {
        $config = config("vote.top100arena");
        $remoteIp = $request->server('HTTP_CF_CONNECTING_IP') ?? $request->ip();

        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps, true)) {
            Log::warning("Unauthorized {$config['name']} postback", ['ip' => $remoteIp]);
            return response("Unauthorized IP: {$remoteIp}", 401);
        }

        $jid = $request->input('postback');
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        $user = User::where('jid', (int)$jid)->first();
        if (!$user) {
            return response('User not found', 200);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 12;
        $rewardAmount = $config['reward'] ?? 0;

        $voteLog = Vote::where('jid', $jid)->where('site', $config['route'])->first();
        if ($voteLog && $voteLog->expire && $now->lessThan(Carbon::parse($voteLog->expire))) {
            return response("Cooldown active until {$voteLog->expire}", 200);
        }

        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 0, $rewardAmount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $rewardAmount);
        }

        Donate::DonateLog([
            'method' => "Vote [{$config['name']}]",
            'value' => $rewardAmount,
            'jid' => $user->jid,
        ]);

        Vote::updateOrCreate(['jid' => $jid, 'site' => $config['route']], ['ip' => $remoteIp, 'expire' => $now->addHours($timeout)]);

        return response("OK", 200);
    }

    public function postbackArenatop100(Request $request)
    {
        $config = config("vote.arenatop100");
        $remoteIp = $request->server('HTTP_CF_CONNECTING_IP') ?? $request->ip();

        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps, true)) {
            Log::warning("Unauthorized {$config['name']} postback", ['ip' => $remoteIp]);
            return response("Unauthorized IP: {$remoteIp}", 401);
        }

        $jid = $request->input('userid');
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        if ((int)$request->input('voted') !== 1) {
            return response("User $jid voted already today!", 200);
        }

        $user = User::where('jid', (int)$jid)->first();
        if (!$user) {
            return response('User not found', 200);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 12;
        $rewardAmount = $config['reward'] ?? 0;

        $voteLog = Vote::where('jid', $jid)->where('site', $config['route'])->first();
        if ($voteLog && $voteLog->expire && $now->lessThan(Carbon::parse($voteLog->expire))) {
            return response("Cooldown active until {$voteLog->expire}", 200);
        }

        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 0, $rewardAmount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $rewardAmount);
        }

        Donate::DonateLog([
            'method' => "Vote [{$config['name']}]",
            'value' => $rewardAmount,
            'jid' => $user->jid,
        ]);

        Vote::updateOrCreate(['jid' => $jid, 'site' => $config['route']], ['ip' => $remoteIp, 'expire' => $now->addHours($timeout)]);

        return response("OK", 200);
    }

    public function postbackSilkroadservers(Request $request)
    {
        $config = config("vote.silkroadservers");
        $remoteIp = $request->server('HTTP_CF_CONNECTING_IP') ?? $request->ip();

        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps, true)) {
            Log::warning("Unauthorized {$config['name']} postback", ['ip' => $remoteIp]);
            return response("Unauthorized IP: {$remoteIp}", 401);
        }

        $jid = $request->input('userid');
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        $user = User::where('jid', (int)$jid)->first();
        if (!$user) {
            return response('User not found', 200);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 12;
        $rewardAmount = $config['reward'] ?? 0;

        $voteLog = Vote::where('jid', $jid)->where('site', $config['route'])->first();
        if ($voteLog && $voteLog->expire && $now->lessThan(Carbon::parse($voteLog->expire))) {
            return response("Cooldown active until {$voteLog->expire}", 200);
        }

        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 0, $rewardAmount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $rewardAmount);
        }

        Donate::DonateLog([
            'method' => "Vote [{$config['name']}]",
            'value' => $rewardAmount,
            'jid' => $user->jid,
        ]);

        Vote::updateOrCreate(['jid' => $jid, 'site' => $config['route']], ['ip' => $remoteIp, 'expire' => $now->addHours($timeout)]);

        return response("OK", 200);
    }

    public function postbackPrivateserver(Request $request)
    {
        $config = config("vote.privateserver");
        $remoteIp = $request->server('HTTP_CF_CONNECTING_IP') ?? $request->ip();

        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps, true)) {
            Log::warning("Unauthorized {$config['name']} postback", ['ip' => $remoteIp]);
            return response("Unauthorized IP: {$remoteIp}", 401);
        }

        $jid = $request->input('userid');
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        $user = User::where('jid', (int)$jid)->first();
        if (!$user) {
            return response('User not found', 200);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 12;
        $rewardAmount = $config['reward'] ?? 0;

        $voteLog = Vote::where('jid', $jid)->where('site', $config['route'])->first();
        if ($voteLog && $voteLog->expire && $now->lessThan(Carbon::parse($voteLog->expire))) {
            return response("Cooldown active until {$voteLog->expire}", 200);
        }

        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 0, $rewardAmount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $rewardAmount);
        }

        Donate::DonateLog([
            'method' => "Vote [{$config['name']}]",
            'value' => $rewardAmount,
            'jid' => $user->jid,
        ]);

        Vote::updateOrCreate(['jid' => $jid, 'site' => $config['route']], ['ip' => $remoteIp, 'expire' => $now->addHours($timeout)]);

        return response("OK", 200);
    }

    public function postbackVote4rewards(Request $request)
    {
        $config = config("vote.vote4rewards");
        $remoteIp = $request->server('HTTP_CF_CONNECTING_IP') ?? $request->ip();

        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps, true)) {
            Log::warning("Unauthorized {$config['name']} postback", ['ip' => $remoteIp]);
            return response("Unauthorized IP: {$remoteIp}", 401);
        }

        $serverId = $request->input('server_uuid');
        $webhookSecret = $config['webhook_secret'];
        $signature = $request->header('X-Webhook-Signature');
        /*
        if ($signature !== $webhookSecret) {
            return response('Wrong Signature', 403);
        }
        */

        $jid = $request->input('voter_id');
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        $user = User::where('jid', (int)$jid)->first();
        if (!$user) {
            return response('User not found', 200);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 6;
        $rewardAmount = $config['reward'] ?? 0;

        $voteLog = Vote::where('jid', $jid)->where('site', $config['route'])->first();
        if ($voteLog && $voteLog->expire && $now->lessThan(Carbon::parse($voteLog->expire))) {
            return response("Cooldown active until {$voteLog->expire}", 200);
        }

        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 0, $rewardAmount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $rewardAmount);
        }

        Donate::DonateLog([
            'method' => "Vote [{$config['name']}]",
            'value' => $rewardAmount,
            'jid' => $user->jid,
        ]);

        Vote::updateOrCreate(['jid' => $jid, 'site' => $config['route']], ['ip' => $remoteIp, 'expire' => $now->addHours($timeout)]);

        return response("OK", 200);
    }
}
