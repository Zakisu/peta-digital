<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class About
 * 
 * @property int $id
 * @property string|null $content
 *
 * @package App\Models
 */
class About extends Model
{
	protected $table = 'about';
	public $timestamps = false;

	protected $fillable = [
		'content'
	];
}
