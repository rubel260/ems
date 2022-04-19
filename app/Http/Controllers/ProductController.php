<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        $products= Product::paginate(3);
        return view('products', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product= new Product();
        $product->name= $request->has('name')? $request->get('name'):'';
        $product->price= $request->has('price')? $request->get('price'):'';
        $product->amount= $request->has('amount')? $request->get('amount'):'';
        $product->details= $request->has('details')? $request->get('details'):'';
        $product->category_id= $request->has('category_id')? $request->get('category_id'):'';

        $product->is_active= 1;

        if($request->hasFile('images')){
            $files = $request->file('images');

            $imageLocation= array();
            $i=0;
            foreach ($files as $file){
                $extension = $file->getClientOriginalExtension();
                $fileName= 'product_'. time() . ++$i . '.' . $extension;
                $location= '/images/uploads/';
                $file->move(public_path() . $location, $fileName);
                $imageLocation[]= $location. $fileName;
            }

            $product->image= implode('|', $imageLocation);
            $product->save();
            return back()->with('success', 'Product Successfully Saved!');
        } else{
            return back()->with('error', 'Product was not saved Successfully!');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $images= explode('|' , $product->image);
        $related_products= Product::where('category_id', $product->category_id)->where('id', '!=', $product->id)->limit(3)->get();
        return view('product_details' , compact('product' , 'images', 'related_products'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
    
    public function addProduct(){
        $products= Product::all();
        $returnProducts= array();

        foreach ($products as $product){
            $images= explode('|', $product->image);

            $returnProducts[] = [
               'name'=> $product->name,
               'price'=> $product->price,
               'amount'=> $product->amount,
               'image'=> $images[0]
            ];

        }

        return view('add_product', compact('returnProducts'));
    }

    public function addToCart(Request $request){
        $id= $request->has('pid')? $request->get('pid'): '';
        $name= $request->has('name')? $request->get('name'): '';
        $quantity= $request->has('quantity')? $request->get('quantity'): '';
        $size= $request->has('size')? $request->get('size'): '';
        $price= $request->has('price')? $request->get('price'): '';

        $images= Product::find($id)->image;
        $image= explode('|', $images)[0];
        $cart= Cart::content()->where('id', $id)->first();

        if(isset($cart)&& $cart!=null){
            $quantity= ((int)$quantity + (int)$cart->qty);
            $total= (int)$quantity * (int)$price;
            Cart::update($cart->rowId, ['qty'=>$quantity, 'options'=>['size' => $size, 'image'=> $image, 'total'=> $total]]);
        }else{
            $total= (int)$quantity * (int)$price;
            Cart::add($id, $name, $quantity, $price, ['size' => $size, 'image'=> $image, 'total'=> $total]);
        }
        return redirect('/products')->with('success','Product add to your cart!');

    }
   
    public function viewCart(){
        $carts= Cart::content();
        $subTotal= Cart::subtotal();

        return view('cart', compact('carts', 'subTotal'));
    }
    public function removeItem($rowId){
        Cart::remove($rowId);
        return redirect('/cart')->with('success', 'Product Removed Successfully!');
    }

    public function home(){
        $featured_products= Product::orderBy('price', 'desc')->limit(4)->get();
        $latest_products= Product::orderBy('created_at', 'desc')->limit(2)->get();

        return view('welcome', compact('featured_products', 'latest_products'));

    }

    public function validateAmount(Request $request){
        $id= $request->has('pid')? $request->get('pid'):'';
        $product_amount= Product::find($id)->amount;

        if($request->has('qty') && $request->get('qty')>$product_amount){
            return json_encode([
                'success'=>true,
                'message'=>'Product quantity must me less than '. $product_amount
            ]);
        } else{
            return json_encode([
                'success'=>false
            ]);
        }
    }


}
