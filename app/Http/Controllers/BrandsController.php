<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandsController extends Controller
{
    //  Armazenar uma nova marca dentro do tipo de café escolhido (apenas funciona com admins)
    public function store(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can add brands.');
        }

        $request->validate([
            'coffee_id' => 'required|integer|exists:coffees,id',
            'name'      => 'required|string|max:100',
            'batch'     => 'required|string|max:100',
            'price'     => 'required|numeric|min:0',
            'caffeine'  => 'required|string|max:50',
        ]);

        Brand::create([
            'coffee_id' => $request->coffee_id,
            'name'      => $request->name,
            'batch'     => $request->batch,
            'price'     => $request->price,
            'caffeine'  => $request->caffeine,
        ]);

        return back()->with('message', 'Brand added successfully!');
    }



    // Atualizar uma marca (brand) existente (apenas admins)
    public function update(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can edit brands.');
        }

        $request->validate([
            'id'       => 'required|integer|exists:brands,id',
            'name'     => 'required|string|max:100',
            'batch'    => 'required|string|max:100',
            'price'    => 'required|numeric|min:0',
            'caffeine' => 'required|string|max:50',
        ]);

        $brand = Brand::findOrFail($request->id);

        $brand->update([
            'name'     => $request->name,
            'batch'    => $request->batch,
            'price'    => $request->price,
            'caffeine' => $request->caffeine,
        ]);

        return back()->with('message', 'Brand updated successfully!');
    }


        // Eliminar uma marca (brand) existente
    public function delete(int $id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can delete brands.');
        }

        Brand::where('id', $id)->delete();

        return back()->with('message', 'Brand deleted successfully!');
    }
}
