<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Product_seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class ProductController extends Controller
{
/**
* Display a listing of the resource.
*
* @return \Illuminate\Http\Response
*/
public function index()
{
$products = Product::join('product_sellers','product_sellers.product_id','=','products.id')->get();
return response()->json([
"success" => true,
"message" => "Products List",
"data" => $products
]);
}

/**
* Store a newly created resource in storage.
*
* @param  \Illuminate\Http\Request  $request
* @return \Illuminate\Http\Response
*/
public function store(Request $request)
{
$input = $request->all();
$validator = Validator::make($input, [
'name' => 'required',
'description' => 'required',
'image' => 'required'
]);
if($validator->fails()){
return $this->sendError('Validation Error.', $validator->errors());       
}
//$product = Product::create($input);
$product=new Product;
$product->name=$request->input('name');
$product->description=$request->input('description');
//$product->image=$request->input('image');
$file = $request->file('image');
    $filename = $file->getClientOriginalName();
    $path = $file->storeAs('public/images', $filename);

    // Create a new Image model and save it to the database
    $product->image = $path;
$product->save();

$product_seller=new Product_seller;
$product_seller->product_id=$product->id;
$product_seller->price=$request->input('price');
$product_seller->stock=$request->input('stock');
$product_seller->seller_id=Auth::id();
$product_seller->save();

return response()->json([
"success" => true,
"message" => "Product created successfully.",
"data" => $product
]);
}


//get details of a product

public function getproduct(Request $request)
{
    $product = Product::join('product_sellers','product_sellers.product_id','=','products.id')
    ->join('users','product_sellers.seller_id','=','users.id')
    ->select('products.name', 'products.image','products.description','product_sellers.price', 'product_sellers.stock','users.name as Seller Name','users.email as Seller Email')
    ->where('product_sellers.product_id','=', $request->product_id)->get();

return response()->json([
"success" => true,
"message" => "Product retrieved successfully.",
"data" => $product
]);
}

//get product list by seller

public function productlist()
{
$product = Product::join('product_sellers','product_sellers.product_id','=','products.id')->select('products.name', 'products.image','products.description','product_sellers.price', 'product_sellers.stock')->where('seller_id',Auth::id())->get();

return response()->json([
"success" => true,
"message" => "Product retrieved successfully.",
"data" => $product
]);
}

//get seller details for a product

public function sellerdetails(Request $request)
{
$product = Product::join('product_sellers','product_sellers.product_id','=','products.id')
->join('users','product_sellers.seller_id','=','users.id')->select('users.name as Seller Name', 'users.email as Seller Email')
->where('product_sellers.product_id','=', $request->product_id)->get();

return response()->json([
"success" => true,
"message" => "Product retrieved successfully.",
"data" => $product
]);
}

/**
* Update the specified resource in storage.
*
* @param  \Illuminate\Http\Request  $request
* @param  int  $id
* @return \Illuminate\Http\Response
*/
// public function update(Request $request, Product $product)
// {
// $input = $request->all();
// $validator = Validator::make($input, [
// 'name' => 'required',
// 'description' => 'required',
// 'image' => 'required'
// ]);
// if($validator->fails()){
// return $this->sendError('Validation Error.', $validator->errors());       
// }
// $product->name = $input['name'];
// $product->description = $input['description'];
// $product->save();
// return response()->json([
// "success" => true,
// "message" => "Product updated successfully.",
// "data" => $product
// ]);
// }
/**
* Remove the specified resource from storage.
*
* @param  int  $id
* @return \Illuminate\Http\Response
*/
// public function destroy(Product $product)
// {
// $product->delete();
// return response()->json([
// "success" => true,
// "message" => "Product deleted successfully.",
// "data" => $product
// ]);
// }
}