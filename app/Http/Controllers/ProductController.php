<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request){
        $keyword = $request->get('search');
        $perPage = 5;
        if(!empty($keyword)){
            $products = Product::where('name', 'LIKE', "%$keyword%")
                    ->orWhere('category','LIKE', "%$keyword%")
                    ->latest()->paginate($perPage);
        }else
            $products = Product::latest()->paginate($perPage);

            return view('products.index', ['products' =>$products])->with('i',(request()->input('page',1)-1)*5);
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request){

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $product = new Product;
        $file_name = time() . '.' . $request->image->getClientOriginalExtension();
        $request->image->move(public_path('images'), $file_name);


        $product->name = $request->name;
        $product->description = $request->description;
        $product->image = $file_name;
        $product->category = $request->category;
        $product->quantity = $request->quantity;
        $product->price = $request->price;

        $product->save();

        return redirect()->route('products.index')->with('success', 'Product Added Successfully');
    }

}
