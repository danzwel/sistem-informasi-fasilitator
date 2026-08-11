<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Kegiatan;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index()
    {
        return response()->json(Kegiatan::with(['penyelenggara', 'fasilitators'])->latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nama'=>'required|string|max:255','deskripsi'=>'nullable|string','tanggal_mulai'=>'required|date','tanggal_selesai'=>'nullable|date|after_or_equal:tanggal_mulai','lokasi'=>'nullable|string|max:255','penyelenggara_id'=>'nullable|exists:penyelenggaras,id','status'=>'required|in:upcoming,ongoing,completed,cancelled','catatan'=>'nullable|string','fasilitators'=>'array','fasilitators.*.id'=>'required|exists:fasilitators,id','fasilitators.*.peran'=>'nullable|string|max:255']);
        $kegiatan = Kegiatan::create($data + ['created_by'=>$request->user()->id,'updated_by'=>$request->user()->id]);
        $kegiatan->fasilitators()->sync(collect($data['fasilitators'] ?? [])->mapWithKeys(fn($item)=>[$item['id']=>['peran'=>$item['peran'] ?? null]])->all());
        ActivityLog::create(['user_id'=>$request->user()->id,'action'=>'create','description'=>'Kegiatan dibuat.','metadata'=>['kegiatan_id'=>$kegiatan->id]]);
        return response()->json($kegiatan->load('fasilitators'), 201);
    }
}
