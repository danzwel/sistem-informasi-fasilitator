<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Pengajuan;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    public function index() { return response()->json(Pengajuan::with(['fasilitator','kegiatan','reviewer'])->latest()->paginate(20)); }
    public function store(Request $request)
    {
        $data=$request->validate(['fasilitator_id'=>'required|exists:fasilitators,id','kegiatan_id'=>'nullable|exists:kegiatans,id','nama_kegiatan'=>'required|string|max:255','materi'=>'nullable|string|max:255','tanggal'=>'nullable|date','penyelenggara_id'=>'nullable|exists:penyelenggaras,id','lokasi'=>'nullable|string|max:255','peran'=>'nullable|string|max:255']);
        $pengajuan=Pengajuan::create($data+['status'=>'pending','submitted_at'=>now()]);
        ActivityLog::create(['user_id'=>$request->user()->id,'action'=>'create','description'=>'Pengajuan dibuat.','metadata'=>['pengajuan_id'=>$pengajuan->id]]);
        return response()->json($pengajuan,201);
    }
    public function review(Request $request, Pengajuan $pengajuan)
    {
        $data=$request->validate(['status'=>'required|in:approved,rejected','catatan_admin'=>'nullable|string']);
        $pengajuan->update($data+['reviewed_by'=>$request->user()->id,'reviewed_at'=>now()]);
        ActivityLog::create(['user_id'=>$request->user()->id,'action'=>$data['status']==='approved'?'approve':'reject','description'=>'Pengajuan ditinjau.','metadata'=>['pengajuan_id'=>$pengajuan->id]]);
        return response()->json($pengajuan->fresh());
    }
}
