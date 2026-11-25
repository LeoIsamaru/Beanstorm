<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Coffee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{


    /* Mostra todos os users e os seus café selecionados (preferido e o que não gosta)
    Acessivel para users autenticados */
    public function allUsers()
    {
        $usersWDB = $this->loadUsersWDB();

        $searchPar = request()->query('search');

        // Confirmar que a coluna renomeada existe, senão vai para o coluna com o nome original (causado pela alteração de colunas atribuidas ao user, que antes eram "da" tabela coffee e passaram a ser da tabela brands)
        $favCol = Schema::hasColumn('coffee_users', 'favorite_brand_id') ? 'favorite_brand_id' : 'favorite_coffee_id';
        $disCol = Schema::hasColumn('coffee_users', 'disliked_brand_id') ? 'disliked_brand_id' : 'disliked_coffee_id';

        $query = User::leftJoin('brands as fav_brand', "coffee_users.{$favCol}", '=', 'fav_brand.id')
            ->leftJoin('brands as dis_brand', "coffee_users.{$disCol}", '=', 'dis_brand.id')
            ->select(
                'coffee_users.*',
                DB::raw("CONCAT(fav_brand.name, ' - ', fav_brand.batch) as favorite_brand_display"),
                DB::raw("CONCAT(dis_brand.name, ' - ', dis_brand.batch) as disliked_brand_display")
            );

        if ($searchPar) {
            $query->where(function ($q) use ($searchPar) {
                $q->where('coffee_users.name', 'LIKE', "%{$searchPar}%")
                  ->orWhere('coffee_users.email', 'LIKE', "%{$searchPar}%");
            });
        }

        $usersFromDB = $query->get();

        return view('users.alluserscoffee', compact('usersWDB', 'usersFromDB'));
    }


    // mostra o formulario para adicionar/registar um novo user
    public function addUsers()
    {
        $usersWDB = $this->loadUsersWDB();
        return view('users.adduserscoffee', compact('usersWDB'));
    }



    // Elimina um user e as suas escolhas de café e marcas
    public function deleteUsers(int $id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can delete users.');
        }

        // Elimina as marcas pertencentes aos cafés esolhidos pelo user
        $coffeeIds = Coffee::where('user_id', $id)->pluck('id');
        Brand::whereIn('coffee_id', $coffeeIds)->delete();
        Coffee::where('user_id', $id)->delete();

        // Elimina o user
        User::where('id', $id)->delete();

        return back()->with('message', 'User deleted successfully!');
    }



    /* Mostra o formulario para editar o user
    (admin pode editar qualquer user; normal user apenas pode editar o seu perfil)*/
    public function editUsers(int $id)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized. Please log in.');
        }

        // normal user apenas pode editar o seu perfil)
        if (!Auth::user()->isAdmin() && (int)$id !== (int)Auth::id()) {
            abort(403, 'Unauthorized. You can only edit your own preferences.');
        }

        $userInfo = User::where('id', $id)->firstOrFail();

        // Obtem todas as marcas com info do café, para a opção escolhida
        $brands = Brand::with('coffee')->get();

        return view('users.editusercoffee', compact('userInfo', 'brands'));
    }

    /**
     * Store a newly created user.
     * (Admin can assign user_type via dropdown; default is Normal User)
     */

    /* Guarda um novo user criado
        (apenas admin pode atribuir um user_type através de dropdown; escolha predefinida é user normal)*/
    public function storeUser(Request $request)
    {
        $validationRules = [
            'name'     => 'required|string|max:50',
            'email'    => 'required|email|unique:coffee_users,email',
            'password' => 'required|min:8',
        ];

        $userType = User::TYPE_GENERIC_USER; // escolha predefinida é user normal

        // apenas admin pode atribuir um user_type
        if (Auth::check() && Auth::user()->isAdmin()) {
            $validationRules['user_type'] = 'nullable|integer|in:' . User::TYPE_ADMIN . ',' . User::TYPE_GENERIC_USER;
            if ($request->filled('user_type')) {
                $userType = (int) $request->user_type;
            }
        }

        $request->validate($validationRules);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'user_type' => $userType,
        ]);

        // Se foi criado por um Admin, volta para a lista de users
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('users.all')->with('message', 'User successfully created!');
        }

        // Novo user normal é redirecionado para a pagina de login
        return redirect()->route('login')->with('message', 'Account created successfully! Please log in with your credentials.');
    }




    // Valores teste fora da BD
    private function loadUsersWDB()
    {
        $usersWithoutDB = [
            ['id' => 1, 'name' => 'Sara', 'email' => 'sara@gmail.com'],
            ['id' => 2, 'name' => 'Bruno', 'email' => 'Bruno@gmail.com'],
            ['id' => 3, 'name' => 'Marco', 'email' => 'Marco@gmail.com'],
            ['id' => 4, 'name' => 'António', 'email' => 'António@gmail.com']
        ];

        return $usersWithoutDB;
    }


    /* Atualizar detalhes do user
        (admin pode atualizar qualquer user; normal user apenas pode atualizar o seu perfil)*/
    public function updateuser(Request $request)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized. Please log in.');
        }

        // normal user apenas pode atualizar o seu perfil
        if (!Auth::user()->isAdmin() && (int)$request->id !== (int)Auth::id()) {
            abort(403, 'Unauthorized. You can only update your own preferences.');
        }

        $request->validate([
            'id'                => 'required|integer|exists:coffee_users,id',
            'name'              => 'required|string|max:50',
            'favorite_brand_id' => 'nullable|integer|exists:brands,id',
            'disliked_brand_id' => 'nullable|integer|exists:brands,id',
        ]);

        if ($request->hasFile('photo')) {
            Storage::putFile('userPhotos', $request->photo);
        }

        $updateData = [
            'name' => $request->name,
        ];

        // Admin pode atualizar o user_type
        if (Auth::user()->isAdmin() && $request->filled('user_type')) {
            $updateData['user_type'] = (int) $request->user_type;
        }

        // Guarda em favorite_brand_id ou coluna de reserva
        if (Schema::hasColumn('coffee_users', 'favorite_brand_id')) {
            $updateData['favorite_brand_id'] = $request->favorite_brand_id;
            $updateData['disliked_brand_id'] = $request->disliked_brand_id;
        } else {
            $updateData['favorite_coffee_id'] = $request->favorite_brand_id;
            $updateData['disliked_coffee_id'] = $request->disliked_brand_id;
        }

        User::where('id', $request->id)->update($updateData);

        // return redirect()->route('users.all')->with('message', 'Profile successfully updated!'); mensagem geral que não dizia qual o perfil atualizado

    return redirect()->route('users.all')->with('message', "{$request->name}'s Profile successfully updated!");}
}
