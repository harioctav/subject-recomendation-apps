<?php

namespace App\Http\Controllers\Settings;

use App\DataTables\Settings\UserDataTable;
use App\Helpers\Enums\AccountStatusType;
use App\Helpers\Enums\RoleType;
use App\Models\User;
use App\Services\Role\RoleService;
use App\Services\User\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UserRequest;

class UserController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(
    protected UserService $userService,
    protected RoleService $roleService,
  ) {
    // 
  }

  /**
   * Display a listing of the resource.
   */
  public function index(UserDataTable $dataTable)
  {
    $status = AccountStatusType::toArray();
    $roleTypes = $this->roleService->getQuery()->pluck('name')->reject(function ($role) {
      return $role === RoleType::ADMINISTRATOR->value;
    });

    return $dataTable->render('settings.users.index', compact('status', 'roleTypes'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $roles = $this->roleService->getWhere(
      wheres: [
        'name' => RoleType::ADMINISTRATOR->value
      ],
      comparisons: '!='
    )->get();

    return view('settings.users.create', compact('roles'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(UserRequest $request)
  {
    $this->userService->handleStoreData($request);
    return redirect(route('users.index'))->withSuccess(trans('session.create'));
  }

  /**
   * Display the specified resource.
   */
  public function show(User $user)
  {
    return view('settings.users.show', compact('user'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(User $user)
  {
    $roles = $this->roleService->getWhere(
      wheres: [
        'name' => RoleType::ADMINISTRATOR->value
      ],
      comparisons: '!='
    )->get();

    return view('settings.users.edit', compact('roles', 'user'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UserRequest $request, User $user)
  {
    $this->userService->handleUpdateData($request, $user->id);

    if ($user->id != me()->id) {
      return redirect(route('users.show', me()->uuid))->withSuccess(trans('session.update'));
    }

    return redirect(route('users.index'))->withSuccess(trans('session.update'));
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(User $user)
  {
    $this->userService->handleDeleteData($user->id);
    return response()->json([
      'message' => trans('session.delete'),
    ]);
  }

  /**
   * Update the specified status account data from storage.
   */
  public function status(User $user)
  {
    $this->userService->handleChangeStatus($user->id);
    return response()->json([
      'message' => trans('session.status'),
    ]);
  }
}
