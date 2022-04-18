<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\ControllerTrait;
use App\Http\Traits\ResponseTrait;
use App\Http\Traits\UploadFileTrait;
use App\Http\Traits\GetImageTrait;
use Illuminate\Http\Request;
use App\Models\Blog;
use Storage;

class BlogController extends Controller
{
    use ResponseTrait,ControllerTrait,UploadFileTrait,GetImageTrait;

     /**
	 * @var Blog
	 */
	protected $table;

    /**
     * http://localhost:8000/api/v1/status?sortOrder=asc&sortField=id&pageNumber=1&pageSize=10&condition_usage=saved&condition_usage_with=admin
     */

    public function __construct(Blog $table)
	{
		$this->table       = $table;
		$this->module_name = 'Blog';
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

            $blog = New Blog;
            $blog->title = $request->title;
            $blog->description = $request->description;
            $blog->id_user = $auth->id;
            $blog->slug = \Str::slug($request->title);
            if($request->picture){
                $blog->picture = $this->uploadFile($request->picture,"blog");
            }
            $blog->save();
            \DB::commit();
            return $this->jsonResponse($blog ,201);
		} catch (\Exception $e) {
			\DB::rollBack();
			report($e);
			return $this->jsonResponse("Internal Error Creating " . $e,400);
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
    public function show(Blog $blog)
    {
        if (!$blog) {
			return $this->jsonResponse("Not Found",400);
		}

		return $this->jsonResponse($blog);
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
    public function update(Request $request, Blog $blog)
    {
        if (empty($blog)) {
			return $this->jsonResponse("Not Found",400);
		}
        \DB::beginTransaction();
        try {
            $blog->title = $request->title;
            $blog->description = $request->description;
            $blog->slug = \Str::slug($request->title);
            if($request->photo){
                $blog->photo = $this->uploadFile($request->photo,"blog");
            }
            if($blog->saveOrFail()){
                \DB::commit();
                return $this->jsonResponse($blog);
            }else{
                return $this->jsonResponse('Cannot Update ' . $this->module_name,400);
            }
        } catch (\Exception $e) {
			\DB::rollBack();
			report($e);
			return $this->jsonResponse("Internal Error Updating " . $this->module_name,400);
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
    public function destroy(Blog $blog)
    {
        try {
			if ($blog) {
				$blog->delete();
				return $this->jsonResponse($blog);
			} else {
				return $this->jsonResponse('Not Found', 400);
			}
		} catch (\Exception $e) {
			report($e);
            return $this->jsonResponse('Error Deleting ' . $this->module_name,400);
		}
    }
}
