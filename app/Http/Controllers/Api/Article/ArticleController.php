<?php

namespace App\Http\Controllers\Api\Article;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Traits\MediaTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Tymon\JWTAuth\JWTAuth;

class ArticleController extends Controller
{
    use MediaTrait;
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if($request->user()){
            $validator = Validator::make($request->all(),[
                'title' => 'required',
                'description' => 'required'
            ]);

            if($validator->fails()){
                return response()->json($validator->errors());
            }

            // dd($request->file('image'));

            $files = $request->file('image');
           // dd($files);

            $post = new Article();
            $post->title = $request->title;
            $post->description = $request->description;
            $post->user_id = $request->user()->id;

            $post->save();

            foreach($files as $file) {
                $fileData = $this->uploads($file, 'images/articles/');

                $image = Image::make(public_path($fileData["filePath"]));

                $image->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $image->save();

                $data['image'] = $fileData['filePath'];
                $post->images()->createMany([
                    ['path' => $data['image'],
                    'article_id' => $post->id]
                ]);
            }
            // dd($images);



            return response()->json([
                'message' => 'Article created successfully',
                'data' => $post
            ], 200);
        }
        else{
            return response()->json([
                "errors" => [
                    "message" => "Invalid Token"
                ]
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {

        if($request->user()){

            $article = Article::with(['images'])->where('id', $id)->first();
            if (is_null($article)) {
                return response()->json([
                    'message' => 'Article not found',
                ], 404);
            }

            return response()->json([
                'message' => 'Article found successfully',
                'data' => $article
            ], 200);
        }
        else{
            return response()->json([
                "errors" => [
                    "message" => "Invalid Token"
                ]
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateArticle(Request $request, $id)
    {

        if($request->user()){
            $validator = Validator::make($request->all(),[
                'title' => 'required',
                'description' => 'required'
            ]);

            if($validator->fails()){
                return response()->json($validator->errors());
            }

            $article = Article::find($id);

            if (is_null($article)) {
                return response()->json([
                    'message' => 'Article not found',
                ], 404);
            }

            $files = $request->file('image');

            $article->title = $request->title;
            $article->description = $request->description;
            $article->save();

            // dd($files);
            foreach($files as $file) {
                $fileData = $this->uploads($file, 'images/articles/');

                $image = Image::make(public_path($fileData["filePath"]));

                $image->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $image->save();

                $data['image'] = $fileData['filePath'];
                $article->images()->update(
                    ['path' => $data['image'],
                    'article_id' => $article->id]
                );
            }

            return response()->json([
                'message' => 'Article updated successfully',
                'data' => $article
            ], 200);
        }
        else{
            return response()->json([
                "errors" => [
                    "message" => "Invalid Token"
                ]
            ]);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if($request->user()){
            $article = Article::find($id);

            if (is_null($article)) {
                return response()->json([
                    'message' => 'Article not found',
                ], 404);
            }

            $article->delete();

            return response()->json([
                'message' => 'Article deleted successfully',
            ], 200);
        }
        else{
            return response()->json([
                "errors" => [
                    "message" => "Invalid Token"
                ]
            ]);
        }
    }
}
