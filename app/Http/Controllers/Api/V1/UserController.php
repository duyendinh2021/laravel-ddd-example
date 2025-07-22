<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserDetailResource;
use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Infrastructure\Criteria\ActiveUsersCriteria;
use App\Infrastructure\Criteria\UsersByRoleCriteria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Prettus\Validator\Exceptions\ValidatorException;

class UserController extends Controller 
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * Display a listing of users
     */
    public function index(Request $request): JsonResponse
    {
        // RequestCriteria will automatically apply filters from query params
        // URL: /api/v1/users?role=admin&status=active&search=john&per_page=10
        
        if ($request->has('role')) {
            $this->userRepository->pushCriteria(
                new UsersByRoleCriteria($request->role)
            );
        }
        
        if ($request->boolean('only_active')) {
            $this->userRepository->pushCriteria(new ActiveUsersCriteria());
        }
        
        $users = $this->userRepository->paginate(
            $request->get('per_page', 15)
        );
        
        return response()->json($users);
    }

    /**
     * Store a new user
     */
    public function store(RegisterUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userRepository->create($request->validated());
            return response()->json($user, 201);
        } catch (ValidatorException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->getMessageBag()
            ], 422);
        }
    }

    /**
     * Show user detail
     */
    public function show(string $id): JsonResponse  
    {
        $user = $this->userRepository->find($id);
        return response()->json($user);
    }

    /**
     * Update user
     */
    public function update(string $id, UpdateUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userRepository->update($request->validated(), $id);
            return response()->json($user);
        } catch (ValidatorException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->getMessageBag()
            ], 422);
        }
    }

    /**
     * Delete user
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->userRepository->delete($id);
        
        return response()->json([
            'message' => 'User deleted successfully',
            'deleted' => $deleted
        ]);
    }

    /**
     * Login user
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        // Implementation for authentication
        // This would typically be handled by AuthenticationService
        return response()->json([
            'message' => 'Login endpoint - to be implemented with AuthenticationService'
        ]);
    }
}