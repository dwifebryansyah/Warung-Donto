<?php

namespace App\Http\Controllers;

use App\Unit;
use Illuminate\Http\Request;
use App\Category;
use App\Cart;
use Carbon\Traits\Units;
use Illuminate\Support\Facades\Auth;
use Session;

class UnitController extends Controller
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
        $units = Unit::with('Category')->latest()->paginate(25);

        $carts = Cart::with('Product')->paginate(15);
        $harga = [];
        foreach ($carts as $c) {
            $harga[] = $c->qty * $c->product->harga_jual;
        }

        $subtotal = array_sum($harga);

        $count = $carts->count();

        return view('admin.units.index')->with(compact('units','carts','count','subtotal'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.units.add')->with(compact('categories'));
    }

    public function store(Request $request)
    {
        $kdUnit = Unit::select(['code_unit'])->max('code_unit');

        $noUrut = (int) substr($kdUnit, 5, 3);

        $noUrut++;
        $char = "CK";
        $kdUnit = $char . sprintf("%05s", $noUrut);

        $tingkat = Unit::where('category_id', (int)$request->category_id)
            ->select('tingkat')
            ->max('tingkat');

        $tingkat++;
        // dd($tingkat);

        Unit::create([
            'name' => $request['name'],
            'category_id' => $request['category_id'],
            'code_unit' => $kdUnit,
            'tingkat' => $tingkat,
        ]);

        return redirect()->route('units.index');
    }

    public function edit(Unit $unit)
    {

        $categories = Category::all();
        return view('admin.units.edit', compact('unit','categories'));
    }

    public function update(Request $request, Unit $unit)
    {
        Unit::where(['id' => $unit->id])->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
        ]);


        return redirect()->route('units.index');
    }
    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route('units.index');
    }
}
