<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Village
 * 
 * @property int $id
 * @property string|null $title
 * @property string|null $coordinate
 * @property string|null $image
 * @property string $description
 *
 * @package App\Models
 */
class Village extends Model
{
	protected $table = 'villages';
	public $timestamps = false;

	protected $fillable = [
		'title',
		'coordinate',
		'image',
		'description'
	];

	protected $appends = ['image_URL'];
  public function getImageURLAttribute()
  {
      if ($this->image == null) {
          abort(404);
      }
			$images = json_decode($this->image) ;

			foreach ($images as $image){
				$imageArray[] = asset('uploads/villages/' . $image);
			}
			return $imageArray;
  }
}
