<?php namespace App\Http\Controllers\Admin;

/**
 * CouponController
 *
 * This is the controller of the coupons of the shop
 * @author Matthijs Neijenhuijs <matthijs@dutchbridge.nl>
 * @version 1.0
 */

use App\Http\Controllers\Controller;
use Dutchbridge\Repositories\CouponRepositoryInterface;

use Illuminate\Http\Request;
use Notification;

class CouponGroupController extends Controller
{
    public function __construct(
        Request $request,
        CouponRepositoryInterface $coupon
    ) {
        $this->coupon = $coupon;
        $this->request = $request;
    }

    public function index()
    {
        if ($this->request->wantsJson()) {

            $query = $this->coupon->getGroupModel()->select([\DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'coupon_group.id', 'coupon_group.title'])
            ->where('coupon_group.shop_id', '=', \Auth::guard('admin')->user()->selected_shop_id);

            $datatables = \Datatables::of($query)
            ->addColumn('action', function ($query) {
                $delete = \Form::deleteajax('/admin/coupon-group/'. $query->id, 'Delete', '', array('class'=>'btn btn-default btn-sm btn-danger'));
                $link = '<a href="/admin/coupon-group/'.$query->id.'/edit" class="btn btn-default btn-sm btn-success"><i class="entypo-pencil"></i>Edit</a>  '.$delete;
            
                return $link;
            });

            return $datatables->make(true);
        } else {
            return view('admin.coupon-group.index')->with('couponGroup', $this->coupon->selectAll());
        }
    }

    public function create()
    {
        return view('admin.coupon-group.create')->with(array());
    }

    public function store()
    {
        $result  = $this->coupon->create($this->request->all());

        if (isset($result->id)) {
            Notification::success('The coupon was inserted.');
            return redirect()->route('admin.coupon-group.index');
        }
        
        foreach ($result->errors()->all() as $error) {
            Notification::error($error);
        }
        
        return redirect()->back()->withInput();
    }

    public function edit($id)
    {
        return view('admin.coupon-group.edit')->with(array('couponGroup' => $this->coupon->findGroup($id)));
    }

    public function update($couponGroupId)
    {

        $result  = $this->coupon->updateGroupById($this->request->all(), $couponGroupId);

        if (isset($result->id)) {
            if ($this->request->get('seo')) {
                Notification::success('CouponGroup seo was updated.');
                return redirect()->route('admin.coupon-group.edit_seo', $couponGroupId);
            } elseif ($this->request->get('coupon-combination')) {
                Notification::success('CouponGroup combination leading attribute group was updated.');
                return redirect()->route('admin.coupon-group.{couponId}.coupon-combination.index', $couponGroupId);
            } else {
                Notification::success('CouponGroup was updated.');
                return redirect()->route('admin.coupon-group.edit', $couponGroupId);
            }
        }

        foreach ($result->errors()->all() as $error) {
            Notification::error($error);
        }
        
       
        return redirect()->back()->withInput();
    }

    public function destroy($id)
    {
        $result  = $this->coupon->destroyGroup($id);

        if ($result) {
            Notification::success('The coupon was deleted.');
            return redirect()->route('admin.coupon-group.index');
        }
    }
}