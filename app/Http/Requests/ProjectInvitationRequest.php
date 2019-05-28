<?php

namespace App\Http\Requests;

// use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProjectInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('update', $this->route('project')); //コントローラのようにauthorize()は使えないのでGateファサードを使う。allows('Policyのメソッド', 'メソッドが扱う変数')。$this->route('project')で、web.phpでルーティングしているワイルドカード{project}を指す。
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'email' => ['required', 'exists:users,email'] //Ruleファサードを使って次のように書くこともできる。
            'email' => ['required', Rule::exists('users', 'email')]
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => 'The user you are inviting must have a birdboard account.' //custom error message.existsという検証ルールでエラーが起きた際のメッセージを指定。
        ];
    }
}
