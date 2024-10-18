<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Menu;
use App\Models\MenuMain;
use App\Models\Page;
use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response; // Import the Response class
use Illuminate\Support\Facades\Auth;

class MenuMainController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return Renderable
     * @return Response

     */
    public function index()
    {
        $MenuMain = MenuMain::all();
        return Inertia::render('Menu/Index', [
            'datas' =>  $MenuMain,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     * @return Response
     */
    public function create()
    {
  
        return Inertia::render('Menu/Create' );
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
                'name' => 'required|string|max:255|unique:menu_main,name,',
                'key' => 'required|string|unique:menu_main,key,',
            ]);

            // Create a new page with the authenticated user's ID
            $Slider = MenuMain::create([
                'name' => $request->input('name'),
                'key' => $request->input('key'),

            ]);


            return response()->json([
                'message' => 'Menu store successfully!',
            ], 200);
        } catch (\Exception $e) {
            // Log the exception message
            return response()->json([
                'message' => 'An error occurred while creating the menu. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param MenuMain $page
     * @return Renderable
     *  @return Response
     */
    public function edit(MenuMain $menu)
    {
        $Menu = Menu::where('main_id',$menu->id)->get();

        return Inertia::render('Menu/Edit', [
            'data' => $menu,
            'Items' => $Menu,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param MenuMain $page
     * @return Renderable
     *  @return Response
     */
    public function update(Request $request, MenuMain $menu)
    {
        try {
            // Validate incoming request
            $request->validate([
                'name' => 'required|string|max:255|unique:sliders,name,' . $menu->id,
                'key' => 'required|string|unique:sliders,key,' . $menu->id,
            ]);

            // Update the block
            $menu->update($request->all());

            return response()->json([
                'message' => 'Menu edit successfully!',
            ], 200);
        } catch (\Exception $e) {
            // Log the exception message
            return response()->json([
                'message' => 'Menu edit error! Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     * @param MenuMain $page
     * @return Renderable
     */
    public function destroy($id)
    {
        // Check if the Page exists
        $destroy = MenuMain::find($id);
        if (!$destroy) {
            return response()->json(['message' => $id], 404);
        }

        // Delete the Page
        $destroy->delete();

        $Slider = MenuMain::all();

        return response()->json(['message' => 'Menu deleted successfully', 'datas' =>  $Slider,]);
    }
}
