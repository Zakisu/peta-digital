<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\ControllerTrait;
use App\Http\Traits\UploadFileTrait;
use App\Http\Traits\ResponseTrait;
use App\Http\Traits\GetImageTrait;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Buyer;


class BuyerController extends Controller
{
    use ResponseTrait,ControllerTrait,UploadFileTrait,GetImageTrait;

     /**
	 * @var Buyer
	 */
	protected $table;

    public function __construct(Buyer $table)
	{
		$this->table       = $table;
		$this->module_name = 'Buyer';
         $this->middleware(function ($request, $next) {
            $this->whereCondition = ['name:=:'.$request->condition_usage];
            $this->whereWithCondition = ['user'=>'name:like:%'.$request->condition_usage_with.'%'];
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
			return $this->jsonResponse("Internal Error Creating", $e);
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

            $buyer = New Buyer;
            $buyer->name = $request->name;
            $buyer->sex = $request->sex;
            $buyer->age = $request->age;
            $buyer->slug = \Str::slug($request->name);
            $buyer->address = $request->address;
            if($request->picture){
                $buyer->picture = $this->uploadFile($request->picture,"buyer");
            }
            $buyer->id_user = $user->id;
            $buyer->save();

            \DB::commit();
            return $this->jsonResponse($user ,201);
		} catch (\Exception $e) {
			\DB::rollBack();
			report($e);
			return $this->jsonResponse("Internal Error Creating " . $this->module_name,400);
		} catch (\Throwable $e) {
			\DB::rollBack();
			report($e);
			return $this->jsonResponse("Error Creating ". $e,400);
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Buyer $buyer)
    {
        if (!$buyer) {
			return $this->jsonResponse("Not Found",400);
		}

		return $this->jsonResponse($buyer);
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
    public function update(Request $request, Buyer $buyer)
    {
        $auth = \Auth::user();

        if (empty($buyer)) {
			return $this->jsonResponse("Not Found",400);
		}
        try {
			\DB::beginTransaction();

            $buyer->name = $request->name;
            $buyer->sex = $request->sex;
            $buyer->age = $request->age;
            $buyer->slug = \Str::slug($request->name);
            $buyer->address = $request->address;
            if($request->picture){
                $buyer->picture = $this->uploadFile($request->picture,"buyer");
            }
            $buyer->save();

            $user = User::findOrFail($buyer->id_user);
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
    public function destroy(Buyer $buyer)
    {
        try {
			if ($buyer) {
                $user = User::findOrFail($buyer->id_user);
                $user->delete();
				$buyer->delete();
				return $this->jsonResponse($buyer);
			} else {
				return $this->jsonResponse('Not Found', 400);
			}
		} catch (\Exception $e) {
			report($e);
            return $this->jsonResponse('Error Deleting ' . $this->module_name,400);
		}
    }
}
