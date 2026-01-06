<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $data = Voucher::paginate(20);
        return view('admin.vouchers.index', compact('data'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'type' => 'required|integer',
            'valid_date' => 'nullable|date',
        ]);

        $code = strtoupper(implode('-', str_split(bin2hex(random_bytes(10)), 5)));

        Voucher::create([
            'code' => $code,
            'amount' => $request->amount,
            'type' => $request->type,
            'valid_date' => $request->valid_date,
            'status' => 'Unused',
        ]);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher created successfully!');
    }

    public function toggle(Voucher $voucher)
    {
        $voucher->update(['status' => $voucher->status === 'Unused' ? 'Disabled' : 'Unused']);

        $action = $voucher->status === 'Unused' ? 'enabled' : 'disabled';

        return redirect()->back()->with('success', "Voucher {$action} successfully!");
    }
}
