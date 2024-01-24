<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use DataTables;
use Auth;

use App\Models\AktivitasModel;
use App\Models\JadwalModel;
use App\Models\User;

class AktivitasController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            if ($request->id) {
                $id = $request->id;
                Cache::rememberForever('data_aktivitas_'.$id.'', function () use($id) {
                    return AktivitasModel::with('jadwal','user')->find($id);
                });
                return response()->json(['result' => Cache::get('data_aktivitas_'.$id.'')]);
            };
            if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Atasan')) {
                $data = AktivitasModel::with('jadwal','user')->orderBy('id','asc')->get();
            } else {
                $data = AktivitasModel::where('id_user',Auth::user()->id)->with('jadwal','user')->orderBy('id','asc')->get();
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '
                    <a href="javascript: void(0);" class="action-icon btn_edit" id="btn_edit" data-id="'.$row->id.'" style="color:green"><i class="mdi mdi-lead-pencil"></i></a>
                    <a href="javascript: void(0);" class="action-icon btn_delete" id="btn_delete" data-id="'.$row->id.'" style="color:red"> <i class="mdi mdi-delete"></i></a>';
                    return $actionBtn;
                })
                ->addColumn('file', function($row){
                    $actionBtn = '
                    <a href="/assets/files/'.$row->file.'" target="_blank" class="action-icon" style="color:green"><i class="mdi mdi-download"></i></a>';
                    return $actionBtn;
                })
                ->addColumn('status', function($row){
                    if ($row->status == "Proses") {
                        $status = '<span class="badge bg-warning" style="color:white">'.$row->status.'</span>';
                    } else {
                        $status = '<span class="badge bg-success" style="color:white">'.$row->status.'</span>';
                    }
                    return $status;
                })
                ->addColumn('created_at',function($row){
                    return $row->created_at->format('d-m-Y H:i:s');
                })
                ->addColumn('updated_at',function($row){
                    return $row->updated_at->format('d-m-Y H:i:s');
                })
                ->rawColumns(['action','status','file'])
                ->make(true);
        }
        $data['jadwal'] = JadwalModel::select('id','tanggal')->get();
        return view('aktivitas',$data);
    }
    
    public function store(Request $request) {
        if ($request->ajax()) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'id_jadwal' => 'required',
                    'aktivitas' => 'required|max:255',
                    'file' => 'required|mimes:jpg,jpeg,png,pdf|max:1024',
                ],
            );

            $attribute = [
                'id_jadwal' => 'Jadwal',
                'aktivitas' => 'Aktivitas',
                'file' => 'File',
            ];
            $validator->setAttributeNames($attribute);

            if (!$validator->passes()) {
                return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
            } else {
                $file = $request->file('file');
                if($request->Hasfile('file')){
                    $filename = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $name= uniqid().'.'.$extension;
                    $path =  public_path('/assets/files/' . $name);
                    $file->move('assets/files/',$name);  
                }
                
                $data = AktivitasModel::create([
                    'id_jadwal' => $request->id_jadwal,
                    'id_user' => Auth::user()->id,
                    'aktivitas' => $request->aktivitas,
                    'keterangan' => $request->keterangan,
                    'file' => $name,
                    'status' => "Proses",
                ]);    

                Cache::forget('jadwal');
                return response()->json(['code' => 1]);
            } 
        }
    }

    public function update(Request $request){
        if ($request->ajax()) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'id_jadwal' => 'required',
                    'aktivitas' => 'required|max:255',
                    'file' => 'mimes:jpg,jpeg,png,pdf|max:1024',
                ],
            );

            $attribute = [
                'id_jadwal' => 'Jadwal',
                'aktivitas' => 'Aktivitas',
                'file' => 'File',
            ];
            $validator->setAttributeNames($attribute);

            if (!$validator->passes()) {
                return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
            } else {
                $data = AktivitasModel::find($request->id);
                $file = $request->file('file');

                if ($request->status) {
                    $status = "Done";
                } else {
                    $status = "Proses";
                }

                $data->update([
                    'id_jadwal' => $request->id_jadwal,
                    'id_user' => Auth::user()->id,
                    'aktivitas' => $request->aktivitas,
                    'keterangan' => $request->keterangan,
                    'status' => $status,
                ]);

                if ($file) {
                    @unlink('assets/files/'. $data->file);
                    if($request->Hasfile('file')){
                        $filename = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                        $name= uniqid().'.'.$extension;
                        $path =  public_path('/assets/files/' . $name);
                        $file->move('assets/files/',$name);  
                    }
                    $data->update([
                        'file' => $name,
                    ]);
                }

                Cache::forget('data_aktivitas_'.$request->id);
                Cache::forget('jadwal');
                return response()->json(['code' => 1]);
            } 
        }
    }

    public function delete(Request $request){
        if ($request->ajax()) {
            $data = AktivitasModel::find($request->id);
            @unlink('assets/files/'. $data->file);
            $data->delete();

            Cache::forget('data_aktivitas_'.$request->id);
            Cache::forget('jadwal');
            return response()->json(['code' => 1]);
        }
    }
}
