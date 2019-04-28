<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\Category;
use App\Models\Link;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use Illuminate\Support\Facades\Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * 论坛首页
     * @param Request $request
     * @param Topic $topic
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function index(Request $request,Topic $topic, User $user, Link $link)
	{
	    //$request->order 是获取 URI http://larabbs.test/topics?order=recent 中的 order 参数。
		$topics = $topic->withOrder($request->order)->paginate(20);
		$active_users = $user->getActiveUsers();
		$links = $link->getAllCached();

//		dd($active_users);
		return view('topics.index', compact('topics','active_users','links'));
	}

    public function show(Request $request, Topic $topic, User $user)
    {
        // URL 矫正
        if ( ! empty($topic->slug) && $topic->slug != $request->slug) {
            return redirect($topic->link(), 301);
        }

        return view('topics.show', compact('topic','user'));
    }

    /**
     * 新建帖子
     * @param Topic $topic
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function create(Topic $topic)
	{
	    $categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function store(TopicRequest $request,Topic $topic)
	{


		$topic->fill($request->all());

		$topic->user_id = Auth::id();

		$topic->save();

        return redirect()->to($topic->link())->with('success', '成功创建主题！');
	}

    /**
     * 编辑页
     * @param Topic $topic
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
        $categories = Category::all();

		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

        return redirect()->to($topic->link())->with('success', '更新成功！');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

        return redirect()->route('topics.index')->with('success', '成功删除！');
	}

	public function uploadImage(Request $request, ImageUploadHandler $uploader)
    {
        $data = [
            'success' => false,
            'msg' => '上传失败',
            'file_path' => ''
        ];

        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload_file){
            //保存图片到本地
            $result = $uploader->save($request->upload_file,'topics',\Auth::id(),1024);

            if ($result) {
                $data['file_path'] = $request['path'];
                $data['msg'] = "上传成功！";
                $data['succes'] = true;
            }
        }

        return $data;
    }
}