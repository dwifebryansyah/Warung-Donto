<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Debt;
use App\DebtDetail;
use App\Report;
use App\Log;
use App\Order;
use App\OrderDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class DebtController extends Controller
{

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $role = $user->roles->first()->pivot->role_id;

            if ($role == 2) {
                if($user){
                    $data = collect([
                        'name' => $user->name,
                        'email' => $user->email,
                        'id' => $user->id,
                        'role_id' => $role,
                        'user_id' => $user->id,
                    ]);
                    Session::put('user', $data);
                }

                return $next($request);
            }else{
                if($user){
                    $data = collect([
                        'name' => $user->name,
                        'email' => $user->email,
                        'id' => $user->id,
                        'role_id' => $role,
                        'user_id' => $user->id,
                    ]);
                    Session::put('user', $data);
                }

                return $next($request);
            }
        });
    }

    public function index()
    {
        $debts = Debt::paginate(25);
        $carts = Cart::with('Product')->paginate(15);
        $harga = [];
        foreach ($carts as $c) {
            $harga[] = $c->qty * $c->product->harga_jual;
        }

        $subtotal = array_sum($harga);

        $count = $carts->count();

        return view('admin.debts.index')->with(compact('debts','carts','count','subtotal'));


    }

    public function bayar($id)
    {

        $debt = Debt::where('id', $id)->first();
        //dd($debt);

        return view('admin.debts.bayar', compact('debt'));

    }

    public function process(Request $request)
    {
        // dd($request->all());

        $date = Carbon::now()->format('Y-m-d');

        $kdReport = Report::select(['code_report'])->max('code_report');

        $noReport = (int) substr($kdReport, 5, 3);

        $noReport++;
        $charReport = "RP";
        $kdReport = $charReport . sprintf("%05s", $noReport);

        $order = Order::where('id', $request->order_id)->first();
        $totalBayar = $order->total_bayar + $request->uangFix;

        //dd($order);

        $orderDetail = OrderDetail::where('order_id', $request->order_id)
            ->get();

        foreach ($orderDetail as $od) {
            Report::where(
                ['order_id' => $od->order_id],
                ['product_id' => $od->product_id]
            )->update([
                'status' => 1
            ]);
        }


        Order::where(['id' => $request->order_id])->update([
            'total_bayar' => $totalBayar,
            'kembalian' => $totalBayar - $order->total_harga,
        ]);

        Debt::where(['id' => $request->debt_id])->update([
            'total_bayar' => $request->uangFix,
            'kembalian' => $request->uangFix - $request->sisa_bayar,
            'status' => 1
        ]);

        $dateNow = Carbon::now();

        Log::create([
            'user_id' => $request->user,
            'activity' => $request->activity,
            'detail_activity' =>  $request->activity.' hutang dengan nama '.$request->name,
            'tanggal' => $dateNow,
            'debt_change' => 1
        ]);

        return redirect()->route('orders.struk');
    }

    public function destroy(Debt $debt)
    {
        $debt->delete();

        $user = Auth::user();
        $role = $user->roles->first()->pivot->role_id;
        $date = Carbon::now();

        Log::create([
            'user_id' => $user->id,
            'activity' => 'menghapus',
            'detail_activity' => 'menghapus hutang dengan nama '.$debt->name,
            'tanggal' => $date,
            'debt_change' => 1
        ]);

        return redirect()->route('debts.index');
    }
}
