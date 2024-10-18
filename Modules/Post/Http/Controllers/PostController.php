<?php

namespace Modules\Post\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Post;
use App\Models\Page;
use App\Models\Slug;
use App\Models\MetaBoxes;
use App\Models\Slider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Inertia\Response; // Import the Response class
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     * @return Response

     */
    public function index()
    {
        $posts = Post::all();
        return Inertia::render('Modules/Post/Index', [
            'datas' =>  $posts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     * @return Response
     */
    public function create()
    {
        $pages = Page::select('id', 'name')->get();
        $slider = Slider::select('id','name')->get();
        return Inertia::render('Modules/Post/Create', [
            'pages' =>  $pages,
            'slider' =>  $slider,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        // Validate incoming request
        try {

            // Validate incoming request
            $request->validate([
                'name' => 'required|string|max:255|unique:posts',
                'slug' => 'required|string|unique:slugs,key,',
                'status' => 'required|string',
                'content' => 'required',
                'title' => 'required',
                'image' => 'nullable|string',
                'target' => 'nullable|array', // Updated to validate as an array
                'description' => 'nullable|string',
                'is_featured' => 'required',
                'page' => 'nullable|array', // Validate if pages are provided
                'slider_id' => 'required',

            ]);

            $target = $request->has('target') ? json_encode($request->input('target')) : null;

            // Create a new page with the authenticated user's ID
            $Post = Post::create([
                'name' => $request->input('name'),
                'slug' => $request->input('slug'),
                'content' => $request->input('content'),
                'status' => $request->input('status'),
                'title' => $request->input('title'),
                'user_id' => Auth::id(), // Add the ID of the currently authenticated user
                'image' => $request->input('image'),
                'target' => $target, // Save the target as a JSON string
                'is_featured' => $request->input('is_featured'),
                'description' => $request->input('description'),
                'slider_id' => $request->input('slider_id')
            ]);

            // Update or create a new slug
            Slug::updateOrCreate(
                [
                    'reference_id' => $Post->id,
                    'reference' => Post::class, // Ensure this is the correct reference type
                    'prefix' => 'post',
                ],
                [
                    'key' => $request->input('slug')
                ]
            );

            // Attach selected pages to the post
            if ($request->has('page')) {
                $Post->pages()->sync($request->input('page')); // Attach or sync the relationship with pages
            }


            return response()->json([
                'message' => 'Post store successfully!',
            ], 200);
        } catch (\Exception $e) {
            // Log the exception message
            return response()->json([
                'message' => 'An error occurred while creating the post. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param Post $page
     * @return Renderable
     *  @return Response
     */
    public function edit(Post $post)
    {
        // Lấy danh sách tất cả các trang (id và name) để hiển thị
        $pages = Page::select('id', 'name')->get();

        // Decode field 'target' nếu nó tồn tại và không null
        $post->target = $post->target ? json_decode($post->target, true) : [];

        // Lấy danh sách các page_id liên kết với bài viết hiện tại
        $post->page = $post->pages()->pluck('page_id')->toArray();

        $slider = Slider::select('id','name')->get();

        // Trả về Inertia view với dữ liệu bài viết, danh sách trang, và các trang đã chọn
        return Inertia::render('Modules/Post/Edit', [
            'data' => $post,
            'pages' => $pages,
            'slider' => $slider,
        ]);
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Post $page
     * @return Renderable
     *   @return Response
     */
    public function update(Request $request, Post $post)
    {

        try {
            // Validate incoming request
            // Validate incoming request
            $request->validate([
                'name' => 'required|string|max:255|unique:posts,name,' . $post->id,
                'slug' => 'required|string|unique:slugs,key,' . $post->id . ',reference_id',
                'status' => 'required|string',
                'content' => 'required',
                'title' => 'required',
                'image' => 'nullable|string',
                'target' => 'nullable|array', // Updated to validate as an array
                'description' => 'nullable|string',
                'is_featured' => 'required',
                'page' => 'nullable|array', // Validate if pages are provided
                'slider_id' => 'required',
            ]);

            $post->update($request->all());


            // Update or create a new slug
            Slug::updateOrCreate(
                [
                    'reference_id' => $post->id,
                    'reference' => Post::class, // Ensure this is the correct reference type
                    'prefix' => 'post',
                ],
                [
                    'key' => $request->input('slug')
                ]
            );

            // Sync the pages (delete old relations and add new ones)
            if ($request->has('page')) {
                $post->pages()->sync($request->input('page')); // Update pages relationships
            } else {
                // If no pages are sent, remove all related pages
                $post->pages()->sync([]);
            }

            // Sync the pages (delete old relations and add new ones)



            return response()->json([
                'message' => 'Post edit successfully!',
            ], 200);
        } catch (\Exception $e) {
            // Log the exception message
            return response()->json([
                'message' => 'Post edit error ! Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param Post $page
     * @return Renderable
     */
    public function destroy($postId)
    {
        // Check if the Page exists
        $post = Post::find($postId);
        if (!$post) {
            return response()->json(['message' => $postId], 404);
        }

        // Check if a slug already exists for this page
        $existingSlug = Slug::where('reference_id', $post->id)
            ->where('reference', Post::class)
            ->first();

        if ($existingSlug) {
            $existingSlug->delete();
        }

        // Xóa các liên kết giữa bài viết và trang (xóa trong bảng page_post)
        $post->pages()->detach();

        // Delete the Page
        $post->delete();

        $posts = Post::all();

        return response()->json(['message' => 'Post deleted successfully', 'datas' =>  $posts,]);
    }
}
