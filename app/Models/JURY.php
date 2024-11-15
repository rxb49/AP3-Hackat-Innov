<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class JURY
 * 
 * @property int $idjury
 * @property int $idhackathon
 * @property string $nom
 * 
 * @property HACKATHON $h_a_c_k_a_t_h_o_n
 * @property Collection|JURYMEMBRE[] $j_u_r_y_m_e_m_b_r_e_s
 *
 * @package App\Models
 */
class JURY extends Model
{
	protected $table = 'JURY';
	protected $primaryKey = 'idjury';
	public $timestamps = false;

	protected $casts = [
		'idhackathon' => 'int'
	];

	protected $fillable = [
		'idhackathon',
		'nom'
	];

	public function h_a_c_k_a_t_h_o_n()
	{
		return $this->belongsTo(HACKATHON::class, 'idhackathon');
	}

	public function j_u_r_y_m_e_m_b_r_e_s()
	{
		return $this->hasMany(JURYMEMBRE::class, 'idjury');
	}
}
