<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Coffee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class CoffeesController extends Controller
{

        // Auxiliar para obter a expressão correta da coluna species.
        /* para evitar conflitos entre as colunas 'name' e 'species' quando usado nas query SQL
        'coffees' como prefixo para 'species' --> "coffees.species"  */
    private function getSpeciesColumn(string $tablePrefix = ''): string
    {
        $prefix = $tablePrefix ? "{$tablePrefix}." : '';
        if (Schema::hasColumn('coffees', 'species')) {
            return "{$prefix}species";
        }
        if (Schema::hasColumn('coffees', 'name')) {
            return "{$prefix}name as species";
        }
        return "'' as species";
    }



        // Mostra todos os tipos de café (grãos, não as marcas criadas dentro de cada)
    public function allCoffees()
    {
        $coffeesWDB = $this->loadCoffeesWDB();
        $speciesCol = $this->getSpeciesColumn();

        $getAllCoffees = DB::table('coffees')
            ->select('id', 'photo', DB::raw($speciesCol), 'origin_country', 'roast_level', 'user_id')
            ->get();

        return view('coffees.allcoffees', compact('coffeesWDB', 'getAllCoffees'));
    }

    // BD local de teste
    private function loadCoffeesWDB()
    {
        $coffeesWithoutDB = [
            ['id' => 1, 'species' => 'a', 'origin_country' => 'c', 'roast_level' => 'd'],
            ['id' => 2, 'species' => 'e', 'origin_country' => 'g', 'roast_level' => 'h'],
            ['id' => 3, 'species' => 'i', 'origin_country' => 'k', 'roast_level' => 'l']
        ];

        return $coffeesWithoutDB;
    }



    // Obtem todos os cafés com o user atribuido
    protected function getAllCoffees()
    {
        $speciesCol = $this->getSpeciesColumn('coffees');

        $coffees = Coffee::leftJoin('coffee_users', 'coffees.user_id', '=', 'coffee_users.id')
            ->select('coffees.id', 'coffees.photo', DB::raw($speciesCol), 'coffees.origin_country', 'coffees.roast_level', 'coffees.user_id', 'coffee_users.name as user_name')
            ->get();

        return $coffees;
    }


        /* Mostrar a pagina view/edit do café e as marcas que lhe estão atribuidas.
        Publico-> todos podem ver, mas apenas os admins ou dono podem editar */
    public function editCoffees(int $id)
    {
        $speciesCol = $this->getSpeciesColumn('coffees');

        $coffeeInfo = Coffee::leftJoin('coffee_users', 'coffees.user_id', '=', 'coffee_users.id')
            ->where('coffees.id', $id)
            ->select('coffees.id', 'coffees.photo', DB::raw($speciesCol), 'coffees.origin_country', 'coffees.roast_level', 'coffees.user_id', 'coffee_users.name as user_name')
            ->firstOrFail();

        // Obtem todas as marcas atribuidas ao café
        $brands = Brand::where('coffee_id', $id)->get();

        return view('coffees.editcoffees', compact('coffeeInfo', 'brands'));
    }


    // Atualizar detalhes do café (Admins podem editar qualquer café; user normal apenas podem editar os que escolhem)
    public function updateCoffees(Request $request)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized. Please log in to update coffee.');
        }

        $request->validate([
            'id'             => 'required|integer|exists:coffees,id',
            'species'        => 'required|string|max:50',
            'origin_country' => 'required|string|max:50',
            'roast_level'    => 'required|string|max:50',
            'photo'          => 'nullable|image|max:5000',
        ]);

        $coffee = Coffee::findOrFail($request->id);
        $user = Auth::user();

        // Verificação de autorização: Apenas admins ou proprietário do café
        if (!$user->isAdmin() && (int)$coffee->user_id !== (int)$user->id) {
            abort(403, 'Unauthorized. You can only edit your own coffee.');
        }

        $data = $request->only(['origin_country', 'roast_level']);

        if (Schema::hasColumn('coffees', 'species')) {
            $data['species'] = $request->species;
        } elseif (Schema::hasColumn('coffees', 'name')) {
            $data['name'] = $request->species;
        }

        if ($request->hasFile('photo')) {
            if ($coffee->photo) {
                Storage::disk('public')->delete($coffee->photo); // apaga foto antiga
            }
            $data['photo'] = $request->file('photo')->store('coffees', 'public');
        }

        $coffee->update($data);

        return redirect()->route('coffees.all')->with('message', 'Coffee updated successfully!');
    }



    // Apaga um café e todas as marcas (brands) associadas (apenas admin)
    public function deleteCoffees(int $id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can delete coffees.');
        }

        Brand::where('coffee_id', $id)->delete();
        Coffee::where('id', $id)->delete();

        return back()->with('message', 'Coffee deleted successfully!');
    }



    // Formulario para adicionar novo café (apenas admin)
    public function addCoffees()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can add coffees.');
        }

        $adminName = Auth::user()->name;
        return view('coffees.addcoffees', compact('adminName'));
    }


    // Guarda um novo café na BD (apenas admin)
    public function storeCoffees(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can add coffees.');
        }

        $request->validate([
            'species'        => 'required|string|max:50',
            'origin_country' => 'required|string|max:50',
            'roast_level'    => 'required|string|max:50',
            'photo'          => 'nullable|image|max:5000',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('coffees', 'public');
        }

        // Usa sempre DB::table insert para ultrapassar problemas de preenchimento/nomes de colunas
        $insertData = [
            'origin_country' => $request->origin_country,
            'roast_level'    => $request->roast_level,
            'user_id'        => Auth::id(),
            'photo'          => $photoPath,
            'created_at'     => now(),
            'updated_at'     => now(),
        ];

        if (Schema::hasColumn('coffees', 'species')) {
            $insertData['species'] = $request->species;
        } else {
            $insertData['name'] = $request->species;
        }

        DB::table('coffees')->insert($insertData);

        return redirect()->route('coffees.all')->with('message', 'Coffee successfully added!');
    }
}
