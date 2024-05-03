<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductColor;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // lấy all
        // $products = Product::with('productDetail')->get();
        // return response()->json($products);
        $perPage = 16;
        $products = Product::paginate($perPage);
        $responseData = [
            'data' => $products,
        ];

        return response()->json($responseData);

        // lấy 1 bảng
        // return Product::select('product_name','product_desc', 'product_content', 'product_price','product_sale', 'product_image', 'product_status')->get();
    
        // lấy 1 số trường
        // $products = Product::with(['productDetail' => function ($query) {
        //     $query->select('product_id', 'product_cpu'); 
        // }])->get();
        // return response()->json($products);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create(Request $request)
    // {
    //     // return Product::create($request -> all());
    // }
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Create the product if validation passes
        $product = Product::create([
            'category_id' => $request->input('category_id'),
            'product_sale' => $request->input('product_sale'),
            'product_name' => $request->input('product_name'),
            'product_content' => $request->input('product_content'),
            'product_image' => $request->input('product_image'),
            'product_status' => $request->input('product_status'),
        ]);

        // Create product details
        $productDetail = ProductDetail::create([
            'product_id' => $product->product_id,
            'product_ram' => $request->input('product_ram'),
            'hard_drive' => $request->input('hard_drive'),
            'product_card' => $request->input('product_card'),
            'desktop' => $request->input('desktop'),
        ]);

        // Create product colors
        foreach ($request->input('colors') as $color) {
            // Check if color already exists
            $existingColor = Color::where('color_name', $color['color_name'])->first();

            if (!$existingColor) {
                // Create the color if it doesn't exist
                $createdColor = Color::create([
                    'color_name' => $color['color_name'],
                ]);

                ProductColor::create([
                    'product_id' => $product->product_id,
                    'color_id' => $createdColor->color_id,
                    'quantity' => $color['quantity'],
                    'product_price' => $color['price'],
                ]);
            } else {
                // Use the existing color
                ProductColor::create([
                    'product_id' => $product->product_id,
                    'color_id' => $existingColor->color_id,
                    'quantity' => $color['quantity'],
                    'product_price' => $color['price'],
                ]);
            }
        }

        return response()->json(['message' => 'Product created successfully'], 201);
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json($product);
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
        // Validate dữ liệu đầu vào
        $request->validate([
            'category_id' => 'exists:categories,category_id',
            'product_sale' => 'numeric',
            'product_name' => 'string|max:255',
            'product_price' => 'numeric',
            // 'product_content' => 'string',
            'product_image' => 'string',
            'product_status' => 'in:1,0',
        ]);

        // Update thông tin của bảng products
        $product->update([
            'category_id' => $request->input('category_id'),
            'product_sale' => $request->input('product_sale'),
            'product_name' => $request->input('product_name'),
            'product_content' => $request->input('product_content'),
            'product_image' => $request->input('product_image'),
            'product_status' => $request->input('product_status'),
        ]);

        // Update hoặc tạo mới thông tin của bảng product_details
        $product->productDetail()->updateOrCreate(
            ['product_id' => $product->product_id],
            [
                'product_ram' => $request->input('product_ram'),
                'hard_drive' => $request->input('hard_drive'),
                'product_card' => $request->input('product_card'),
                'desktop' => $request->input('desktop'),
            ]
        );

        // Create product colors
        foreach ($request->input('colors') as $color) {
            // Check if color already exists
            $existingColor = Color::where('color_name', $color['color_name'])->first();

            if (!$existingColor) {
                // Create the color if it doesn't exist
                $createdColor = Color::create([
                    'color_name' => $color['color_name'],
                ]);

                ProductColor::create([
                    'product_id' => $product->product_id,
                    'color_id' => $createdColor->color_id,
                    'quantity' => $color['quantity'],
                    'product_price' => $color['price'],
                ]);
            } else {
                // Use the existing color
                // Check if the product color already exists for this product and color combination
                $existingProductColor = ProductColor::where('product_id', $product->product_id)
                                                    ->where('color_id', $existingColor->color_id)
                                                    ->first();

                if ($existingProductColor) {
                    // If the product color already exists, update the quantity and price
                    $existingProductColor->update([
                        'quantity' => $color['quantity'],
                        'product_price' => $color['price'],
                    ]);
                } else {
                    // If the product color doesn't exist, create it
                    ProductColor::create([
                        'product_id' => $product->product_id,
                        'color_id' => $existingColor->color_id,
                        'quantity' => $color['quantity'],
                        'product_price' => $color['price'],
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Product updated successfully']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Lấy product_id của sản phẩm
        $productId = $product->product_id;

        // Xóa tất cả các bản ghi liên quan trong các bảng
        $product->productDetail()->delete();
        $product->productColors()->delete();
        //Thêm các bảng liên quan khác nếu có

        // Xóa sản phẩm chính
        $product->delete();

        // Trả về phản hồi với thông báo
        return response()->json(['message' => 'Product and related records deleted successfully'], 200);
    }
    
    public function searchByName(Request $request)
    {
        try {
            // Validate the search query
            $request->validate([
                'product_name' => 'required|string|max:255',
            ]);
    
            $products = Product::with('productColors')->where('product_name', 'like', '%' . $request->input('product_name') . '%')->get();
    
            if ($products->isEmpty()) {
                return response()->json(['message' => 'No products found for the given search query', 'data' => []]);
            }
    
            return response()->json(['data' => $products]);
        } catch (\Exception $e) {
            // Handle exceptions if any
            return response()->json(['message' => 'Error searching products', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function getProductsByCategory(Request $request, $category_id)
    {
        try {
            // Validate the category ID
            $category = Category::find($category_id);

            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }

            $perPage = $request->input('perPage', 16); // Sử dụng giá trị mặc định là 16 nếu không có giá trị

            // Lấy trang hiện tại từ tham số 'page' trong URL, mặc định là 1 nếu không có
            $currentPage = $request->input('page', 1);

            // Lấy các sản phẩm liên quan đến danh mục và phân trang kết quả
            $products = $category->products()
            ->whereHas('category', function ($query) {
                $query->where('category_status', '!=', 0);
            })
            ->with('productColors')
            ->paginate($perPage, ['*'], 'page', $currentPage);

            if ($products->isEmpty()) {
                return response()->json(['message' => 'No products found for the given category', 'data' => []]);
            }
            $responseData = [
                'category_name' => $category->category_name,
                'data' => $products,
            ];

            // return response()->json(['data' => $products]);
            return response()->json($responseData);
        } catch (\Exception $e) {
            // Handle exceptions if any
            return response()->json(['message' => 'Error retrieving products by category', 'error' => $e->getMessage()], 500);
        }
    }

    public function getProductDetail($product_id)
    {
        try {
            $product = Product::with('productDetail')->with('productColors')->findOrFail($product_id);

            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }
    
            $colors = Color::get();

            $responseData = [
                'data' => $product,
                'colors' => $colors,
            ];
            return response()->json($responseData);
        } catch (\Exception $e) {
            return response()->json(['data' => []]);
        }
    }

    public function getFourProductsByCategory(Request $request, $category_id, $product_id)
    {
        try {
            // Validate the category ID
            $category = Category::find($category_id);

            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }

            // Exclude the provided product_id from the query and limit to 4 results
            $products = $category->products()->with('productColors')
                ->where('product_id', '!=', $product_id)
                ->whereHas('category', function ($query) {
                    $query->where('category_status', '!=', 0);
                })
                ->limit(4)
                ->get();

            if ($products->isEmpty()) {
                return response()->json(['message' => 'No products found for the given category'], 404);
            }

            return response()->json(['data' => $products]);
        } catch (\Exception $e) {
            // Handle exceptions if any
            return response()->json(['message' => 'Error retrieving products by category', 'error' => $e->getMessage()], 500);
        }
    }

    public function getLatestProducts(Request $request)
    {
        try {
            // Retrieve the latest products without considering a specific category
            $latestProducts = Product::with('productColors')->orderBy('created_at', 'desc')
            ->whereHas('category', function ($query) {
                $query->where('category_status', '!=', 0);
            })
            ->take(8)->get();
    
            if ($latestProducts->isEmpty()) {
                return response()->json(['message' => 'No products found'], 404);
            }
    
            return response()->json(['data' => $latestProducts]);
        } catch (\Exception $e) {
            // Handle exceptions if any
            return response()->json(['message' => 'Error retrieving latest products', 'error' => $e->getMessage()], 500);
        }
    }

    public function getRandomEightProducts()
    {
        try {
            $products = Product::with('productColors')->inRandomOrder()
            ->whereHas('category', function ($query) {
                $query->where('category_status', '!=', 0);
            })
            ->take(8)
            ->get();
               
            if ($products->isEmpty()) {
                return response()->json(['message' => 'No products found'], 404);
            }
    
            return response()->json(['data' => $products]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving products', 'error' => $e->getMessage()], 500);
        }
    }

    public function getAllProductsExceptInactiveCategories()
    {
        $perPage = 16;
        $products =  Product::with('productDetail')->with('productColors')->whereHas('category', function ($query) {
            $query->where('category_status', '!=', 0);
        })->paginate($perPage);

        $responseData = [
            'data' => $products,
        ];

        return response()->json($responseData);
    }

    public function updateProductStatus(Request $request, Product $product)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'product_status' => 'required|in:0,1',
        ]);

        $product->update([
            'product_status' => $request->input('product_status'),
        ]);

        return response()->json(['message' => 'Product status updated successfully']);
    }
}
