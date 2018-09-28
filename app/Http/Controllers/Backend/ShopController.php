<?php namespace App\Http\Controllers\Backend;

/**
 * ShopController
 *
 * This is the controller of the shops
 * @author Matthijs Neijenhuijs <matthijs@hideyo.io>
 * @version 0.1
 */

use App\Http\Controllers\Controller;
use Hideyo\Ecommerce\Framework\Repositories\ShopRepositoryInterface;
use Illuminate\Http\Request;
use Notification;
use Datatables;
use Form;
use Hideyo\Ecommerce\Framework\Services\Shop\ShopFacade as ShopService;

class ShopController extends Controller
{
    public function __construct(
        Request $request, 
        ShopRepositoryInterface $shop)
    {
        $this->shop = $shop;
        $this->request = $request;
    }

    public function index()
    {
        if ($this->request->wantsJson()) {

            $query = ShopService::getModel()
            ->select(['id', 'title', 'logo_file_name']);
            $datatables = Datatables::of($query)

            ->addColumn('action', function ($query) {
                $deleteLink = Form::deleteajax(url()->route('shop.destroy', $query->id), 'Delete', '', array('class'=>'btn btn-default btn-sm btn-danger'));
                $links = '<a href="'.url()->route('shop.edit', $query->id).'" class="btn btn-default btn-sm btn-success"><i class="entypo-pencil"></i>Edit</a>  '.$deleteLink;
                return $links;
            })

            ->addColumn('image', function ($query) {
                if ($query->logo_file_name) {
                    return '<img src="http://shop.brulo.nl/files/'.$query->id.'/logo/'.$query->logo_file_name.'"  />';
                }
            });

            return $datatables->make(true);
        }
        
        return view('backend.shop.index')->with('shop', ShopService::selectAll());
    }

    public function create()
    {
        return view('backend.shop.create');
    }

    public function store()
    {
        $result  = ShopService::create($this->request->all());

        if (isset($result->id)) {
            Notification::success('The shop was inserted.');
            return redirect()->route('shop.index');
        }
        
        foreach ($result->errors()->all() as $error) {
            Notification::error($error);
        }
        
        return redirect()->back()->withInput();
    }

    public function edit($shopId)
    {
        return view('backend.shop.edit')->with(array('shop' => ShopService::find($shopId)));
    }

    public function update($shopId)
    {
        $result  = ShopService::updateById($this->request->all(), $shopId);

        if (isset($result->id)) {
            Notification::success('The shop was updated.');
            return redirect()->route('shop.index');
        }
        
        foreach ($result->errors()->all() as $error) {
            Notification::error($error);
        }
        
        return redirect()->back()->withInput();
    }

    public function destroy($shopId)
    {
        $result  = ShopService::destroy($shopId);

        if ($result) {
            Notification::success('The shop was deleted.');
            return redirect()->route('shop.index');
        }
    }
}
