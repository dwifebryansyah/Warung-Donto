<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use App\Unit;
use App\Cart;
use App\Supplier;
use App\InformationUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class ProductController extends Controller
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
        $products = Product::with('Unit.Category')->latest()->paginate(25);

        $carts = Cart::with('Product')->paginate(15);

        $harga = [];

        foreach ($carts as $c) {
            $harga[] = $c->qty * $c->product->harga_jual;
        }

        $subtotal = array_sum($harga);

        $count = $carts->count();

        return view('admin.products.index')->with(compact('products','carts','count','subtotal'));
    }

    public function create()
    {
        $products = Product::all();
        $units = Unit::all();
        $categories = Category::all();
        $informations = InformationUnit::all();

        return view('admin.products.add')->with(compact('products','units','categories','informations'));
    }


    public function store(Request $request)
    {

        $kdBarang = Product::select(['code_item'])->max('code_item');

        $noUrut = (int) substr($kdBarang, 5, 3);

        $noUrut++;
        $char = "BR";
        $kdBarang = $char . sprintf("%05s", $noUrut);

        //dd($request->all());

        if($request->information_unit_id == null){

            Product::create([
                'name' => $request['name'],
                'category_id' => $request['category_id'],
                'unit_id' => $request['unit_id'],
                'harga_jual' => $request['harga_jual'],
                'stok' => $request['stok'],
                'code_item' => $kdBarang,
                'information_unit_id' => null,
            ]);

        }else{

            Product::create([
                'name' => $request['name'],
                'category_id' => $request['category_id'],
                'unit_id' => $request['unit_id'],
                'harga_jual' => $request['harga_jual'],
                'stok' => $request['stok'],
                'code_item' => $kdBarang,
                'information_unit_id' => $request->information_unit_id,
            ]);

        }

        $getProduct = Product::orderBy('id', 'desc')->first();

        //dd($getProduct);

        Supplier::create([
            'product_id' => $getProduct->id,
            'harga_beli' => $request['harga_beli'],
        ]);

        return redirect()->route('products.index');
    }

    public function edit(Product $product)
    {
        $units = Unit::all();
        $categories = Category::all();

        return view('admin.products.edit', compact('units','categories','product'));
    }

    public function update(Request $request, Product $product)
    {
        // dd( $request->category_id );

        Product::where(['id' => $product->id])->update([
            'name' => $request['name'],
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'harga_jual' => $request['harga_jual'],
            'stok' => $request['stok'],
            'code_item' => $request['code_item'],
        ]);


        return redirect()->route('products.index');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index');
    }
}
