<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use DataTables;

use App\Models\User;
use Spatie\Permission\Models\Role;
class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if ($request->id) {
                $data = User::with('roles')->find($request->id);
                return response()->json(['result' => $data]);
            };
            $data = User::with('roles')->has("roles")->orderBy('id','desc')->get();
            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $assign = '<button type="button" class="btn btn-success btn-sm btn-icon-text mr-3" id="btn_assign" data-id="'.$row->id.'">Assign Role <i class="mdi mdi-lead-pencil" style="color:white"></i></button>';
                return $assign;
            })
            ->addColumn('created_at',function($row){
                return $row->created_at->format('d-m-Y H:i:s');
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        $data['role'] = Role::select('name')->orderBy('id','desc')->get();
        return view('user',$data);
    }

    public function store(Request $request)
    {
        if ($request->ajax()) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:8|confirmed',
                    'role' => 'required',
                ],
            );

            $attribute = [
                'name' => 'Nama',
                'email' => 'Email',
                'password' => 'Password',
                'role' => 'Role'
            ];
            $validator->setAttributeNames($attribute);

            if (!$validator->passes()) {
                return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
            } else {
                $data = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)
                ]);

                $user = User::find($data->id);
                $user->syncRoles($request->role);

                return response()->json(['code' => 1]);
            } 
        }
    }

    public function assign(Request $request)
    {
        if ($request->ajax()) { 
            $validator = \Validator::make(
                $request->all(),
                [
                    'email' => 'required|max:255|email',
                ],
            );

            $attribute = [
                'email' => 'Email',
                'role' => 'Role'
            ];
            $validator->setAttributeNames($attribute);

            if (!$validator->passes()) {
                return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
            } else {
                $user = User::where('email','=',$request->email)->first();
                if ($user == NULL) {
                    return response()->json(['code' => 2, 'error' => 'Email tidak terdaftar']);
                } else {
                    $user->syncRoles($request->role);
                    return response()->json(['code' => 1]);
                }
            } 
        }
    }
}
