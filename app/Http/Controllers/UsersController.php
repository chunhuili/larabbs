<?php

namespace App\Http\Controllers;


use App\Handlers\ImageUploadHandler;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }

    /**
     * 显示页面
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('user.show',compact('user'));
    }

    /**
     * 个人资料更新页面
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('user.edit',compact('user'));
    }

    /**
     * 个人资料更新功能
     * @param UserRequest $request
     * @param ImageUploadHandler $uploader
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request,ImageUploadHandler $uploader, User $user)
    {
        $this->authorize('update', $user);

        $data = $request->all();

        if ($request->avatar) {
            $result = $uploader->save($request->avatar,'avatars',$user->id,362);
            if ($result) {
                $data['avatar'] = $result['path'];
            }
        }
        $user->update($data);

        return redirect()->route('users.show',$user->id)->with('success','个人资料更新成功');
    }
}
