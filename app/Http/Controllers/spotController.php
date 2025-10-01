<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpotRequest;
use App\Models\Category;
use App\Models\Spot;
use App\Models\spots;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class spotController extends Controller
{

    public function index(){
        try{
            $spots = Spot::with([
                'user::id,name',
                'catgories:category,spot_id'
            ])
            ->withCount([
                'riviews'
            ])
            ->withSum('riviews','rating')
            ->orderBy('created_at', 'desc')
            ->paginate(request('size',10));
            return response()->json([
                'message' => 'List Spot',
                'spot' => $spots
            ],200);

        } catch (Exception $e){
           return Response::json ([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function store(StoreSpotRequest $request){
        try{
            $validated = $request->safe()->all();

            $picture_path = Storage::disk('public')->putFile('spots', $request->file('picture'));

            $validated['user_id'] = Auth::user()->id;
            $validated['picture'] = $picture_path;

            $spot = Spot::create($validated);

            if($spot){

                $categories = [];

                foreach($validated['category'] as $category){
                    $categories[] =[
                        'spot_id' => $spot->id,
                        'category' => $category
                    ];
                }

                Category::fillAndInsert($categories);

                return response()->json([
                    'message' => "Berhasil menyimpan spot",
                    'data' => null
                ],201);
            }

         }catch (Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);

        }
    }
        public function destroy(Spot $spot){
            try{
                $user = Auth::user();
                if($spot->user_id == $user->id || $user->role == 'ADMIN'){
                    if($spot->dalete()){
                      return Response::json([
                        'message' => "Spot berhasil di hapus",
                        'data' => null
                      ],200);

                    }
                } else{
                    return Response::json([
                        'message'=> "Spot gagal di hapus",
                        'data' => null
                    ], 200);
                }
            }catch (Exception $e){
            return Response::json ([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }




    }

    public function review(Spot $spot){

        try{
            return Response::json([
                'message' => 'List Review',
                'data' => $spot->reviews()->with([
                    'user:id,nama'
                ])->get()
                ], 200);
        }catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ],500);
        }
    }
}


