<?php

namespace App\Http\Controllers\Auth;



use App\User;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Services\CheckExtensionServices; //拡張子を判別するファイル
use App\Services\FileUploadServices;
use Illuminate\Http\Request; //データ受け渡し処理時のRequestを使うためshino
use Illuminate\Support\Facades\Auth;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'image' => ['file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2000'],
            'self_introduction' => ['string', 'max:255'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(Request $data)
    {
    
        // ddd($data);
        //引数 $data から name='image'を取得(アップロードするファイル情報)
        $imageFile = $data['image'];
        // $imageFile = $data['image2'];

        $list = FileUploadServices::fileUpload($imageFile);

        list($extension, $fileNameToStore, $fileData) = $list;

        $data_url = CheckExtensionServices::checkExtension($fileData, $extension);

        //画像アップロード(Imageクラス makeメソッドを使用)
        $image = Image::make($data_url);

        //画像を横400px, 縦400pxにリサイズし保存
        $image->resize(400, 400)->save(storage_path() . '/app/public/images/' . $fileNameToStore);
        // ddd($data);
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'age' => $data['age'],
            'password' => Hash::make($data['password']),
            'self_introduction' => $data['self_introduction'],
            'sex' => $data['sex'],
            'school' => $data['school'],
            'hobby1' => $data['hobby1'],
            'hobby2' => $data['hobby2'],
            'hobby3' => $data['hobby3'],
            'hobby4' => $data['hobby4'],
            'hobby5' => $data['hobby5'],
            'img_name' => $fileNameToStore,
        ]);
        // Eメールとパスワードを基にHomeへリダイレクト
        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return redirect()->intended('home');
        }
    }

    // 会員登録でページ遷移しながらデータは保持する関数　ここからshino
    public function name()
    // public function name(Request $request)
    {
        return view('auth.name');
    }
    public function birthday(Request $request)
    {
        // ddd($request);
        //ポストデータすべての取得
        $post_data = $request;
        return view('auth.birthday', compact('post_data'));
    }
    public function gender(Request $request)
    {
        // ddd($request);
        $post_data = $request;
        return view('auth.gender', compact('post_data'));
    }
    public function school(Request $request)
    {
        // ddd($request);
        $post_data = $request;
        return view('auth.school', compact('post_data'));
    }
    public function hobby(Request $request)
    {
        // ddd($request);
        $post_data = $request;
        return view('auth.hobby', compact('post_data'));
    }
    public function picture(Request $request)
    {
        // ddd($request);
        $post_data = $request;
        return view('auth.picture', compact('post_data'));
    }
    // 会員登録でページ遷移しながらデータは保持する関数　ここまで

}
