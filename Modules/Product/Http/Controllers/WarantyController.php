<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Warranty;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response; // Import the Response class

class WarantyController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     * @return Response
     * 
     */
    public function index()
    {
        $Product = Product::get();

        return Inertia::render('Modules/Waranty/Index', [
            'datas' =>  $Product,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     *  @return Response
     */
    public function create()
    {
        $Product = Product::get();

        return Inertia::render('Modules/Waranty/Create', [
            'datas' =>  $Product,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */ public function store(Request $request)
    {
        try {
            // Validate incoming request
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id', // Change string to integer for ID validation
                'quantity' => 'required|integer|min:1', // Ensure quantity is a positive integer
            ]);

            $warranties = []; // Array to hold created warranties

            // Loop to create multiple warranties
            for ($i = 0; $i < $validated['quantity']; $i++) {
                // Generate a unique warranty code
                $code = $this->generateUniqueWarrantyCode();

                // Create the warranty
                $warranty = Warranty::create([
                    'product_id' => $validated['product_id'],
                    'warranty_code' => $code,
                    'customer_id' => null,
                    'active_date' => null,
                    'status' => 'pending',
                ]);

                $warranties[] = $warranty; // Add to the warranties array
            }

            return response()->json([
                'message' => 'Warranties created successfully!',
                'warranties' => $warranties, // Return the created warranties
            ], 200);
        } catch (\Exception $e) {
            // Log the error if any
            Log::error('Error while creating warranty: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while creating the warranty. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate a unique warranty code.
     *
     * @return string
     */
    private function generateUniqueWarrantyCode()
    {
        do {
            // Generate a new warranty code
            $code = 'WTY-' . uniqid() . '-' . mt_rand(10000, 99999);
            
            // Check if the code already exists in the database
            $exists = Warranty::where('warranty_code', $code)->exists();
        } while ($exists); // Repeat if the code already exists

        return $code; // Return the unique code
    }



    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     * @return Response
     */
    public function floder($id)
    {
        $Warranty = Warranty::where('product_id' ,$id)->where(   'status' ,'pending')->get();

        return Inertia::render('Modules/Waranty/Folder', [
            'datas' =>  $Warranty,
            'product_id_clear' =>  $id,
        ]);
    }


      /**
     * Clear the warranty status for a specific product.
     *
     * @param  int  $product_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear($product_id)
    {
        try {
            // Find all warranties with the given product_id and status 'pending'
            $affectedRows = Warranty::where('product_id', $product_id)
                ->where('status', 'pending')
                ->update(['status' => 'clear']);
    
            // Check if any rows were updated
            if ($affectedRows == 0) {
                return response()->json([
                    'message' => 'No pending warranties found for the given product.',
                ], 404);
            }
    
            return response()->json([
                'message' => 'All pending warranties for the product have been cleared successfully.',
                'affected_rows' => $affectedRows,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error while clearing warranties: ' . $e->getMessage());
    
            return response()->json([
                'message' => 'Error clearing the warranties.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
}
