<?php

namespace App\Http\Controllers;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Inertia\Response; // Import the Response class

class UserController extends Controller
{
    //

        /**
     * Display a listing of the resource.
     * @return Renderable
     * @return Response

     */
    public function index()
    {
        $users = User::whereNotIn('role', ['user', 'owner'])->get();
        return Inertia::render('Modules/User/Index', [
            'datas' =>  $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            // Validate incoming request
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:users,name',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8',
                'role' => 'required|in:owner,admin,customer,user', // Adjust roles as necessary
            ]);
    
            // Create a new user
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role' => $validatedData['role'],
                'avatar' => null, // You might want to handle avatar uploads separately
            ]);
    
            // Retrieve users excluding 'user' and 'owner' roles
            $users = User::whereNotIn('role', ['user', 'owner'])->get();
    
            return response()->json([
                'message' => 'User stored successfully!',
                'datas' => $users,
            ], 200);
        } catch (\Exception $e) {
            // Log the exception (optional but recommended)
            Log::error('User Store Error: ' . $e->getMessage());
    
            return response()->json([
                'message' => $e->getMessage(),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param User $page
     * @return Renderable
     *  @return Response
     */
    public function update(Request $request, User $user)
    {
        try {
            // Validate incoming request
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:users,name,' . $user->id,
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8',
                'role' => 'required|in:owner,admin,customer,web', // Adjust roles as necessary
            ]);
    
            // Prepare data for update
            $updateData = [
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'role' => $validatedData['role'],
            ];
    
            // Update password only if provided
            if (!empty($validatedData['password'])) {
                $updateData['password'] = Hash::make($validatedData['password']);
            }
    
            // Update the user
            $user->update($updateData);
    
            // Retrieve users excluding 'user' and 'owner' roles
            $users = User::whereNotIn('role', ['user', 'owner'])->get();
    
            return response()->json([
                'message' => 'User updated successfully!',
                'datas' => $users,
            ], 200);
        } catch (\Exception $e) {
            // Log the exception (optional but recommended)
            Log::error('User Update Error: ' . $e->getMessage());
    
            return response()->json([
                'message' => 'An error occurred while updating the user. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    

    /**
     * Remove the specified resource from storage.
     * @param User $page
     * @return Renderable
     */
    public function destroy($postId)
    {
        // Check if the Page exists
        $User = User::find($postId);
        if (!$User) {
            return response()->json(['message' => $postId], 404);
        }

        // Delete the Page
        $User->delete();

        $User = User::all();

        return response()->json(['message' => 'Block deleted successfully', 'datas' =>  $User,]);
    }

}
