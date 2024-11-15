<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class JURYMEMBRE
 * 
 * @property int $idjury_membre
 * @property int $idjury
 * @property string $nom
 * @property string $prenom
 * @property string $email
 * @property string|null $entreprise
 * 
 * @property JURY $j_u_r_y
 *
 * @package App\Models
 */
class JURYMEMBRE extends Model
{
	protected $table = 'JURY_MEMBRE';
	protected $primaryKey = 'idjury_membre';
	public $timestamps = false;

	protected $casts = [
		'idjury' => 'int'
	];

	protected $fillable = [
		'idjury',
		'nom',
		'prenom',
		'email',
		'entreprise'
	];

	public function j_u_r_y()
	{
		return $this->belongsTo(JURY::class, 'idjury');
	}
}
