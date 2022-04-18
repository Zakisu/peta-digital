<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\ControllerTrait;
use App\Http\Traits\ResponseTrait;
use Illuminate\Http\Request;
use App\Models\TypeProduct;

class TypeProductController extends Controller
{
    use ResponseTrait,ControllerTrait;

     /**
	 * @var TypeProduct
	 */
	protected $table;

    public function __construct(TypeProduct $table)
	{
		$this->table       = $table;
		$this->module_name = 'TypeProduct';
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
			return $this->jsonResponse("Internal Error Creating");
		}
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
        try {
			\DB::beginTransaction();

            $type = New TypeProduct;
            $type->name_type = $request->name_type;
            $type->save();
            \DB::commit();
            return $this->jsonResponse($type ,201);
		} catch (\Exception $e) {
			\DB::rollBack();
			report($e);
			return $this->jsonResponse("Internal Error Creating" . $this->module_name,400);
		} catch (\Throwable $e) {
			\DB::rollBack();
			report($e);
			return $this->jsonResponse("Error Creating". $this->module_name,400);
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TypeProduct $typeProduct)
    {
        if (!$typeProduct) {
			return $this->jsonResponse("Not Found",400);
		}

		return $this->jsonResponse($typeProduct);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeProduct $typeProduct)
    {
        if (empty($typeProduct)) {
			return $this->jsonResponse("Not Found",400);
		}
        try {
			\DB::beginTransaction();

            $typeProduct->name_type = $request->name_type;
            if($typeProduct->saveOrFail()){
                \DB::commit();
                return $this->jsonResponse($typeProduct);
            }else{
                return $this->jsonResponse('Cannot Update ' . $this->module_name,400);
            }
		} catch (\Exception $e) {
			\DB::rollBack();
			report($e);
			return $this->jsonResponse("Internal Error Creating" . $this->module_name,400);
		} catch (\Throwable $e) {
			\DB::rollBack();
			report($e);
			return $this->jsonResponse("Error Creating". $this->module_name,400);
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TypeProduct $typeProduct)
    {
        try {
			if ($typeProduct) {
				$typeProduct->delete();
				return $this->jsonResponse($typeProduct);
			} else {
				return $this->jsonResponse('Not Found', 400);
			}
		} catch (\Exception $e) {
			report($e);
            return $this->jsonResponse('Error Deleting ' . $this->module_name,400);
		}
    }
}
