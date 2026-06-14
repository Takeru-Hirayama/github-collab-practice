<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // パスワードハッシュ化に必須（事実）

class UserController extends Controller
{
    /**
     * ユーザー一覧を表示する
     */
    public function index()
    {
        // 【指摘①への対応】all() から paginate(20) に変更し、パフォーマンスを守ります（事実）
        $users = User::paginate(20);
        
        return view('users.index', ['users' => $users]);
    }

    /**
     * 新規ユーザーを登録する
     */
    public function store(Request $request)
    {
        // 【指摘②への対応】バリデーションを最上段に設置し、不正データを完全に遮断（事実）
        $validated = $request->validate([
            'name' => 'required|string|max:50',            // 必須、最大50文字
            'email' => 'required|string|email|unique:users', // 必須、メール形式、重複禁止
            'password' => 'required|string|min:8',         // 必須、最小8文字
        ]);

        // 【指摘③への対応】Hash::make() で生のパスワードを安全に暗号化（ハッシュ化）します（事実）
        $user = new User;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect('/users');
    }
}