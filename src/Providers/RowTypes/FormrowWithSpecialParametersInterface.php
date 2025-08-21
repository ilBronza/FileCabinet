<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

interface FormrowWithSpecialParametersInterface
{
	public function getSpecialParametersFieldsetParameters() : array;
	public function getSpecialParametersFields() : array;
}