<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Materi;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    public function index() { return response()->json(Materi::orderBy('nama')->paginate(50)); }
    public function store(Request $request)
    {
        $data=$request->validate(['nama'=>'required|string|max:255|unique:materis,nama','deskripsi'=>'nullable|string','status'=>'required|in:aktif,nonaktif']);
        $materi=Materi::create($data);
        ActivityLog::create(['user_id'=>$request->user()->id,'action'=>'create','description'=>'Materi dibuat.','metadata'=>['materi_id'=>$materi->id]]);
        return response()->json($materi,201);
    }
}
