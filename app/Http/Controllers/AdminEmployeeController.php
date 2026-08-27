<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminEmployeeController extends Controller
{
    // Danh sách nhân viên
    public function employeesList()
    {
        $employees = User::whereIn('role', ['staff', 'admin'])->orderBy('created_at', 'desc')->get();

        return view('admin.employees', compact('employees'));
    }

    // Form thêm mới nhân viên
    public function employeeCreate()
    {
        return view('admin.employees_add');
    }

    // Lưu nhân viên mới
    public function employeeStore(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:staff,admin',
            'status' => 'required|integer|in:0,1',
            'notes' => 'nullable|string',
        ]);

        User::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('quanly_nhanvien')->with('success', 'Thêm mới nhân viên thành công!');
    }

    // Chi tiết nhân viên
    public function employeeShow($id)
    {
        $employee = User::whereIn('role', ['staff', 'admin'])->findOrFail($id);

        return view('admin.employees_detail', compact('employee'));
    }

    // Form chỉnh sửa thông tin nhân viên
    public function employeeEdit($id)
    {
        $employee = User::whereIn('role', ['staff', 'admin'])->findOrFail($id);

        return view('admin.employees_edit', compact('employee'));
    }

    // Cập nhật thông tin nhân viên
    public function employeeUpdate(Request $request, $id)
    {
        $employee = User::whereIn('role', ['staff', 'admin'])->findOrFail($id);

        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,'.$employee->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|in:staff,admin',
            'status' => 'required|integer|in:0,1',
            'notes' => 'nullable|string',
        ]);

        $data = $request->only('fullname', 'phone', 'email', 'role', 'status', 'notes');
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);

        return redirect()->route('quanly_nhanvien')->with('success', 'Cập nhật thông tin nhân viên thành công!');
    }

    // Xóa nhân viên
    public function employeeDestroy($id)
    {
        $employee = User::whereIn('role', ['staff', 'admin'])->findOrFail($id);

        if ($employee->id === auth()->id()) {
            return redirect()->route('quanly_nhanvien')->with('error', 'Bạn không thể tự xóa tài khoản của chính mình.');
        }

        if ($employee->orders()->count() > 0 || $employee->subscriptions()->count() > 0) {
            return redirect()->route('quanly_nhanvien')->with('error', 'Không thể xóa nhân viên này vì đã có dữ liệu đơn hàng hoặc gói dịch vụ liên kết.');
        }

        $employee->delete();

        return redirect()->route('quanly_nhanvien')->with('success', 'Đã xóa tài khoản nhân viên thành công!');
    }
}
