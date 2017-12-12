<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Mail;
use Illuminate\Http\Request;
use BrowserDetect;
use Hideyo\Repositories\ContentRepositoryInterface;
use Hideyo\Repositories\ProductTagGroupRepositoryInterface;
use Hideyo\Repositories\ShopRepositoryInterface;
use Validator;
use Notification;


class BasicController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        ProductTagGroupRepositoryInterface $productTagGroup, 
        ShopRepositoryInterface $shop, 
        ContentRepositoryInterface $content)
    {
        $this->request = $request;
        $this->content = $content;
        $this->shop = $shop;
        $this->productTagGroup = $productTagGroup;

    }

    public function index()
    {        
        $populairProducts = $this->productTagGroup->selectAllByTagAndShopId(config()->get('app.shop_id'), 'home-populair');
        return view('frontend.basic.index')->with(array('populairProducts' => $populairProducts));
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function getContact()
    {
        return view('frontend.basic.contact');
    }


    public function postContact(Request $request)
    {
        // create the validation rules ------------------------
        $rules = array(
            'email'            => 'required'
        );

        $input = $request->all();
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            // get the error messages from the validator
            $messages = $validator->messages();
            // redirect our user back to the form with the errors from the validator
            Notification::error($validator->errors()->all());  
        }

        Mail::send('frontend.email.contact', ['data' => $input], function ($m) use ($input) {

            $m->from('info@dutchbridge.nl', 'Dutchbridge');
            $m->replyTo($input['email'], $input['name']);

            $m->to('info@dutchbridge.nl')->subject(': thnx for your contact!');
        });
      
        Notification::success(trans('thnx for your contact!'));
        return redirect()->route('contact');  
    }

}