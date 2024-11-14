<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Actions\User\UserAction;
use App\Helpers\Response;
use App\Helpers\ResponseHelper;
use App\Http\Requests\Pagination\PaginationRequest;
use App\Http\Resources\User\UserDetailResource;
use App\Http\Resources\User\UserListResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class UserController extends Controller
{
    protected $userAction;

    public function __construct(UserAction $userAction)
    {
        $this->userAction = $userAction;
    }

    public function index(PaginationRequest $request)
    {
        try {
            $pagination = $request->paginationParams();
            $users = $this->userAction->getAllUsers($pagination['page'], $pagination['size']);

            return ResponseHelper::pagedResponse(
                UserListResource::collection($users),
                $users->currentPage(),
                $users->perPage(),
                $users->total()
            );
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR,
                $e->getMessage()
            );
        }
    }

    public function show($id)
    {
        try {
            $user = $this->userAction->getUser($id);
            return ResponseHelper::nonPagedResponse(
                new UserDetailResource($user),
                HttpResponse::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::errorResponse(
                HttpResponse::HTTP_NOT_FOUND,
                'User not found'
            );
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR,
                $e->getMessage()
            );
        }
    }


    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->userAction->createUser($request->validated());
            return ResponseHelper::nonPagedResponse($user, HttpResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(HttpResponse::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = $this->userAction->updateUser($id, $request->validated());
            return ResponseHelper::nonPagedResponse($user, HttpResponse::HTTP_OK);
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(HttpResponse::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->userAction->deleteUser($id);
            return ResponseHelper::nonPagedResponse("Successfully deleted user", HttpResponse::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(HttpResponse::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
