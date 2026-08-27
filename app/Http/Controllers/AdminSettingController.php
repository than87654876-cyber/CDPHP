<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminSettingController extends Controller
{
    // Hiển thị giao diện cấu hình hình nền
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
        return view('admin.settings', compact('settings'));
    }

    // Cập nhật cấu hình hình nền
    public function update(Request $request)
    {
        $request->validate([
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'site_background' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
        ]);

        // 1. Xử lý upload Ảnh Nền Khung Banner Hero (Khung bên trái trang chủ)
        if ($request->hasFile('banner_image')) {
            $bannerUrl = null;
            try {
                $cloudinary = app(\App\Services\CloudinaryService::class);
                $bannerUrl = $cloudinary->upload($request->file('banner_image'));
            } catch (\Exception $e) {
                Log::warning('Cloudinary settings banner upload failed: ' . $e->getMessage());
            }

            if (!$bannerUrl) {
                $file = $request->file('banner_image');
                $filename = 'banner_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $filename);
                $bannerUrl = '/uploads/' . $filename;
            }

            Setting::setValue('banner_image', $bannerUrl);
        }

        // 2. Xử lý upload Ảnh Nền Chính Toàn Website (Background bàn ăn phía sau)
        if ($request->hasFile('site_background')) {
            $bgUrl = null;
            try {
                $cloudinary = app(\App\Services\CloudinaryService::class);
                $bgUrl = $cloudinary->upload($request->file('site_background'));
            } catch (\Exception $e) {
                Log::warning('Cloudinary settings site background upload failed: ' . $e->getMessage());
            }

            if (!$bgUrl) {
                $file = $request->file('site_background');
                $filename = 'site_bg_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $filename);
                $bgUrl = '/uploads/' . $filename;
            }

            Setting::setValue('site_background', $bgUrl);
        }

        // Xóa cache view để trang chủ nhận ngay ảnh mới
        try {
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
        } catch (\Exception $e) {
            // Ignore if fails
        }

        return redirect()->route('quanly_cauhinh')->with('success', 'Đã cập nhật và áp dụng hình nền mới cho website thành công!');
    }
}
