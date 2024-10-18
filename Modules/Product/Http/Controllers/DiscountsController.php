<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response; // Import the Response class
use Modules\Product\Entities\Discount;
use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\Auth;

class DiscountsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     * @return Response

     */
    public function index()
    {
        $Discount = Discount::all();
        return Inertia::render('Modules/Discount/Index', [
            'datas' =>  $Discount,
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
                'code' => 'required|string|max:255|unique:discounts',
                'value' => 'required',
                'unit' => 'required',
            ]);


            // Create a new page with the authenticated user's ID
            $Discount = Discount::create([
                'code' => $request->input('code'),
                'value' => $request->input('value'),
                'unit' => $request->input('unit'),
            ]);

            $Discount = Discount::all();

            return response()->json([
                'message' => 'Discount store successfully!',
                'datas' =>  $Discount,
            ], 200);
        } catch (\Exception $e) {
            // Log the exception message
            return response()->json([
                'message' => 'An error occurred while creating the discount. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Discount $page
     * @return Renderable
     *   @return Response
     */
    public function update(Request $request, Discount $Discount)
    {
        try {
            // Validate incoming request
            $request->validate([
                'code' => 'required|string|max:255|unique:discounts,code,' . $Discount->id,
                'value' => 'required',
                'unit' => 'required',
            ]);
    
            // Update the discount record
            $Discount->update($request->all());
    
            $Discount = Discount::all();

            return response()->json([
                'message' => 'Discount updated successfully!',
                'datas' =>  $Discount,
            ], 200);


        } catch (\Exception $e) {
            // Log the exception message
            return response()->json([
                'message' => 'Discount update error! Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    

    /**
     * Remove the specified resource from storage.
     * @param Discount $page
     * @return Renderable
     */
    public function destroy($id)
    {
        // Check if the Discount exists
        $discount = Discount::find($id);
        
        if (!$discount) {
            return response()->json(['message' => 'Discount not found'], 404);
        }
    
        // Update all products related to this discount, setting the discount_id to null
        Product::where('discount_id', $id)->update(['discount_id' => null]);
    
        // Delete the Discount
        $discount->delete();
    
        // Get all remaining discounts to return updated data
        $discounts = Discount::all();
    
        return response()->json([
            'message' => 'Discount deleted successfully, related products updated.',
            'datas' => $discounts,
        ]);
    }
    
}
