<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Service;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $transactions = Transaction::with('status')
            ->where('member_id', $user->id)
            ->orderBy('created_at', 'DESC')
            ->orderBy('status_id', 'ASC')
            ->get();

        return view('member.transactions_history', compact('user', 'transactions'));
    }

    public function show(string|int $id): View
    {
        $user = Auth::user();
        $transaction = Transaction::with('service_type')->findOrFail($id);

        $transactions = TransactionDetail::with([
            'price_list.item',
            'price_list.service',
            'price_list.category',
        ])->where('transaction_id', $id)->get();

        return view('member.detail', compact('user', 'transaction', 'transactions'));
    }

    public function create(): View
    {
        $items = Item::all();
        $services = Service::all();
        $categories = Category::all();

        return view('member.transaction_input', compact('items', 'services', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item' => 'required|exists:items,id',
            'service' => 'required|exists:services,id',
            'category' => 'required|exists:categories,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();

        $transaction = Transaction::create([
            'member_id' => $user->id,
            'status_id' => 1,
            'total_price' => 0, // akan diupdate nanti jika perlu
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'item_id' => $request->item,
            'service_id' => $request->service,
            'category_id' => $request->category,
            'quantity' => $request->quantity,
        ]);

        return redirect()->route('member.transactions.index')->with('success', 'Pesanan berhasil dibuat!');
    }
}
