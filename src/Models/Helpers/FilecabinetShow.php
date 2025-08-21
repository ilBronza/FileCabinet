<?php

namespace IlBronza\FileCabinet\Models\Helpers;

use IlBronza\FileCabinet\Models\Filecabinet;

class FilecabinetShow extends Filecabinet
{
	public function getRouteClassname()
	{
		return 'filecabinet';
	}

	public function getButtonUrl() : string
	{
		return $this->getShowUrl();
	}

	public function getForeignKey() : string
	{
		return 'filecabinet_id';
	}
}
