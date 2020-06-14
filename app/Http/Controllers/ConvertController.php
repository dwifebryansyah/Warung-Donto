<?php

namespace App\Http\Controllers;

use App\Convert;
use App\Product;
use App\Unit;
use App\Category;
use App\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class ConvertController extends Controller
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
                    ]);
                    Session::put('user', $data);
                }

                return $next($request);
            }
        });
    }

    public function index()
    {
        $converts = Convert::with('Product.Unit.Category', 'Product.InformationUnit')->latest()->paginate(10);

        $carts = Cart::with('Product')->paginate(15);
        $harga = [];
        foreach ($carts as $c) {
            $harga[] = $c->qty * $c->product->harga_jual;
        }

        $subtotal = array_sum($harga);

        $count = $carts->count();

        return view('admin.converts.index')->with(compact('converts','carts','count','subtotal'));
    }

    public function create()
    {
        $products = Product::all();
        $units = Unit::all();
        $categories = Category::all();

        return view('admin.converts.add')->with(compact('products','units','categories'));
    }

    public function store(Request $request)
    {

        //dd($request->all());

        $product = Product::with('InformationUnit')->where('id', $request->product_id)->first(); // stok 10 bal

        $unit = Unit::where('id', $request->convert_awal)->first();
        //dd($product);
        $stok = 0;
        $stok = $request->stok * $product->informationUnit->jumlah_akhir;

        // if($unit->tingkat == 1){

        //     if($product->informationUnit->jumlah_akhir == 10){
        //         $satuan_sebelumnya = 10;

        //         $stok = $request->stok * $satuan_sebelumnya;
        //     }elseif($product->informationUnit->jumlah_akhir == 20){
        //         $satuan_sebelumnya = 20;

        //         $stok = $request->stok * $satuan_sebelumnya;
        //     }

        // }

        //dd($stok);

        $satuan_akhir = Unit::where('id', $request->convert_akhir)->first();

        $product_convert = Product::where('unit_id',$satuan_akhir->id)->first(); // stok 20 pack

        if($product_convert == null){
            return redirect()->route('converts.index');
        }

        Product::where(['id' => $product_convert->id])->update([
            'stok' => $product_convert->stok + $stok,
        ]);

        Product::where(['id' => $product->id])->update([
            'stok' => $product->stok - $request->stok,
        ]);

        //dd($product_convert);


        Convert::create([
            'product_id' => $request->product_id,
            'category_id' => $request->category_id,
            'convert_awal' => $unit->name,
            'convert_akhir' => $satuan_akhir->name,
            'stok' => $request->stok
        ]);

        return redirect()->route('converts.index');
    }

    public function edit(Convert $convert)
    {
        //
    }

    public function update(Request $request, Convert $convert)
    {
        //
    }

    public function destroy(Convert $convert)
    {
        $convert->delete();

        return redirect()->route('converts.index');
    }
}
