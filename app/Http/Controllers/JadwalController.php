<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use DataTables;

use App\Models\JadwalModel;

class JadwalController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            if ($request->id) {
                $id = $request->id;
                Cache::rememberForever('data_jadwal_'.$id.'', function () use($id) {
                    return JadwalModel::find($id);
                });
                return response()->json(['result' => Cache::get('data_jadwal_'.$id.'')]);
            };
            Cache::rememberForever('jadwal', function () {
                return JadwalModel::orderBy('id','asc')->get();
            });
            return Datatables::of(Cache::get('jadwal'))
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '
                    <a href="javascript: void(0);" class="action-icon btn_edit" id="btn_edit" data-id="'.$row->id.'" style="color:green"><i class="mdi mdi-lead-pencil"></i></a>
                    <a href="javascript: void(0);" class="action-icon btn_delete" id="btn_delete" data-id="'.$row->id.'" style="color:red"> <i class="mdi mdi-delete"></i></a>';
                    return $actionBtn;
                })
                ->addColumn('status', function($row){
                    if ($row->status == "Aktif") {
                        $status = '<span class="badge bg-success" style="color:white">'.$row->status.'</span>';
                    } else {
                        $status = '<span class="badge bg-danger" style="color:white">'.$row->status.'</span>';
                    }
                    return $status;
                })
                ->addColumn('created_at',function($row){
                    return $row->created_at->format('d-m-Y H:i:s');
                })
                ->addColumn('updated_at',function($row){
                    return $row->updated_at->format('d-m-Y H:i:s');
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }
        return view('jadwal');
    }
    
    public function store(Request $request) {
        if ($request->ajax()) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'tanggal' => 'required',
                ],
            );

            $attribute = [
                'tanggal' => 'Tanggal',
            ];
            $validator->setAttributeNames($attribute);

            if (!$validator->passes()) {
                return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
            } else {
                if ($request->status) {
                    $status = "Aktif";
                } else {
                    $status = "Tidak Aktif";
                }
                
                $data = JadwalModel::create([
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->keterangan,
                    'status' => $status,
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
                    'tanggal' => 'required',
                ],
            );

            $attribute = [
                'tanggal' => 'Tanggal',
            ];
            $validator->setAttributeNames($attribute);

            if (!$validator->passes()) {
                return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
            } else {
                $data = JadwalModel::find($request->id);
                if ($request->status) {
                    $status = "Aktif";
                } else {
                    $status = "Tidak Aktif";
                }

                $data->update([
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->keterangan,
                    'status' => $status,
                ]);

                Cache::forget('data_jadwal_'.$request->id);
                Cache::forget('jadwal');
                return response()->json(['code' => 1]);
            } 
        }
    }

    public function delete(Request $request){
        if ($request->ajax()) {
            $data = JadwalModel::find($request->id);
            $data->delete();

            Cache::forget('data_jadwal_'.$request->id);
            Cache::forget('jadwal');
            return response()->json(['code' => 1]);
        }
    }
}
