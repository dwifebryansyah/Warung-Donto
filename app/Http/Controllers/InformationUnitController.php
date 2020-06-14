<?php

namespace App\Http\Controllers;

use App\InformationUnit;
use App\Unit;
use App\Category;
use App\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class InformationUnitController extends Controller
{

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $role = $user->roles->first()->pivot->role_id;

            if ($role == 2) {
                return redirect('home/');
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
        $informations = InformationUnit::with(['Category','UnitOne','UnitTwo'])
            ->latest()
            ->paginate(25);

        $carts = Cart::with('Product')->paginate(15);
        $harga = [];
        foreach ($carts as $c) {
            $harga[] = $c->qty * $c->product->harga_jual;
        }

        $subtotal = array_sum($harga);

        $count = $carts->count();

        return view('admin.informations.index')->with(compact('informations','carts','count','subtotal'));
    }


    public function create()
    {
        $units = Unit::all();
        $categories = Category::all();
        return view('admin.informations.add')->with(compact('units','categories'));
    }

    public function store(Request $request)
    {

        InformationUnit::create([
            'category_id' => $request->category_id,
            'jumlah_awal' => $request->jumlah_awal,
            'satuan_awal_id' => $request->satuan_awal_id,
            'jumlah_akhir' => $request->jumlah_akhir,
            'satuan_akhir_id' => $request->satuan_akhir_id,
        ]);

        return redirect('/admin/informations');
    }


    public function edit(InformationUnit $informationUnit)
    {
        //
    }


    public function update(Request $request, InformationUnit $informationUnit)
    {
        //
    }

    public function destroy(InformationUnit $informationUnit)
    {
        //
    }
}
