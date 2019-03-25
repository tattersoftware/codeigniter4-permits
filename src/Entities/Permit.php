<?php namespace Tatter\Permits\Entities;

use CodeIgniter\Entity;
use Tatter\Permits\Models\PermitModel;

class Permit extends Entity
{
	protected $id;
	protected $session_id;
	protected $user_id;
	protected $ip_address;
	protected $user_agent;
	protected $scheme;
	protected $host;
	protected $port;
	protected $user;
	protected $pass;
	protected $path;
	protected $query;
	protected $fragment;
	protected $views;
	protected $created_at;
	protected $updated_at;
	
	protected $_options = [
		'dates' => ['created_at', 'verified_at'],
		'casts' => [ ],
		'datamap' => [ ]
	];
	
	// magic timezone helpers
	public function setCreatedAt(string $dateString)
	{
		$this->created_at = new Time($dateString, 'UTC');
		return $this;
	}

	public function getCreatedAt(string $format = 'Y-m-d H:i:s')
	{
		// Convert to CodeIgniter\I18n\Time object
		$this->created_at = $this->mutateDate($this->created_at);

		$timezone = $this->timezone ?? app_timezone();

		$this->created_at->setTimezone($timezone);

		return $this->created_at->format($format);
	}
}
