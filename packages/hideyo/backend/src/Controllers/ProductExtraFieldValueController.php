<?php namespace App\Http\Controllers\Admin;

/**
 * ProductController
 *
 * This is the controller of the product weight types of the shop
 * @author Matthijs Neijenhuijs <matthijs@dutchbridge.nl>
 * @version 1.0
 */

use App\Http\Controllers\Controller;


use Dutchbridge\Repositories\ProductExtraFieldValueRepositoryInterface;
use Dutchbridge\Repositories\ProductRepositoryInterface;
use Dutchbridge\Repositories\ExtraFieldRepositoryInterface;

use \Request;
use \Notification;

class ProductExtraFieldValueController extends Controller
{
    public function __construct(
        ProductExtraFieldValueRepositoryInterface $productExtraFieldValue,
        ProductRepositoryInterface $product,
        ExtraFieldRepositoryInterface $extraField
    ) {
        $this->productExtraFieldValue = $productExtraFieldValue;
        $this->product = $product;
        $this->extraField = $extraField;
    }

    public function index($productId)
    {
        $product = $this->product->find($productId);
        $extraFieldsData = $this->productExtraFieldValue->selectAllByProductId($productId);
        $newExtraFieldsData = array();
        if ($extraFieldsData->count()) {
            foreach ($extraFieldsData as $row) {
                $newExtraFieldsData[$row->extra_field_id] = array(
                    'value' => $row->value,
                    'extra_field_default_value_id' => $row->extra_field_default_value_id
                );
            }
        }
   
        return view('admin.product-extra-field-value.index')->with(
            array(
                'extraFields' =>  $this->extraField->selectAllByAllProductsAndProductCategoryId($product->product_category_id),
                'product' => $this->product->find($productId),
                'populateData' => $newExtraFieldsData
            )
        );
    }

    public function store($productId)
    {
        $result  = $this->productExtraFieldValue->create(Request::all(), $productId);
 
        if (isset($result->id)) {
            Notification::success('The product extra fields are updated.');
            return redirect()->route('admin.product.{productId}.product-extra-field-value.index', $productId);
        }
          
        return redirect()->back()->withInput();
    }

    public function edit($productId, $id)
    {
        $product = $this->product->find($productId);
        return view('admin.product-extra-field-value.edit')->with(array('productExtraFieldValue' => $this->productExtraFieldValue->find($id), 'product' => $product));
    }

    public function update($productId, $id)
    {
        $result  = $this->productExtraFieldValue->updateById(Request::all(), $productId, $id);

        if (isset($result->id)) {
            return redirect()->back()->withInput()->withErrors($result->errors()->all());
        }
        
        Notification::success('The product image is updated.');
        return redirect()->route('admin.product.{productId}.images.index', $productId);
    }

    public function destroy($productId, $id)
    {
        $result  = $this->productExtraFieldValue->destroy($id);

        if ($result) {
            Notification::success('The product image is deleted.');
            return redirect()->route('admin.product.{productId}.images.index', $productId);
        }
    }
}