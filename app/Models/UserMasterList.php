<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserMasterList extends Model
{
	protected $connection = 'app'; 
	protected $table = 'User_MasterList';
	protected $primaryKey = 'TeacherID';

	protected $fillable = [
		'FirstName',
		'LastName',
		'BichakoName',
		'StartDate',
		'EndDate',
		'IsActive',
		'TeacherType',
		'Phone1',
		'Phone2',
		'SecondaryEmail',
		'Website',
		'Bio',
		'HomeLocation',
		'HomeGeolocation',
		'DateAdded',
		'DateUpdated',
		'user_id'
	];

	protected $appends = ['name', 'email'];

	public function getIdAttribute()
	{
		return $this->attributes['TeacherID'];
	}

	public function getNameAttribute()
	{
		return $this->attributes['FirstName'].' '.$this->attributes['BichakoName']
				.' '.$this->attributes['LastName'];
	}

	public function getIsActiveAttribute()
	{
		return $this->attributes['IsActive'];
	}

	public function getTeacherTypeAttribute()
	{
		return $this->attributes['TeacherType'];
	}

	public function getEmailAttribute()
	{
		return $this->user->email;
	}

	public function getPhone1Attribute()
	{
		return $this->attributes['Phone1'];
	}

	public function getPhone2Attribute()
	{
		return $this->attributes['Phone2'];
	}

	public function getGeolocationAttribute()
	{
		return $this->attributes['HomeGeolocation'];
	}

	public function getLocationAttribute()
	{
		return $this->attributes['HomeLocation'];
	}

	public function user(){
		return $this->belongsTo('App\User');
	}

}
