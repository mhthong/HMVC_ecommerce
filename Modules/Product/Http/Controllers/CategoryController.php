<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Product\Entities\Category;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response; // Import the Response class

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     * @return Response

     */
    public function index()
    {
        $categories = Category::get(); // Lấy tất cả danh mục, bao gồm cả danh mục con (đa cấp)
        return Inertia::render('Modules/Category/Index', [
            'data' =>  $categories,
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

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string',
                'parent_id' => 'nullable', // Xác thực nếu có parent_id thì phải tồn tại
                'is_featured' => 'required',
            ]);

            $category = Category::create($validated);

            $categories = Category::get(); // Lấy tất cả danh mục, bao gồm cả danh mục con (đa cấp)

            return response()->json([
                'message' => 'Categories store successfully!',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            $categories = Category::get(); // Lấy tất cả danh mục, bao gồm cả danh mục con (đa cấp)
            // Log the exception message
            return response()->json([
                'message' => 'An error occurred while creating the categories. Please try again.',
                'error'   => $e->getMessage(),
                'data' => $categories
            ], 500);
        }
    }



    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Category $page
     * @return Renderable
     *   @return Response
     */
    public function update(Request $request, Category $category)
    {
        try {
            // Validate incoming request

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:categories,id', // Xác thực nếu có parent_id thì phải tồn tại
                'is_featured' => 'required',
            ]);

            $category->update($validated);

            $categories = Category::get(); // Lấy tất cả danh mục, bao gồm cả danh mục con (đa cấp)

            return response()->json([
                'message' => 'Categories edit successfully!',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            $categories = Category::get(); // Lấy tất cả danh mục, bao gồm cả danh mục con (đa cấp)
            // Log the exception message
            return response()->json([
                'message' => 'Categories edit error ! Please try again.',
                'error'   => $e->getMessage(),
                'data' => $categories
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param Category $page
     * @return Renderable
     */
    public function destroy($id)
    {

        try {
            // Validate incoming request

            $category = Category::with('children', 'products')->findOrFail($id);

            // Xóa các quan hệ với sản phẩm trong bảng category_product
            $category->products()->detach();
    
            // Xóa tất cả các danh mục con của category
            $this->deleteChildren($category);
    
            // Xóa danh mục chính
            $category->delete();

            $categories = Category::get(); // Lấy tất cả danh mục, bao gồm cả danh mục con (đa cấp)

            return response()->json([
                'message' => 'Categories edit successfully!',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            $categories = Category::get(); // Lấy tất cả danh mục, bao gồm cả danh mục con (đa cấp)
            // Log the exception message
            return response()->json([
                'message' => 'Category and its related products deleted successfully!',
                'error'   => $e->getMessage(),
                'data' => $categories
            ], 500);
        }

    }

    // Hàm đệ quy để xóa các danh mục con và các liên kết với sản phẩm
    private function deleteChildren($category)
    {
        foreach ($category->children as $child) {
            // Xóa các quan hệ giữa danh mục con và sản phẩm
            $child->products()->detach();

            // Đệ quy để tiếp tục xóa danh mục con của danh mục con
            $this->deleteChildren($child);

            // Xóa danh mục con
            $child->delete();
        }
    }
}
