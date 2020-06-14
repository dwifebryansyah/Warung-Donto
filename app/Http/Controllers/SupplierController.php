<?php

namespace App\Http\Controllers;

use App\Supplier;
use App\Product;
use App\Report;
use App\Cart;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Session;

class SupplierController extends Controller
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
        $suppliers = Supplier::with('Product.Unit.Category')->latest()->paginate(25);

        $carts = Cart::with('Product')->paginate(15);
        $harga = [];
        foreach ($carts as $c) {
            $harga[] = $c->qty * $c->product->harga_jual;
        }

        $subtotal = array_sum($harga);

        $count = $carts->count();

        return view('admin.suppliers.index')->with(compact('suppliers','carts','count','subtotal'));
    }

    public function pasok(Request $request)
    {
        $pasok = [];

        foreach ($request->pasok as $val) {

            // $product = Product::with('Unit.Category')->find($val);
            $product = Supplier::with('Product.Unit.Category')->find($val);
            //dd($product);
            $pasok[] = [
                'id' => $product->product->id,
                'harga' => $product->harga_beli,
                'name' => $product->product->name,
                'stok' => $product->product->stok,
                'unit' => $product->product->unit->name,
                'category' => $product->product->unit->category->name
            ];

        }


        return view('admin.suppliers.pasok', compact('pasok'));
    }

    public function store(Request $request)
    {
        //dd($request->all());

        $date = Carbon::now()->format('Y-m-d');

        foreach ($request->pasok as $val) {

            // CODE Report
            $kdReport = Report::select(['code_report'])->max('code_report');
            $noUrut = (int) substr($kdReport, 5, 3);
            $noUrut++;
            $char = "RP";
            $kdReport = $char . sprintf("%05s", $noUrut);

            $report = Report::where([
                ['product_id', '=' , $val['id'] ],
                ['bm_jumlah', '<>', null]
            ])
            ->latest()
            ->first();

            // dd($report);

            if ($report) {

                Report::create([
                    'product_id' => $val['id'],
                    'order_id' => null,
                    'tanggal' => $date,
                    'jumlah_awal' => $report->jumlah_akhir,
                    'bm_jumlah' => $val['qty'],
                    'jumlah_akhir' => $report->jumlah_akhir + ($val['qty']),
                    'harga' => $report->harga,
                    'keterangan' => null,
                    'status' => 1,
                    'code_report' => $kdReport,
                    'jenis_laporan' => 'pasok'
                    ]);

                $report_back = Report::where('product_id', $val['id'])
                    ->orderBy('code_report','asc')
                    ->select('code_report','product_id')
                    ->first();

                $report_banget = Report::where('product_id', $val['id'])
                    ->orderBy('code_report','desc')
                    ->first();

                Report::where(['id' => $report_back->id])->update([
                    'bm_jumlah' => $report_banget->bm_jumlah,
                    'jumlah_akhir' => $report_banget->jumlah_akhir
                ]);

            }else{
                //dd('test');
                $product = Product::where('id',$val['id'])->first();

                Report::create([
                    'product_id' => $val['id'],
                    'order_id' => null,
                    'tanggal' => $date,
                    'jumlah_awal' => $product->stok,
                    'bm_jumlah' => $val['qty'],
                    'jumlah_akhir' => $product->stok + $val['qty'],
                    'harga' => $product->harga_jual,
                    'keterangan' => null,
                    'status' => 1,
                    'code_report' => $kdReport,
                    'jenis_laporan' => 'pasok'
                ]);

            }

            //BATAS

            $product = Product::with('Unit.Category')->find($val['id']);
            Product::where(['id' => $val['id']])->update([
                'stok' => $product->stok + $val['qty']
            ]);

            Supplier::where(['product_id' => $val['id']])->update([
                'harga_beli' => $val['harga_beli']
            ]);

        }

        $dateNow = Carbon::now();
        $total = count($request->pasok);

        Log::create([
            'user_id' => $request->user,
            'activity' => $request->activity,
            'detail_activity' => 'memasok '.$total.' Jumlah Produk',
            'tanggal' => $dateNow,
            'supplier_change' => 1
        ]);


        return redirect()->route('suppliers.index');
    }

    public function updatePasok(Request $request, $id)
    {

        $supplier = Supplier::where('id', $id)->first();
        $product = Product::where('id', $supplier->product_id)->first();

        Product::where(['id' => $supplier->product_id])->update([
            'stok' => $product->stok + $request->jumlah
        ]);


        return redirect()->route('suppliers.index');
    }


    public function edit(Supplier $supplier)
    {
        $suppliers = Supplier::with('Product.Unit.Category')
            ->where('id', $supplier->id)
            ->first();

        return view('admin.suppliers.edit')->with(compact('suppliers'));
    }


    public function update(Request $request, Supplier $supplier)
    {
        Supplier::where(['id' => $supplier->id])->update([
            'harga_beli' => $request->harga_beli
        ]);


        return redirect()->route('suppliers.index');
    }


    public function destroy(Supplier $supplier)
    {
        //
    }
}
