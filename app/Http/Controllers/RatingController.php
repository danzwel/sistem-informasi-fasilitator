<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $data=$request->validate(['kegiatan_id'=>'required|exists:kegiatans,id','fasilitator_id'=>'required|exists:fasilitators,id','rating'=>'required|integer|between:1,5','review'=>'nullable|string']);
        $rating=Rating::updateOrCreate(['kegiatan_id'=>$data['kegiatan_id'],'fasilitator_id'=>$data['fasilitator_id'],'reviewer_id'=>$request->user()->id],$data+['reviewer_id'=>$request->user()->id]);
        ActivityLog::create(['user_id'=>$request->user()->id,'action'=>'rating','description'=>'Rating fasilitator disimpan.','metadata'=>['rating_id'=>$rating->id]]);
        return response()->json($rating, $rating->wasRecentlyCreated ? 201 : 200);
    }
}
