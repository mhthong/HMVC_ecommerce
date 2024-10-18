<?php

namespace Modules\Slider\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\SliderItem;
use App\Models\Slider;
use App\Models\Page;
use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response; // Import the Response class
use Illuminate\Support\Facades\Auth;

class SliderController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return Renderable
     * @return Response

     */
    public function index()
    {
        $Slider = Slider::all();
        return Inertia::render('Modules/Slider/Index', [
            'datas' =>  $Slider,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     * @return Response
     */
    public function create()
    {
  
        return Inertia::render('Modules/Slider/Create' );
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
                'name' => 'required|string|max:255|unique:sliders,name,',
                'key' => 'required|string|unique:sliders,key,',
                'status' => 'required|string',
                'description' => 'nullable|string',
            ]);

            // Create a new page with the authenticated user's ID
            $Slider = Slider::create([
                'name' => $request->input('name'),
                'key' => $request->input('key'),
                'description' => $request->input('description'),
                'status' => $request->input('status'),
            ]);


            return response()->json([
                'message' => 'Slider store successfully!',
            ], 200);
        } catch (\Exception $e) {
            // Log the exception message
            return response()->json([
                'message' => 'An error occurred while creating the slider. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param Slider $page
     * @return Renderable
     *  @return Response
     */
    public function edit(Slider $slider)
    {
        $sliderItems = SliderItem::where('slider_id',$slider->id)->get();
        return Inertia::render('Modules/Slider/Edit', [
            'data' => $slider,
            'sliderItems' => $sliderItems,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Slider $page
     * @return Renderable
     *  @return Response
     */
    public function update(Request $request, Slider $slider)
    {
        try {
            // Validate incoming request
            $request->validate([
                'name' => 'required|string|max:255|unique:sliders,name,' . $slider->id,
                'key' => 'required|string|unique:sliders,key,' . $slider->id,
                'status' => 'required|string',
                'description' => 'nullable|string',
            ]);

            // Update the block
            $slider->update($request->all());

            return response()->json([
                'message' => 'Slider edit successfully!',
            ], 200);
        } catch (\Exception $e) {
            // Log the exception message
            return response()->json([
                'message' => 'Slider edit error! Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     * @param Slider $page
     * @return Renderable
     */
    public function destroy($id)
    {
        // Check if the Page exists
        $destroy = Slider::find($id);
        if (!$destroy) {
            return response()->json(['message' => $id], 404);
        }

        // Delete the Page
        $destroy->delete();

        $Slider = Slider::all();

        return response()->json(['message' => 'Slider deleted successfully', 'datas' =>  $Slider,]);
    }
}
