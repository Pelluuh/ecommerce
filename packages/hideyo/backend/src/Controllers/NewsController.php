<?php namespace App\Http\Controllers\Admin;

/**
 * CouponController
 *
 * This is the controller of the newss of the shop
 * @author Matthijs Neijenhuijs <matthijs@dutchbridge.nl>
 * @version 1.0
 */

use App\Http\Controllers\Controller;
use Dutchbridge\Repositories\NewsRepositoryInterface;
use Dutchbridge\Repositories\TaxRateRepositoryInterface;
use Dutchbridge\Repositories\PaymentMethodRepositoryInterface;
use Dutchbridge\Repositories\NewsGroupRepositoryInterface;

use Illuminate\Http\Request;
use Notification;

class NewsController extends Controller
{
    public function __construct(Request $request, NewsRepositoryInterface $news)
    {
        $this->request = $request;
        $this->news = $news;
    }

    public function index()
    {
        if ($this->request->wantsJson()) {

            $query = $this->news->getModel()->select(
                [
                \DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                'news.id',
                'news.title', 'news_group_id', 'news_group.title as newstitle']
            )->where('news.shop_id', '=', \Auth::guard('admin')->user()->selected_shop_id)


            ->with(array('newsGroup'))        ->leftJoin('news_group', 'news_group.id', '=', 'news.news_group_id');
            
            $datatables = \Datatables::of($query)
            ->filterColumn('title', function ($query, $keyword) {

                $query->where(
                    function ($query) use ($keyword) {
                        $query->whereRaw("news.title like ?", ["%{$keyword}%"]);
                        ;
                    }
                );
            })
            ->addColumn('newsgroup', function ($query) {
                return $query->newstitle;
            })

            ->addColumn('action', function ($query) {
                $delete = \Form::deleteajax('/admin/news/'. $query->id, 'Delete', '', array('class'=>'btn btn-default btn-sm btn-danger'), $query->title);
                $link = '<a href="/admin/news/'.$query->id.'/edit" class="btn btn-default btn-sm btn-success"><i class="entypo-pencil"></i>Edit</a>  '.$delete;
            
                return $link;
            });

            return $datatables->make(true);

        } else {
            return view('admin.news.index')->with('news', $this->news->selectAll());
        }
    }

    public function create()
    {
        return view('admin.news.create')->with(array('groups' => $this->news->selectAllGroups()->lists('title', 'id')->toArray()));
    }

    public function store()
    {
        $result  = $this->news->create($this->request->all());

        if (isset($result->id)) {
            Notification::success('The news was inserted.');
            return redirect()->route('admin.news.index');
        }
        
        foreach ($result->errors()->all() as $error) {
            Notification::error($error);
        }
        
        return redirect()->back()->withInput();
    }

    public function edit($id)
    {
    
        return view('admin.news.edit')->with(array('news' => $this->news->find($id), 'groups' => $this->news->selectAllGroups()->lists('title', 'id')->toArray()));
    }

    public function reDirectoryAllImages()
    {
        $this->newsImage->reDirectoryAllImagesByShopId(\Auth::guard('admin')->user()->selected_shop_id);

        return redirect()->route('admin.news.index');
    }

    public function refactorAllImages()
    {
        $this->newsImage->refactorAllImagesByShopId(\Auth::guard('admin')->user()->selected_shop_id);

        return redirect()->route('admin.news.index');
    }

    public function editSeo($id)
    {
        return view('admin.news.edit_seo')->with(array('news' => $this->news->find($id)));
    }
    
    public function update($id)
    {
        $result  = $this->news->updateById($this->request->all(), $id);

        if (isset($result->id)) {
            Notification::success('The news was updated.');
            return redirect()->route('admin.news.index');
        }
        
        foreach ($result->errors()->all() as $error) {
            Notification::error($error);
        }
        
        return redirect()->back()->withInput();
    }

    public function destroy($id)
    {

        $result  = $this->news->destroy($id);

        if ($result) {
            Notification::success('The news was deleted.');
            return redirect()->route('admin.news.index');
        }
    }
}