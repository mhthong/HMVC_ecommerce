<?php

namespace Modules\Block\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Block;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Inertia\Response; // Import the Response class

class BlockController extends Controller
{
        /**
     * Display a listing of the resource.
     * @return Renderable
     * @return Response

     */
    public function index()
    {
        $blocks = Block::all();
        return Inertia::render('Modules/Block/Index', [
            'datas' =>  $blocks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     * @return Response
     */
    public function create()
    {
        return Inertia::render('Modules/Block/Create');
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
                'name' => 'required|string|max:255|unique:blocks,name,',
                'alias' => 'required|string|unique:blocks,alias,',
                'content' => 'required',
                'status' => 'required|string',
                'description' => 'nullable|string',
            ]);

            // Create a new page with the authenticated user's ID
            $Block = Block::create([
                'name' => $request->input('name'),
                'alias' => $request->input('alias'),
                'content' => $request->input('content'),
                'user_id' => Auth::id(), // Add the ID of the currently authenticated user
                'description' => $request->input('description'),
                'status' => $request->input('status')
            ]);


            return response()->json([
                'message' => 'BLock store successfully!',
            ], 200);
        } catch (\Exception $e) {
            // Log the exception message
            return response()->json([
                'message' => 'An error occurred while creating the block. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param Block $page
     * @return Renderable
     *  @return Response
     */
    public function edit(Block $block)
    {

        return Inertia::render('Modules/Block/Edit', [
            'data' => $block,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Block $page
     * @return Renderable
     *  @return Response
     */
    public function update(Request $request, Block $block)
    {
        try {
            // Validate incoming request
            $request->validate([
                'name' => 'required|string|max:255|unique:blocks,name,' . $block->id,
                'alias' => 'required|string|unique:blocks,alias,' . $block->id,
                'content' => 'required',
                'status' => 'required|string',
                'description' => 'nullable|string',
            ]);
    
            // Update the block
            $block->update($request->all());
    
            return response()->json([
                'message' => 'Block edit successfully!',
            ], 200);
        } catch (\Exception $e) {
            // Log the exception message
            return response()->json([
                'message' => 'Block edit error! Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    /**
     * Remove the specified resource from storage.
     * @param Block $page
     * @return Renderable
     */
    public function destroy($postId)
    {
        // Check if the Page exists
        $block = Block::find($postId);
        if (!$block) {
            return response()->json(['message' => $postId], 404);
        }

        // Delete the Page
        $block->delete();

        $block = Block::all();

        return response()->json(['message' => 'Block deleted successfully', 'datas' =>  $block,]);
    }
}
