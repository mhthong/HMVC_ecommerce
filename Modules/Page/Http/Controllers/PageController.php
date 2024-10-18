<?php

namespace Modules\Page\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Page;
use App\Models\Slug;
use App\Models\Slider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Inertia\Response; // Import the Response class
use Illuminate\Validation\Rule;




class PageController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return Renderable
     * @return Response

     */
    public function index()
    {
        $pages = Page::all();
        return Inertia::render('Modules/Page/Index', [
            'pages' =>  $pages,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     * @return Response
     */
    public function create()
    {
        $slider = Slider::select('id','name')->get();

        return Inertia::render('Modules/Page/Create',
    [
        'slider' => $slider,
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
                'name' => 'required|string|max:255|unique:pages',
                'slug' => 'required|string|unique:slugs,key,',
                'content' => 'required',
                'status' => 'required|string',
                'template' => 'nullable|string',
                'description' => 'nullable|string',
                'image' => 'nullable|string',
                'slider_id'=>'required',
            ]);

            // Create a new page with the authenticated user's ID
            $Page = Page::create([
                'name' => $request->input('name'),
                'slug' => $request->input('slug'),
                'content' => $request->input('content'),
                'user_id' => Auth::id(), // Add the ID of the currently authenticated user
                'image' => $request->input('image'),
                'template' => $request->input('template'),
                'description' => $request->input('description'),
                'status' => $request->input('status'),
                'slider_id' => $request->input('slider_id')
            ]);

            // Update or create a new slug
            Slug::updateOrCreate(
                [
                    'reference_id' => $Page->id,
                    'reference' => Page::class, // Ensure this is the correct reference type
                    'prefix' => 'page',
                ],
                [
                    'key' => $request->input('slug')
                ]
            );

            return response()->json([
                'message' => 'Page store successfully!',
            ], 200);
        } catch (\Exception $e) {
            // Log the exception message
            return response()->json([
                'message' => 'An error occurred while creating the page. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param Page $page
     * @return Renderable
     *  @return Response
     */
    public function edit(Page $page)
    {
        $slider = Slider::select('id','name')->get();
        return Inertia::render('Modules/Page/Edit', [
            'page' => $page,
            'slider' => $slider,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Page $page
     * @return Renderable
     *   @return Response
     */
    public function update(Request $request, Page $page)
    {
        try {
            // Validate incoming request
            $request->validate([
                'name' => 'required|string|max:255|unique:pages,name,' . $page->id,
                'slug' => 'required|string|unique:slugs,key,' . $page->id . ',reference_id',
                'content' => 'required',
                'status' => 'required|string',
                'template' => 'nullable|string',
                'description' => 'nullable|string',
                'image' => 'nullable|string',
                'slider_id'=>'required',
            ]);

            $page->update($request->all());


            // Update or create a new slug
            Slug::updateOrCreate(
                [
                    'reference_id' => $page->id,
                    'reference' => Page::class, // Ensure this is the correct reference type
                    'prefix' => 'page',
                ],
                [
                    'key' => $request->input('slug')
                ]
            );

            return response()->json([
                'message' => 'Page edit successfully!',
            ], 200);
        } catch (\Exception $e) {
            // Log the exception message
            return response()->json([
                'message' => 'Page edit error ! Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param Page $page
     * @return Renderable
     */
    public function destroy($pageId)
    {
        // Check if the Page exists
        $page = Page::find($pageId);
        if (!$page) {
            return response()->json(['message' => $pageId], 404);
        }

        // Check if a slug already exists for this page
        $existingSlug = Slug::where('reference_id', $page->id)
            ->where('reference', Page::class)
            ->first();

        if ($existingSlug) {
            $existingSlug->delete();
        }


        // Delete the Page
        $page->delete();

        $pages = Page::all();

        return response()->json(['message' => 'Page deleted successfully', 'datas' =>  $pages,]);
    }
}
