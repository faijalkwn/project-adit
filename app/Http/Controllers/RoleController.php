<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use DataTables;

use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            if ($request->id) {
                $data = Role::find($request->id);
                return response()->json(['result' => $data]);
            };
            $data = Role::orderBy('id','asc')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '
                    <a href="javascript: void(0);" class="action-icon btn_edit" id="btn_edit" data-id="'.$row->id.'" style="color:green"><i class="mdi mdi-lead-pencil"></i></a>
                    <a href="javascript: void(0);" class="action-icon btn_delete" id="btn_delete" data-id="'.$row->id.'" style="color:red"> <i class="mdi mdi-delete"></i></a>';
                    return $actionBtn;
                })
                ->addColumn('created_at',function($row){
                    return $row->created_at->format('d-m-Y H:i:s');
                })
                ->addColumn('updated_at',function($row){
                    return $row->updated_at->format('d-m-Y H:i:s');
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view ('role');
    }

    public function store(Request $request)
    {
        if ($request->ajax()) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:255|unique:roles',
                ],
            );

            $attribute = [
                'name' => 'Role',
            ];
            $validator->setAttributeNames($attribute);

            if (!$validator->passes()) {
                return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
            } else {
                $data = Role::create([
                    'name' => $request->name,
                ]);

                return response()->json(['code' => 1]);
            } 
        }
    }

    public function update(Request $request)
    {
        if ($request->ajax()) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:255',
                ],
            );

            $attribute = [
                'name' => 'Role',
            ];
            $validator->setAttributeNames($attribute);

            if (!$validator->passes()) {
                return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
            } else {
                $role = Role::find($request->id);
                $role->update([
                    'name' => $request->name,
                ]);

                return response()->json(['code' => 1]);
            } 
        }
    }

    public function delete(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::find($request->id);
            $data->delete();
            return response()->json(['code' => 1]);
        }
    }
}
