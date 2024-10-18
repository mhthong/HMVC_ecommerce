<?php

namespace App\Http\Controllers;

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

class MenuController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            // Lấy slider_id từ request
            $sliderId = $request->input('slider_id');
    
            // Xóa tất cả các SliderItems có cùng slider_id
            SliderItem::where('slider_id', $sliderId)->delete();
    
            // Lấy danh sách slider items từ request
            $sliderItems = $request->input('slider_items');
    
            foreach ($sliderItems as $key => $item) {
                // Tạo mới từng SliderItem
                SliderItem::create([
                    'description' => $item['description'],
                    'link' => $item['link'],
                    'title' => $item['title'],
                    'image' => $item['image'],
                    'order' => $key + 1,  // Lưu thứ tự (order)
                    'slider_id' => $sliderId,  // Lấy slider_id từ request
                ]);
            }
    
            // Trả về phản hồi thành công
            return response()->json(['message' => 'Slider items created or updated successfully'], 200);
        } catch (\Exception $e) {
            // Xử lý lỗi và trả về phản hồi lỗi với chi tiết lỗi
            return response()->json([
                'message' => 'An error occurred while creating the slider. Please try again.',
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
    
    
    

    /**
     * Remove the specified resource from storage.
     * @param SliderItem $page
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
