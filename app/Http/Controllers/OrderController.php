<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderDetail;
use App\Debt;
use App\DebtDetail;
use App\Cart;
use App\Product;
use App\Report;
use App\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class OrderController extends Controller
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
                    // dd($data);
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
        //
    }


    public function create()
    {
        $carts = Cart::with('Product')->paginate(15);
        $harga = [];
        foreach ($carts as $c) {
            $harga[] = $c->qty * $c->product->harga_jual;
        }

        $subtotal = array_sum($harga);

        $count = $carts->count();

        return view('admin.orders.add')->with(compact('carts','count','subtotal'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $date = Carbon::now()->format('Y-m-d');


        // CODE ORDER
        $kdOrder = Order::select(['code'])->max('code');

        $noUrut = (int) substr($kdOrder, 5, 3);

        $noUrut++;
        $char = "OR";
        $kdOrder = $char . sprintf("%05s", $noUrut);

        // CODE DEBT
        $kdDebt = Debt::select(['code_debt'])->max('code_debt');

        $noDebt = (int) substr($kdDebt, 5, 3);

        $noDebt++;
        $charDebt = "DB";
        $kdDebt = $charDebt . sprintf("%05s", $noDebt);

        $order = Order::create([
            'code' => $kdOrder,
            'tanggal' => $date,
            'kembalian' => $request->duit - $request->subtotal,
            'total_bayar' => $request->duit,
            'total_harga' => $request->subtotal,
        ]);

        if(!$request->nama == null){

            $sisa = $request->subtotal - $request->duit;

            $debt = Debt::create([
                'name' => $request->nama,
                'order_id' => $order->id,
                'code_debt' => $kdDebt,
                'tanggal' => $date,
                'total_sebelumnya' => $request->subtotal,
                'sudah_bayar' => $request->duit,
                'sisa_bayar' => $sisa,
                'total_bayar' => 0,
                'kembalian' => 0,
            ]);

            foreach($request->Order as $key){
                DebtDetail::create([
                    'debt_id' => $debt->id,
                    'product_id' => $key['product_id'],
                    'qty' => $key['qty']
                ]);
            }

        }

        //dd($request->Order);
        foreach($request->Order as $key){
            //dd($key);
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $key['product_id'],
                'qty' => $key['qty']
            ]);

            $reports = $order->orderDetails()->latest()->first();
            //dd($reports);

            // CODE Report
            $kdReport = Report::select(['code_report'])->max('code_report');
            $noUrut = (int) substr($kdReport, 5, 3);
            $noUrut++;
            $char = "RP";
            $kdReport = $char . sprintf("%05s", $noUrut);

            $report = Report::where('product_id',$key['product_id'])
                ->latest()
                ->first();

            // dd($report);

            if ($report) {

                // $report_now = Report::where('product_id',$reports->product_id)->first();

                if(!$request->nama == null){

                    Report::create([
                        'product_id' => $key['product_id'],
                        'order_id' => $order->id,
                        'tanggal' => $date,
                        'jumlah_awal' => $report->jumlah_akhir,
                        'jumlah_jual' => $key['qty'],
                        'jumlah_akhir' => $report->jumlah_akhir - $key['qty'],
                        'harga' => $report->harga,
                        'keterangan' => null,
                        'code_report' => $kdReport,
                        'jenis_laporan' => 'barang'
                    ]);

                }else{

                    Report::create([
                        'product_id' => $key['product_id'],
                        'order_id' => $order->id,
                        'tanggal' => $date,
                        'jumlah_awal' => $report->jumlah_akhir,
                        'jumlah_jual' => $key['qty'],
                        'jumlah_akhir' => $report->jumlah_akhir - $key['qty'],
                        'harga' => $report->harga,
                        'keterangan' => null,
                        'status' => 1,
                        'code_report' => $kdReport,
                        'jenis_laporan' => 'barang'
                    ]);

                }

                $report_back = Report::where('product_id', $key['product_id'])
                    ->orderBy('code_report','asc')
                    ->select('code_report','product_id')
                    ->first();

                $report_banget = Report::where('product_id', $key['product_id'])
                    ->orderBy('code_report','desc')
                    ->first();

                Report::where(['id' => $report_back->id])->update([
                    'jumlah_jual' => $report_banget->jumlah_jual,
                    'jumlah_akhir' => $report_banget->jumlah_akhir
                ]);

            }else{
                //dd('null');

                $product = Product::where('id',$key['product_id'])->first();
                //dd($product);

                if(!$request->nama == null){
                    Report::create([
                        'product_id' => $key['product_id'],
                        'order_id' => $order->id,
                        'tanggal' => $date,
                        'jumlah_awal' => $product->stok + $key['qty'],
                        'jumlah_jual' => $key['qty'],
                        'jumlah_akhir' => $product->stok,
                        'harga' => $product->harga_jual,
                        'keterangan' => null,
                        'code_report' => $kdReport,
                        'jenis_laporan' => 'barang'
                    ]);
                }else{
                    Report::create([
                        'product_id' => $key['product_id'],
                        'order_id' => $order->id,
                        'tanggal' => $date,
                        'jumlah_awal' => $product->stok + $key['qty'],
                        'jumlah_jual' => $key['qty'],
                        'jumlah_akhir' => $product->stok,
                        'harga' => $product->harga_jual,
                        'keterangan' => null,
                        'status' => 1,
                        'code_report' => $kdReport,
                        'jenis_laporan' => 'barang'
                    ]);
                }
            }

        }

        Cart::query()->truncate();

        $dateNow = Carbon::now();
        $total = count($request->Order);
        if ($request->nama == null) {
            $statusPembayaran = 'lunas';
        }else{
            $statusPembayaran = 'belum lunas';
        }

        //dd('tetss');

        Log::create([
            'user_id' => $request->user,
            'activity' => $request->activity,
            'detail_activity' =>  $request->activity.' pesanan dengan jumlah '.$total.' produk dengan status '.$statusPembayaran,
            'tanggal' => $dateNow,
            'order_change' => 1
        ]);

        return redirect()->route('orders.struk');

    }

    public function destroy($id)
    {
        $cart = Cart::find($id)->first();
        $product = Product::where('id', $cart->product_id)->first();

        Product::where('id', $cart->product_id)->update([
            'stok' => $cart->qty + $product->stok
        ]);

        $cart->delete();
        
        $user = Auth::user();
        $role = $user->roles->first()->pivot->role_id;
        $date = Carbon::now();

        Log::create([
            'user_id' => $user,
            'activity' => 'menghapus',
            'detail_activity' => 'menghapus pesanan produk '.$product->name,
            'tanggal' => $date,
            'order_change' => 1
        ]);

        return redirect()->route('orders.create');
    }
}
