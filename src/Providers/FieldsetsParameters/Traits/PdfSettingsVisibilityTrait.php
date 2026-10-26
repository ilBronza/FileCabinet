<?php

namespace IlBronza\FileCabinet\Providers\FieldsetsParameters\Traits;

trait PdfSettingsVisibilityTrait
{
	protected function configurePdfSettingsVisibility(array $parameters) : array
	{
		$fields = $parameters['pdfSettings']['fields'];
		$targets = [];
		$show = in_array(old('pdf_show', $this->getModel()?->pdf_show), [true, 1, '1', 'true'], true);

		foreach ($fields as $name => $field)
		{
			if ($name === 'pdf_show')
				continue;

			$targets[] = $name;
			if (count($field) === 1)
				$field = static::getFieldParametersFromString($field);

			$field['closed'] = ! $show;
			$fields[$name] = $field;
		}

		$fields['pdf_show']['opener'] = ['targetNames' => $targets, 'value' => 'true'];
		$parameters['pdfSettings']['fields'] = $fields;

		return $parameters;
	}
}
