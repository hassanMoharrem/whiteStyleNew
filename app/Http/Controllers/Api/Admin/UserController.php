<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // عرض جميع المستخدمين مع pagination
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $users = $query->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'data' => ['users' => $users],
            'message' => 'Users retrieved successfully'
        ]);
    }
    // إضافة مستخدم جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'visible' => 'boolean',
        ]);
        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);
        return response()->json([
            'status' => true,
            'data' => ['user' => $user],
            'message' => 'User created successfully'
        ], 201);
    }

    // عرض مستخدم واحد
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'status' => true,
            'data' => ['user' => $user]
        ]);
    }

    // تحديث مستخدم
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'visible' => 'boolean',
        ]);
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }
        $user->update($validated);
        return response()->json([
            'status' => true,
            'data' => ['user' => $user]
        ]);
    }

    // حذف مستخدم
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            'status' => true,
            'message' => 'تم حذف المستخدم بنجاح'
        ]);
    }
}
