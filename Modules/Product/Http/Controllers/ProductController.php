<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response; // Import the Response class
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Discount;
use Modules\Product\Entities\Category;
use App\Models\Slug;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     * @return Response

     */
    public function index()
    {
        $Product = Product::with('categories:id,name','discount:id,code')->get();
        $Discount = Discount::select('id', 'code')->get();
        $Category = Category::get();
        return Inertia::render('Modules/Product/Index', [
            'datas' =>  $Product,
            'Category' => $Category,
            'Discount' => $Discount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     * @return Response
     */
    public function create()
    {
        $Discount = Discount::select('id', 'code')->get();
        $Category = Category::get();
        return Inertia::render(
            'Modules/Product/Create',
            [
                'Category' => $Category,
                'Discount' => $Discount,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate incoming request
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:products,name',
                'slug' => 'required|string|max:255|unique:products,slug',
                'price' => 'required',
                'priceoff' => 'required',
                'status' => 'required|string|max:60',
                'is_featured' => 'boolean',
                'shortdescription' => 'nullable|string',
                'description' => 'nullable|string',
                'content' => 'nullable|string',
                'image' => 'required|string|max:255', // Đảm bảo ảnh chính tồn tại
                'discount_id' => 'nullable|exists:discounts,id', // Kiểm tra nếu discount_id tồn tại trong bảng discounts
                'Category_id' => 'array', // Danh sách các ID danh mục
                'Category_id.*' => 'exists:categories,id', // Kiểm tra danh mục có tồn tại không
                'ForeignImage' => 'nullable|string', // Các ảnh phụ nếu có
            ]);

            // Tạo sản phẩm mới
            $product = Product::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'price' => $validated['price'],
                'priceoff' => $validated['priceoff'],
                'status' => $validated['status'],
                'is_featured' => $validated['is_featured'] ?? 0,
                'shortdescription' => $validated['shortdescription'] ?? '',
                'description' => $validated['description'] ?? '',
                'content' => $validated['content'] ?? '',
                'image' => $validated['image'],
                'discount_id' => $validated['discount_id'] ,
            ]);

            // Liên kết sản phẩm với các danh mục
            if (!empty($validated['Category_id'])) {
                $product->categories()->sync($validated['Category_id']);
            }

            // Thêm ảnh phụ nếu có
            if (!empty($validated['ForeignImage'])) {
                $foreignImages = explode(',', $validated['ForeignImage']);
                foreach ($foreignImages as $image) {
                    $product->foreignImages()->create([
                        'image' => $image,
                    ]);
                }
            }

            // Tạo hoặc cập nhật slug cho sản phẩm
            Slug::updateOrCreate(
                [
                    'reference_id' => $product->id,
                    'reference' => Product::class,
                    'prefix' => 'product',
                ],
                [
                    'key' => $validated['slug']
                ]
            );

            return response()->json([
                'message' => 'Product created successfully!',
                'product' => $product,
            ], 200);
        } catch (\Exception $e) {
            // Ghi log lỗi nếu có
            Log::error('Error while creating product: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while creating the product. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param Product $page
     * @return Renderable
     *  @return Response
     */
    public function edit(Product $Product)
    {
        $Discount = Discount::select('id', 'code')->get();
        $Category = Category::get();
        $product = Product::with('categories:id','foreignImages','discount')->where('id', '=', $Product->id)->first();
        return Inertia::render('Modules/Product/Edit', [
            'data' => $product,
            'Category' => $Category,
            'Discount' => $Discount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Product $page
     * @return Renderable
     *   @return Response
     */
    public function update(Request $request, Product $Product)
    {
        try {
            // Validate incoming request
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:products,name,' . $Product->id,
                'slug' => 'required|string|unique:slugs,key,' . $Product->id . ',reference_id',
                'price' => 'required',
                'priceoff' => 'required',
                'status' => 'required|string|max:60',
                'is_featured' => 'boolean',
                'shortdescription' => 'nullable|string',
                'description' => 'nullable|string',
                'content' => 'nullable|string',
                'image' => 'required|string|max:255', // Ensure main image exists
                'discount_id' => 'nullable|exists:discounts,id', // Validate if discount exists
                'Category_id' => 'array', // Array of category IDs
                'Category_id.*' => 'exists:categories,id', // Ensure categories exist
                'ForeignImage' => 'nullable|string', // Foreign images if any
            ]);
    
            // Update product fields
            $Product->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'price' => $validated['price'],
                'priceoff' => $validated['priceoff'],
                'status' => $validated['status'],
                'is_featured' => $validated['is_featured'] ?? 0,
                'shortdescription' => $validated['shortdescription'] ?? '',
                'description' => $validated['description'] ?? '',
                'content' => $validated['content'] ?? '',
                'image' => $validated['image'],
                'discount_id' => $validated['discount_id'],
            ]);
    
            // Update product categories if provided
            if (!empty($validated['Category_id'])) {
                $Product->categories()->sync($validated['Category_id']);
            }
    
            // Update foreign images if provided
            if (!empty($validated['ForeignImage'])) {
                $foreignImages = explode(',', $validated['ForeignImage']);
                
                // Remove old foreign images
                $Product->foreignImages()->delete();
    
                // Add the new ones
                foreach ($foreignImages as $image) {
                    $Product->foreignImages()->create([
                        'image' => $image,
                    ]);
                }
            }
    
            // Update slug for the product
            Slug::updateOrCreate(
                [
                    'reference_id' => $Product->id,
                    'reference' => Product::class,
                    'prefix' => 'product',
                ],
                [
                    'key' => $validated['slug']
                ]
            );
    
            return response()->json([
                'message' => 'Product updated successfully!',
                'product' => $Product,
            ], 200);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error while updating product: ' . $e->getMessage());
    
            return response()->json([
                'message' => 'An error occurred while updating the product. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    /**
     * Remove the specified resource from storage.
     * @param Product $page
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            // Check if the product exists
            $Product = Product::find($id);
            if (!$Product) {
                return response()->json([
                    'message' => 'Product not found!',
                    'error'   => 'The product with the given ID does not exist.',
                ], 404);
            }
    
            // Delete associated foreign images (if they exist)
            if ($Product->foreignImages) {
                $Product->foreignImages()->delete();
            }
    
            // Detach associated categories
            $Product->categories()->detach();
    
            // Delete the product
            $Product->delete();
    
            // Fetch all remaining products for the response
            $allProducts = Product::with('categories:id,name', 'discount:id,code')->get();
    
            return response()->json([
                'message' => 'Product deleted successfully!',
                'datas'   => $allProducts,
            ], 200);
        } catch (\Exception $e) {
            // Log any exception that occurs during the deletion process
            Log::error('Error while deleting product: ' . $e->getMessage());
    
            return response()->json([
                'message' => 'An error occurred while deleting the product. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    /**
 * Bulk delete products.
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
*/
    public function bulkDestroy(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:products,id',
            ]);

            // Delete associated foreign images and detach categories
            Product::whereIn('id', $validated['ids'])->each(function ($product) {
                $product->foreignImages()->delete();
                $product->categories()->detach();
                $product->delete();
            });

            // Fetch updated products list
            $allProducts = Product::with('categories:id,name', 'discount:id,code')->get();

            return response()->json([
                'message' => 'Selected products deleted successfully!',
                'datas'   => $allProducts,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error while bulk deleting products: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while deleting the products. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    
}
