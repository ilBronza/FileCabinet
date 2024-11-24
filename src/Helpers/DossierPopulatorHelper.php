<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Dossierrow;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

use function count;

class DossierPopulatorHelper
{
    public Request $request;
    public Dossier $dossier;
    public array $bindingParameters;

    private function validateRequest() : array
    {
        $rules = $this->getDossierValidationRules();

        return $this->getRequest()->validate($rules);
    }

    public function setBindingParameters(array $parameters) : static
    {
        $this->bindingParameters = $parameters;

        return $this;
    }

    public function getBindingParameters() : array
    {
        return $this->bindingParameters;
    }

    public function getParameterByName(string $parameterName) : mixed
    {
        return $this->getBindingParameters()[$parameterName];
    }

    public function getParameter(Dossierrow $dossierrow) : mixed
    {
        $parameter = $this->getParameterByName($dossierrow->getFormfieldName());

        if(! $dossierrow->isRepeatable())
            return $parameter;

        return $parameter[$dossierrow->getKey()];
    }

    public function setRequest(Request $request) : static
    {
        $this->request = $request;

        return $this;
    }

	public function validateAndBindParameters() : self
	{
		$parameters = $this->validateRequest();

		$this->setBindingParameters($parameters);

		return $this;
	}

	public function validateAndBindSingleParameter() : self
	{
		$parameters = $this->validateRequestSingleParameter();

		$this->setBindingParameters($parameters);

		return $this;
	}

	/**
	 * @return array
	 */
	public function getDossierValidationRules() : array
	{
		return $this->getDossier()->getDossierValidationRules();
	}

	protected function setDossierrows() : static
    {
        $this->dossierrows = $this->getDossier()->getDossierrows();

        return $this;
    }

    public function getDossierrows() : Collection
    {
        return $this->dossierrows;
    }

    public function setDossier(Dossier $dossier) : static
    {
        $this->dossier = $dossier;

        $this->setDossierrows();

        return $this;
    }

    public function getDossier() : Dossier
    {
        return $this->dossier;
    }

    public function getRequest() : Request
    {
        return $this->request;
    }

    public function setDossierPopulated()
    {
        $this->getDossier()->setPopulated();
    }

    public function populate()
    {
        foreach($this->getDossierrows() as $dossierrow)
		{
			if($dossierrow->getFormfieldType() == 'file')
				if(count($dossierrow->getMedia("*")) > 0)
					continue;

            if($dossierrow->isFormfieldReadOnly())
                    continue;

			$dossierrow->storeRowValue(
				$this->getParameter(
					$dossierrow
				)
			);
		}

        $this->setDossierPopulated();
    }

	public function returnResponse()
	{
		return $this;
	}

    static function populateByRequest(Request $request, Dossier $dossier)
    {
        $helper = new static();
        $helper->setDossier($dossier)
                ->setRequest($request)
				->validateAndBindParameters()
				->populate();

		return $helper->returnResponse();
    }

}