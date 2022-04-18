<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\ControllerTrait;
use App\Http\Traits\ResponseTrait;
use App\Http\Traits\UploadFileTrait;
use App\Http\Traits\GetImageTrait;
use Illuminate\Http\Request;
use App\Models\PhotoProduct;
use App\Models\Product;
use Storage;

class ProductController extends Controller
{
    use ResponseTrait,ControllerTrait,UploadFileTrait,GetImageTrait;

    /**
	 * @var Product
	 */
	protected $table;

    public function __construct(Product $table)
	{
		$this->table       = $table;
		$this->module_name = 'Product';
        $this->middleware(function ($request, $next) {
            $this->whereCondition = ['name:=:'.$request->condition_usage];
            $this->whereWithCondition = [];
            return $next($request);
        });
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
			$result = $this->customTable($request);
			return $this->jsonResponse($result);
		} catch (\Exception $e) {
			report($e);
			return $this->jsonResponse("Internal Error Creating".$e);
		}
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        $auth = \Auth::user();

        try {
			\DB::beginTransaction();

            $product = New Product;
            $product->id_type_product = $request->id_type_product;
            $product->price = $request->price;
            $product->id_retail = 1;
            $product->name_product = $request->name_product;
            $product->slug = \Str::slug($request->name_product);
            $product->save();

            if($request->picture){
                $photoProduct = new PhotoProduct;
                $photoProduct->picture = $this->uploadFile($request->picture,"photo");
                $photoProduct->save();
            }

            \DB::commit();
            return $this->jsonResponse($product ,201);
		} catch (\Exception $e) {
			\DB::rollBack();
			report($e);
			return $this->jsonResponse("Internal Error Creating " . $e,400);
		} catch (\Throwable $e) {
			\DB::rollBack();
			report($e);
			return $this->jsonResponse("Error Creating ". $this->module_name,400);
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        if (!$product) {
			return $this->jsonResponse("Not Found",400);
		}

		return $this->jsonResponse($product);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $auth = \Auth::user();

        if (empty($product)) {
			return $this->jsonResponse("Not Found",400);
		}
        \DB::beginTransaction();
        try {
            $product->id_type_product = $request->id_type_product;
            $product->price = $request->price;
            $product->name_product = $request->name_product;
            $product->slug = \Str::slug($request->name_product);

            if($product->saveOrFail()){
                \DB::commit();
                return $this->jsonResponse($product);
            }else{
                return $this->jsonResponse('Cannot Update ' . $this->module_name,400);
            }
        } catch (\Exception $e) {
			\DB::rollBack();
			report($e);
			return $this->jsonResponse("Internal Error Updating " . $e,400);
		} catch (\Throwable $e) {
			\DB::rollBack();
			report($e);
			return $this->jsonResponse("Error Updating ". $this->module_name,400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        try {
			if ($product) {
				$product->delete();
				return $this->jsonResponse($product);
			} else {
				return $this->jsonResponse('Not Found', 400);
			}
		} catch (\Exception $e) {
			report($e);
            return $this->jsonResponse('Error Deleting ' . $this->module_name,400);
		}
    }
}
