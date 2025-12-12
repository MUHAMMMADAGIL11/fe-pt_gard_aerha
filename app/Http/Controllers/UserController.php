<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth; 
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function register(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:50|unique:users,username',
                'password' => 'required|string|min:6',
                'nama_lengkap' => 'nullable|string|max:255',
                'role' => 'nullable|string|max:255',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 400);
            }
            $user = $this->userService->register($request->all());
            return response()->json([
                'message' => 'User registered successfully!',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Username and password are required'], Response::HTTP_BAD_REQUEST);
        }
        $credentials = $request->only('username', 'password');
        $user = User::where('username', $credentials['username'])->first();
        if ($user && Hash::check($credentials['password'], $user->password)) {
            $token = JWTAuth::fromUser($user);
            return response()->json([
                'message' => 'Login successful',
                'username' => $user->username,
                'nama_lengkap' => $user->nama_lengkap,
                'role' => $user->role,
                'token' => $token
            ], Response::HTTP_OK)->cookie('token', $token, 60 * 24);
        }
        return response()->json(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
    }

   public function logout(Request $request)
{
    $token = $request->bearerToken() ?: $request->cookie('token');

    if ($token) {
        return response()->json(['message' => 'Logout successful'])
            ->withCookie(cookie('token', null, -1));
    }

    return response()->json(['message' => 'Token not found'], 400);
}

    public function indexKelolaUser(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user->hasRole('KepalaDivisi')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Kepala Divisi yang dapat mengelola user'
                ], 403);
            }
                
            $users = User::all();
            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function showKelolaUser(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user->hasRole('KepalaDivisi')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Kepala Divisi yang dapat mengelola user'
                ], 403);
            }

            $targetUser = User::find($id);
            if (!$targetUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $targetUser
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeKelolaUser(Request $request)
    {
        try {
            $user = $request->user();
            
            $allowedRoles = null;
            if ($user->hasRole('KepalaDivisi')) {
                $allowedRoles = ['AdminGudang', 'PetugasOperasional', 'KepalaDivisi'];
            } elseif ($user->hasRole('AdminGudang')) {
                $allowedRoles = ['PetugasOperasional'];
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menambah user'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:50|unique:users,username',
                'password' => 'required|string|min:6',
                'nama_lengkap' => 'nullable|string|max:255',
                'role' => ['required','string', Rule::in($allowedRoles)],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $newUser = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'nama_lengkap' => $request->nama_lengkap,
                'role' => $request->role,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan',
                'data' => $newUser
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateKelolaUser(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user->hasRole('KepalaDivisi')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Kepala Divisi yang dapat mengelola user'
                ], 403);
            }

            $targetUser = User::find($id);
            if (!$targetUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'username' => 'sometimes|string|max:50|unique:users,username,' . $id . ',id_user',
                'password' => 'sometimes|string|min:6',
                'nama_lengkap' => 'sometimes|string|max:255',
                'role' => 'sometimes|string|in:AdminGudang,PetugasOperasional,KepalaDivisi',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = $request->only(['username', 'nama_lengkap', 'role']);
            if ($request->has('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $targetUser->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui',
                'data' => $targetUser
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroyKelolaUser(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user->hasRole('KepalaDivisi')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Kepala Divisi yang dapat mengelola user'
                ], 403);
            }

            $targetUser = User::find($id);
            if (!$targetUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $targetUser->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
