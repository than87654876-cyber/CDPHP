<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    // Danh sách đánh giá của khách hàng
    public function reviewsList()
    {
        $reviews = Review::with(['user', 'order'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.reviews', compact('reviews'));
    }

    // Xóa đánh giá
    public function destroyReview($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('quanly_reviews')->with('success', 'Đã xóa đánh giá thành công.');
    }
}
