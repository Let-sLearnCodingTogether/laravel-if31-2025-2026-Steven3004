<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ReviewController
{

public function store(StoreReviewRequest $request)
    {
        try{
            $validated = $request->safe()->all;
            $validated['user_id']=Auth::user()->id;

            $response = Review::create($validated);

            if($response){
                return response()->json([
                    'message' => "berhasil",
                    "data"  => $validated
                ], 200);
            }
                return response()->json([
                    'message'=> "Review gagal di buat",
                    'data'=>null
                ],400);
        } catch(Exception $e){
             return response()->json([
                'message'=> $e->getMessage(),
                'data'=>null
            ],500);
        }
    }

public function destroy(Review $review)
{
    try{
        if($review->delete()){
            return response()->json([
                'message' => "Review berhasil di hapus",
                'data' => null
            ], 200);
        }

        return Response::json([
            'message' => "Review gagal di hapus",
            'data' => null
        ], 500);
    } catch (Exception $e){
        return response()->json([
            'message' => $e->getMessage(),
            'data' => null
        ], 500);
    }
}




}
