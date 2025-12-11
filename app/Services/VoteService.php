<?php

namespace App\Services;

use App\Models\DonateLog;
use App\Models\SRO\Account\SkSilk;
use App\Models\SRO\Portal\AphChangedSilk;
use App\Models\User;
use App\Models\VoteLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VoteService
{
    public function postbackXtremetop100(Request $request)
    {
        $config = config("vote.xtremetop100");
        $remoteIp = $request->header('CF-Connecting-IP') ?? $request->server('HTTP_CF_CONNECTING_IP') ?? $request->ip();
        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps)) {
            Log::warning("Unauthorized IP: $remoteIp (expected: " . json_encode($allowedIps) . ")");
            return response('Unauthorized IP: ' . $remoteIp, 401);
        }

        $data = $request->isMethod('POST') ? $request->post() : $request->query();
        $votingip = $data['votingip'] ?? null;
        $jid = $data['custom'] ?? null;
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 24;
        $voteLog = VoteLog::where('jid', $jid)->where('site', $config['route'])->first();
        if ($voteLog && $voteLog->expire && $now->lessThan($voteLog->expire)) {
            return response("Cooldown active until {$voteLog->expire}", 200);
        }

        $user = User::where('jid', $jid)->first();
        if (!$user) {
            return response('User not found', 404);
        }

        $rewardAmount = $config['reward'] ?? 0;
        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 0, $rewardAmount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $rewardAmount);
        }

        DonateLog::setDonateLog('Vote', (string) Str::uuid(), 'true', 0, $rewardAmount, "[{$config['name']}] User {$user->username} earned {$rewardAmount} silk.", $user->jid, $remoteIp);
        VoteLog::updateOrCreate(['jid' => $jid, 'site' => $config['route']], ['ip' => $remoteIp, 'expire' => $now->clone()->addHours($timeout),]);

        return response("OK", 200);
    }

    public function postbackGtop100(Request $request)
    {
        $config = config("vote.gtop100");
        $remoteIp = $request->header('CF-Connecting-IP') ?? $request->server('HTTP_CF_CONNECTING_IP') ?? $request->ip();
        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps)) {
            Log::warning("Unauthorized IP: $remoteIp (expected: " . json_encode($allowedIps) . ")");
            return response('Unauthorized IP: ' . $remoteIp, 401);
        }

        $data = $request->isMethod('POST') ? $request->post() : $request->query();
        $voterIP = $data["VoterIP"] ?? null;
        $success = abs((int)($data["Successful"] ?? 1));
        $reason = $data["Reason"] ?? null;
        $pingbackkey = $data["pingbackkey"] ?? null;
        $jid = $data["pingUsername"] ?? null;
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        /*
        if ($pingbackkey !== $config['pingbackkey']) {
            return response('Invalid pingback key.', 403);
        }
        */

        if(abs($data['Successful']) == 1) {
            return response($data['Reason'] ?? 'Vote not successful', 200);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 24;
        $voteLog = VoteLog::where('jid', $jid)->where('site', $config['route'])->first();
        if ($voteLog && $voteLog->expire && $now->lessThan($voteLog->expire)) {
            return response("Cooldown active until {$voteLog->expire}", 200);
        }

        $user = User::where('jid', $jid)->first();
        if (!$user) {
            return response('User not found', 404);
        }

        $rewardAmount = $config['reward'] ?? 0;
        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 0, $rewardAmount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $rewardAmount);
        }

        DonateLog::setDonateLog('Vote', (string) Str::uuid(), 'true', 0, $rewardAmount, "[{$config['name']}] User {$user->username} earned {$rewardAmount} silk.", $user->jid, $remoteIp);
        VoteLog::updateOrCreate(['jid' => $jid, 'site' => $config['route']], ['ip' => $remoteIp, 'expire' => $now->clone()->addHours($timeout),]);

        return response("OK", 200);
    }

    public function postbackTopg(Request $request)
    {
        $config = config("vote.topg");
        $remoteIp = $request->header('CF-Connecting-IP') ?? $request->server('HTTP_CF_CONNECTING_IP') ?? $request->ip();
        //$allowedIps = array_map('trim', explode(',', $config['ip']));
        $allowedIps = gethostbyname('monitor.topg.org');
        if ($remoteIp !== $allowedIps) {
            Log::warning("Unauthorized IP: $remoteIp (expected: " . json_encode($allowedIps) . ")");
            return response('Unauthorized IP: ' . $remoteIp, 401);
        }

        $data = $request->isMethod('POST') ? $request->post() : $request->query();
        $votingip = $data['ip'] ?? null;
        $jid = $data['p_resp'] ?? null;
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 24;
        $voteLog = VoteLog::where('jid', $jid)->where('site', $config['route'])->first();
        if ($voteLog && $voteLog->expire && $now->lessThan($voteLog->expire)) {
            return response("Cooldown active until {$voteLog->expire}", 200);
        }

        $user = User::where('jid', $jid)->first();
        if (!$user) {
            return response('User not found', 404);
        }

        $rewardAmount = $config['reward'] ?? 0;
        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 0, $rewardAmount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $rewardAmount);
        }

        DonateLog::setDonateLog('Vote', (string) Str::uuid(), 'true', 0, $rewardAmount, "[{$config['name']}] User {$user->username} earned {$rewardAmount} silk.", $user->jid, $remoteIp);
        VoteLog::updateOrCreate(['jid' => $jid, 'site' => $config['route']], ['ip' => $remoteIp, 'expire' => $now->clone()->addHours($timeout),]);

        return response("OK", 200);
    }

    public function postbackTop100arena(Request $request)
    {
        $config = config("vote.top100arena");
        $remoteIp = $request->header('CF-Connecting-IP') ?? $request->server('HTTP_CF_CONNECTING_IP') ?? $request->ip();
        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps)) {
            Log::warning("Unauthorized IP: $remoteIp (expected: " . json_encode($allowedIps) . ")");
            return response('Unauthorized IP: ' . $remoteIp, 401);
        }

        $data = $request->isMethod('POST') ? $request->post() : $request->query();
        $jid = $data['postback'] ?? null;
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 24;
        $voteLog = VoteLog::where('jid', $jid)->where('site', $config['route'])->first();
        if ($voteLog && $voteLog->expire && $now->lessThan($voteLog->expire)) {
            return response("Cooldown active until {$voteLog->expire}", 200);
        }

        $user = User::where('jid', $jid)->first();
        if (!$user) {
            return response('User not found', 404);
        }

        $rewardAmount = $config['reward'] ?? 0;
        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 0, $rewardAmount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $rewardAmount);
        }

        DonateLog::setDonateLog('Vote', (string) Str::uuid(), 'true', 0, $rewardAmount, "[{$config['name']}] User {$user->username} earned {$rewardAmount} silk.", $user->jid, $remoteIp);
        VoteLog::updateOrCreate(['jid' => $jid, 'site' => $config['route']], ['ip' => $remoteIp, 'expire' => $now->clone()->addHours($timeout),]);

        return response("OK", 200);
    }

    public function postbackArenatop100(Request $request)
    {
        $config = config("vote.arenatop100");
        $remoteIp = $request->header('CF-Connecting-IP') ?? $request->server('HTTP_CF_CONNECTING_IP') ?? $request->ip();
        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps)) {
            Log::warning("Unauthorized IP: $remoteIp (expected: " . json_encode($allowedIps) . ")");
            return response('Unauthorized IP: ' . $remoteIp, 401);
        }

        $data = $request->isMethod('POST') ? $request->post() : $request->query();
        $secret = $data['secret'] ?? false;
        $jid = $data['userid'] ?? null;
        $userip = $data['userip'] ?? null;
        $valid = $data['voted'] ?? null;
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        if (!isset($data['voted']) || (int)$data['voted'] !== 1) {
            return response("User $jid voted already today!", 200);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 24;
        $voteLog = VoteLog::where('jid', $jid)->where('site', $config['route'])->first();
        if ($voteLog && $voteLog->expire && $now->lessThan($voteLog->expire)) {
            return response("Cooldown active until {$voteLog->expire}", 200);
        }

        $user = User::where('jid', $jid)->first();
        if (!$user) {
            return response('User not found', 404);
        }

        $rewardAmount = $config['reward'] ?? 0;
        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 0, $rewardAmount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $rewardAmount);
        }

        DonateLog::setDonateLog('Vote', (string) Str::uuid(), 'true', 0, $rewardAmount, "[{$config['name']}] User {$user->username} earned {$rewardAmount} silk.", $user->jid, $remoteIp);
        VoteLog::updateOrCreate(['jid' => $jid, 'site' => $config['route']], ['ip' => $remoteIp, 'expire' => $now->clone()->addHours($timeout),]);

        return response("OK", 200);
    }

    public function postbackSilkroadservers(Request $request)
    {
        $config = config("vote.silkroadservers");
        $remoteIp = $request->header('CF-Connecting-IP') ?? $request->server('HTTP_CF_CONNECTING_IP') ?? $request->ip();
        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps)) {
            Log::warning("Unauthorized IP: $remoteIp (expected: " . json_encode($allowedIps) . ")");
            return response('Unauthorized IP: ' . $remoteIp, 401);
        }

        $data = $request->isMethod('POST') ? $request->post() : $request->query();
        $valid = $data['voted'] ?? null;
        $jid = $data['userid'] ?? null;
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        if (!isset($data['voted']) || (int)$data['voted'] !== 1) {
            return response("User $jid voted already today!", 200);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 24;
        $voteLog = VoteLog::where('jid', $jid)->where('site', $config['route'])->first();
        if ($voteLog && $voteLog->expire && $now->lessThan($voteLog->expire)) {
            return response("Cooldown active until {$voteLog->expire}", 200);
        }

        $user = User::where('jid', $jid)->first();
        if (!$user) {
            return response('User not found', 404);
        }

        $rewardAmount = $config['reward'] ?? 0;
        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 0, $rewardAmount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $rewardAmount);
        }

        DonateLog::setDonateLog('Vote', (string) Str::uuid(), 'true', 0, $rewardAmount, "[{$config['name']}] User {$user->username} earned {$rewardAmount} silk.", $user->jid, $remoteIp);
        VoteLog::updateOrCreate(['jid' => $jid, 'site' => $config['route']], ['ip' => $remoteIp, 'expire' => $now->clone()->addHours($timeout),]);

        return response("OK", 200);
    }

    public function postbackPrivateserver(Request $request)
    {
        $config = config("vote.privateserver");
        $remoteIp = $request->header('CF-Connecting-IP') ?? $request->server('HTTP_CF_CONNECTING_IP') ?? $request->ip();
        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps)) {
            Log::warning("Unauthorized IP: $remoteIp (expected: " . json_encode($allowedIps) . ")");
            return response('Unauthorized IP: ' . $remoteIp, 401);
        }

        $data = $request->isMethod('POST') ? $request->post() : $request->query();
        $valid = $data['voted'] ?? null;
        $jid = $data['userid'] ?? null;
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        if (!isset($data['voted']) || (int)$data['voted'] !== 1) {
            return response("User $jid voted already today!", 200);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 24;
        $voteLog = VoteLog::where('jid', $jid)->where('site', $config['route'])->first();
        if ($voteLog && $voteLog->expire && $now->lessThan($voteLog->expire)) {
            return response("Cooldown active until {$voteLog->expire}", 200);
        }

        $user = User::where('jid', $jid)->first();
        if (!$user) {
            return response('User not found', 404);
        }

        $rewardAmount = $config['reward'] ?? 0;
        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 0, $rewardAmount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $rewardAmount);
        }

        DonateLog::setDonateLog('Vote', (string) Str::uuid(), 'true', 0, $rewardAmount, "[{$config['name']}] User {$user->username} earned {$rewardAmount} silk.", $user->jid, $remoteIp);
        VoteLog::updateOrCreate(['jid' => $jid, 'site' => $config['route']], ['ip' => $remoteIp, 'expire' => $now->clone()->addHours($timeout),]);

        return response("OK", 200);
    }
}
