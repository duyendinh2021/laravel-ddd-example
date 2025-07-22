<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Commands\RegisterUserCommand;
use App\Application\Commands\UpdateUserCommand;
use App\Application\Commands\DeactivateUserCommand;
use App\Application\Queries\GetUserQuery;
use App\Application\Queries\GetUsersQuery;
use App\Application\Services\UserApplicationService;
use App\Application\Services\AuthenticationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserDetailResource;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class UserController extends Controller
{
    public function __construct(
        private UserApplicationService $userApplicationService,
        private AuthenticationService $authenticationService
    ) {}

    /**
     * Display a listing of users.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = new GetUsersQuery(
            role: $request->get('role'),
            isActive: $request->boolean('is_active'),
            isVerified: $request->boolean('is_verified'),
            page: (int)$request->get('page', 1),
            perPage: (int)$request->get('per_page', 10)
        );

        $users = $this->userApplicationService->getUsers($query);

        return UserResource::collection($users);
    }

    /**
     * Store a newly created user.
     */
    public function store(RegisterUserRequest $request): JsonResponse
    {
        try {
            $command = new RegisterUserCommand(
                username: $request->validated('username'),
                email: $request->validated('email'),
                password: $request->validated('password'),
                firstName: $request->validated('first_name'),
                lastName: $request->validated('last_name'),
                phone: $request->validated('phone'),
                role: $request->validated('role', 'user')
            );

            $user = $this->userApplicationService->registerUser($command);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => new UserResource($user),
            ], 201);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified user.
     */
    public function show(int $id): JsonResponse
    {
        $query = new GetUserQuery($id);
        $user = $this->userApplicationService->getUser($query);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new UserDetailResource($user),
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        try {
            $command = new UpdateUserCommand(
                userId: $id,
                firstName: $request->validated('first_name'),
                lastName: $request->validated('last_name'),
                phone: $request->validated('phone'),
                profileImageUrl: $request->validated('profile_image_url'),
                timezone: $request->validated('timezone'),
                language: $request->validated('language')
            );

            $user = $this->userApplicationService->updateUser($command);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => new UserResource($user),
            ]);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified user (soft delete via deactivation).
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $command = new DeactivateUserCommand($id, 'Account deleted via API');
            $user = $this->userApplicationService->deactivateUser($command);

            return response()->json([
                'success' => true,
                'message' => 'User deactivated successfully',
                'data' => new UserResource($user),
            ]);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Deactivation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Authenticate user and return token.
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        try {
            $user = $this->authenticationService->authenticate(
                $request->validated('email'),
                $request->validated('password')
            );

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            // Create a Sanctum token for API authentication
            $userModel = UserModel::where('email', $user->getEmail()->value())->first();
            $token = $userModel->createToken('api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout user and revoke token.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout successful',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}