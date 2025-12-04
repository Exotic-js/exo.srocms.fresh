<?php

namespace App\Services;

use App\Models\DonateLog;
use App\Models\SRO\Account\SkSilk;
use App\Models\SRO\Portal\AphChangedSilk;
use App\Models\User;
use App\Models\VoteLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VoteService
{
    public function postbackXtremetop100(Request $request)
    {
        $config = config("vote.xtremetop100");
        $remoteIp = self::getRealIP($request);

        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps)) {
            return response('Unauthorized IP', 401);
        }

        $data = $request->isMethod('POST') ? $request->post() : $request->query();
        $jid = $data['custom'] ?? null;
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 24;
        $voteLog = VoteLog::where('jid', $jid)->where('site', $config['route'])->first();

        if ($voteLog && $voteLog->expire && $now->lessThan(Carbon::parse($voteLog->expire))) {
            return response("User $jid must wait until {$voteLog->expire} to vote again for {$config['name']}.", 200);
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

        DonateLog::setDonateLog(
            'Vote',
            (string) Str::uuid(),
            'true',
            0,
            $rewardAmount,
            "User: {$user->username} earned {$rewardAmount} silk for voting on {$config['name']}.",
            $user->jid,
            $remoteIp
        );

        VoteLog::updateOrCreate(
            ['jid' => $jid, 'site' => $config['route']],
            [
                'ip' => $remoteIp,
                'expire' => $now->addHours((int) $timeout),
            ]
        );

        return response("Vote registered and user rewarded!", 200);
    }

    public function postbackGtop100(Request $request)
    {
        $config = config("vote.gtop100");
        $remoteIp = self::getRealIP($request);

        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps)) {
            return response('Unauthorized IP', 401);
        }

        $data = $request->isMethod('POST') ? $request->post() : $request->query();
        $jid = $data['pingUsername'] ?? null;
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        if (empty($data['Successful']) || abs($data['Successful']) !== 0) {
            return response($data['Reason'] ?? 'Vote not successful', 200);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 24;
        $voteLog = VoteLog::where('jid', $jid)->where('site', $config['route'])->first();

        if ($voteLog && $voteLog->expire && $now->lessThan(Carbon::parse($voteLog->expire))) {
            return response("User $jid must wait until {$voteLog->expire} to vote again for {$config['name']}.", 200);
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

        DonateLog::setDonateLog(
            'Vote',
            (string) Str::uuid(),
            'true',
            0,
            $rewardAmount,
            "User: {$user->username} earned {$rewardAmount} silk for voting on {$config['name']}.",
            $user->jid,
            $remoteIp
        );

        VoteLog::updateOrCreate(
            ['jid' => $jid, 'site' => $config['route']],
            [
                'ip' => $remoteIp,
                'expire' => $now->addHours((int) $timeout),
            ]
        );

        return response("Vote registered and user rewarded!", 200);
    }

    public function postbackTopg(Request $request)
    {
        $config = config("vote.topg");
        $remoteIp = self::getRealIP($request);

        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps)) {
            return response('Unauthorized IP', 401);
        }

        $data = $request->isMethod('POST') ? $request->post() : $request->query();
        $jid = $data['p_resp'] ?? null;
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 24;
        $voteLog = VoteLog::where('jid', $jid)->where('site', $config['route'])->first();

        if ($voteLog && $voteLog->expire && $now->lessThan(Carbon::parse($voteLog->expire))) {
            return response("User $jid must wait until {$voteLog->expire} to vote again for {$config['name']}.", 200);
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

        DonateLog::setDonateLog(
            'Vote',
            (string) Str::uuid(),
            'true',
            0,
            $rewardAmount,
            "User: {$user->username} earned {$rewardAmount} silk for voting on {$config['name']}.",
            $user->jid,
            $remoteIp
        );

        VoteLog::updateOrCreate(
            ['jid' => $jid, 'site' => $config['route']],
            [
                'ip' => $remoteIp,
                'expire' => $now->addHours((int) $timeout),
            ]
        );

        return response("Vote registered and user rewarded!", 200);
    }

    public function postbackTop100arena(Request $request)
    {
        $config = config("vote.top100arena");
        $remoteIp = self::getRealIP($request);

        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps)) {
            return response('Unauthorized IP', 401);
        }

        $data = $request->isMethod('POST') ? $request->post() : $request->query();
        $jid = $data['postback'] ?? null;
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 24;
        $voteLog = VoteLog::where('jid', $jid)->where('site', $config['route'])->first();

        if ($voteLog && $voteLog->expire && $now->lessThan(Carbon::parse($voteLog->expire))) {
            return response("User $jid must wait until {$voteLog->expire} to vote again for {$config['name']}.", 200);
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

        DonateLog::setDonateLog(
            'Vote',
            (string) Str::uuid(),
            'true',
            0,
            $rewardAmount,
            "User: {$user->username} earned {$rewardAmount} silk for voting on {$config['name']}.",
            $user->jid,
            $remoteIp
        );

        VoteLog::updateOrCreate(
            ['jid' => $jid, 'site' => $config['route']],
            [
                'ip' => $remoteIp,
                'expire' => $now->addHours((int) $timeout),
            ]
        );

        return response("Vote registered and user rewarded!", 200);
    }

    public function postbackArenatop100(Request $request)
    {
        $config = config("vote.arenatop100");
        $remoteIp = self::getRealIP($request);

        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps)) {
            return response('Unauthorized IP', 401);
        }

        $data = $request->isMethod('POST') ? $request->post() : $request->query();
        $jid = $data['userid'] ?? null;
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        if (empty($data['voted']) || (int)$data['voted'] !== 1) {
            return response("User $jid voted already today!", 200);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 24;
        $voteLog = VoteLog::where('jid', $jid)->where('site', $config['route'])->first();

        if ($voteLog && $voteLog->expire && $now->lessThan(Carbon::parse($voteLog->expire))) {
            return response("User $jid must wait until {$voteLog->expire} to vote again for {$config['name']}.", 200);
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

        DonateLog::setDonateLog(
            'Vote',
            (string) Str::uuid(),
            'true',
            0,
            $rewardAmount,
            "User: {$user->username} earned {$rewardAmount} silk for voting on {$config['name']}.",
            $user->jid,
            $remoteIp
        );

        VoteLog::updateOrCreate(
            ['jid' => $jid, 'site' => $config['route']],
            [
                'ip' => $remoteIp,
                'expire' => $now->addHours((int) $timeout),
            ]
        );

        return response("Vote registered and user rewarded!", 200);
    }

    public function postbackSilkroadservers(Request $request)
    {
        $config = config("vote.silkroadservers");
        $remoteIp = self::getRealIP($request);

        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps)) {
            return response('Unauthorized IP', 401);
        }

        $data = $request->isMethod('POST') ? $request->post() : $request->query();
        $jid = $data['userid'] ?? null;
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        if (empty($data['voted']) || (int)$data['voted'] !== 1) {
            return response("User $jid voted already today!", 200);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 24;
        $voteLog = VoteLog::where('jid', $jid)->where('site', $config['route'])->first();

        if ($voteLog && $voteLog->expire && $now->lessThan(Carbon::parse($voteLog->expire))) {
            return response("User $jid must wait until {$voteLog->expire} to vote again for {$config['name']}.", 200);
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

        DonateLog::setDonateLog(
            'Vote',
            (string) Str::uuid(),
            'true',
            0,
            $rewardAmount,
            "User: {$user->username} earned {$rewardAmount} silk for voting on {$config['name']}.",
            $user->jid,
            $remoteIp
        );

        VoteLog::updateOrCreate(
            ['jid' => $jid, 'site' => $config['route']],
            [
                'ip' => $remoteIp,
                'expire' => $now->addHours((int) $timeout),
            ]
        );

        return response("Vote registered and user rewarded!", 200);
    }

    public function postbackPrivateserver(Request $request)
    {
        $config = config("vote.privateserver");
        $remoteIp = self::getRealIP($request);

        $allowedIps = array_map('trim', explode(',', $config['ip']));
        if (!in_array($remoteIp, $allowedIps)) {
            return response('Unauthorized IP', 401);
        }

        $data = $request->isMethod('POST') ? $request->post() : $request->query();
        $jid = $data['userid'] ?? null;
        if (!$jid) {
            return response('Missing user ID', 400);
        }

        if (empty($data['voted']) || (int)$data['voted'] !== 1) {
            return response("User $jid voted already today!", 200);
        }

        $now = Carbon::now();
        $timeout = $config['timeout'] ?? 24;
        $voteLog = VoteLog::where('jid', $jid)->where('site', $config['route'])->first();

        if ($voteLog && $voteLog->expire && $now->lessThan(Carbon::parse($voteLog->expire))) {
            return response("User $jid must wait until {$voteLog->expire} to vote again for {$config['name']}.", 200);
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

        DonateLog::setDonateLog(
            'Vote',
            (string) Str::uuid(),
            'true',
            0,
            $rewardAmount,
            "User: {$user->username} earned {$rewardAmount} silk for voting on {$config['name']}.",
            $user->jid,
            $remoteIp
        );

        VoteLog::updateOrCreate(
            ['jid' => $jid, 'site' => $config['route']],
            [
                'ip' => $remoteIp,
                'expire' => $now->addHours((int) $timeout),
            ]
        );

        return response("Vote registered and user rewarded!", 200);
    }

    private function getRealIP($request)
    {
        if ($request->hasHeader('CF-Connecting-IP')) {
            return $request->header('CF-Connecting-IP');
        }

        if ($request->hasHeader('X-Forwarded-For')) {
            $ips = explode(',', $request->header('X-Forwarded-For'));
            return trim($ips[0]);
        }

        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }

        return $request->ip();
    }
}
