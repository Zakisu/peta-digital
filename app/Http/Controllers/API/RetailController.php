<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\ControllerTrait;
use App\Http\Traits\UploadFileTrait;
use App\Http\Traits\ResponseTrait;
use App\Http\Traits\GetImageTrait;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Retail;
use App\Models\Schedule;

class RetailController extends Controller
{
    use ResponseTrait,ControllerTrait,UploadFileTrait,GetImageTrait;

    /**
	 * @var Retail
	 */
	protected $table;

    public function __construct(Retail $table)
	{
		$this->table       = $table;
		$this->module_name = 'Retail';
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
        $auth = \Auth::user();

        try {
			\DB::beginTransaction();

            $user = New User;
            $user->id_role = 2;
            $user->is_active = true;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();

            $retail = New Retail;
            $retail->phone_number = $request->phone_number;
            $retail->type_retail = $request->type_retail;
            $retail->name_retail = $request->name_retail;
            $retail->address = $request->address;
            if($request->picture){
                $retail->picture = $this->uploadFile($request->picture,"retail");
            }
            $retail->id_user = $user->id;
            $retail->save();

            \DB::commit();
            return $this->jsonResponse($user ,201);
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
    public function show(Retail $retail)
    {
        if (!$retail) {
			return $this->jsonResponse("Not Found",400);
		}

		return $this->jsonResponse($retail);
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
    public function update(Request $request, Retail $retail)
    {
        $auth = \Auth::user();

        if (empty($retail)) {
			return $this->jsonResponse("Not Found",400);
		}
        try {
			\DB::beginTransaction();

            $retail->phone_number = $request->phone_number;
            $retail->type_retail = $request->type_retail;
            $retail->name_retail = $request->name_retail;
            $retail->address = $request->address;
            if($request->picture){
                $retail->picture = $this->uploadFile($request->picture,"retail");
            }
            $retail->save()

            $user = User::findOrFail($user->id);
            $user->id_role = 2;
            $user->is_active = true;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();
            \DB::commit();
            return $this->jsonResponse($user ,201);
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
    public function destroy(Retail $retail)
    {
        try {
			if ($retail) {
				$retail->delete();
				return $this->jsonResponse($retail);
			} else {
				return $this->jsonResponse('Not Found', 400);
			}
		} catch (\Exception $e) {
			report($e);
            return $this->jsonResponse('Error Deleting ' . $this->module_name,400);
		}
    }
}
