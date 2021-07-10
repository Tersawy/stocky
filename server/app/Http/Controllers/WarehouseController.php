<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::all();

        return $this->success($warehouses);
    }


    public function options()
    {
        $warehouses = Warehouse::all(['id AS value', 'name AS text']);

        return $this->success($warehouses);
    }


    public function show(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $warehouse = Warehouse::find($req->id);

        if (!$warehouse) return $this->error('The warehouse was not found', 404);

        return $this->success($warehouse);
    }


    public function create(Request $req)
    {
        $attr = $req->validate([
            'name'      => ['required', 'string', 'max:255', 'unique:warehouses'],
            'phone'     => ['required', 'string', 'max:255'],
            'country'   => ['required', 'string', 'max:255'],
            'city'      => ['required', 'string', 'max:255'],
            'zip_code'  => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email:rfc,dns', 'max:255'],
        ]);

        $warehouse = Warehouse::create($attr);

        return $this->success($warehouse, 'The warehouse has been created successfully');
    }


    public function update(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate([
            'id'        => ['required', 'numeric', 'min:1'],
            'name'      => ['required', 'string', 'max:255', 'unique:warehouses,name,' . $req->id],
            'phone'     => ['required', 'string', 'max:255'],
            'country'   => ['required', 'string', 'max:255'],
            'city'      => ['required', 'string', 'max:255'],
            'zip_code'  => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email:rfc,dns', 'max:255'],
        ]);

        $inputs = $req->only(['name', 'phone', 'country', 'city', 'zip_code', 'email']);

        $warehouse = Warehouse::find($req->id);

        if (!$warehouse) return $this->error('The warehouse was not found', 404);

        $warehouse->fill($inputs);

        $warehouse->save();

        return $this->success($warehouse, 'The warehouse has been updated successfully');
    }


    public function moveToTrash(Request $req, $id)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $warehouse = Warehouse::find($req->id);

        if (!$warehouse) return $this->error('The warehouse was not found', 404);

        $warehouse->delete();

        return $this->success($req->id, 'The warehouse has been moved to the trash successfully');
    }


    public function trashed()
    {
        $warehouses = Warehouse::onlyTrashed()->get();

        return $this->success($warehouses);
    }


    public function restore(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $isDone = Warehouse::onlyTrashed()->where('id', $req->id)->restore();

        if (!$isDone) return $this->error('The warehouse is not in the trash', 404);

        return $this->success($req->id, 'The warehouse has been restored successfully');
    }


    public function remove(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $isDone = Warehouse::onlyTrashed()->where('id', $req->id)->forceDelete();

        if (!$isDone) return $this->error('The warehouse is not in the trash', 404);

        return $this->success($req->id, 'The warehouse has been deleted successfully');
    }
}