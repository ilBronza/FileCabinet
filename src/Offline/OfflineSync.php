<?php

namespace IlBronza\FileCabinet\Offline;

use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Formrow;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;

class OfflineSync
{
    public const TABLE = 'filecabinets__offline_sync_receipts';

    public function __construct(protected OfflineQuestionnaire $questionnaire, protected OfflineAccess $access) {}

    public function apply(Dossier $dossier, array $input) : array
    {
        $this->access->authorize($dossier);
        abort_unless(hash_equals($this->access->owner(), $input['owner']), 403);
        $connection = $dossier->getConnection();

        return $connection->transaction(function () use ($dossier, $input, $connection) {
            // Serialize offline operations for this dossier, including retries of a committed request.
            $dossier = $dossier->newQuery()->lockForUpdate()->findOrFail($dossier->getKey());
            $this->access->authorize($dossier);
            $receiptId = $this->questionnaire->hash([$input['owner'], $dossier->getKey(), $input['operation_id']]);
            $requestHash = $this->questionnaire->hash($input);
            $receipt = $connection->table(self::TABLE)->where('id', $receiptId)->first();
            if ($receipt)
            {
                abort_unless(hash_equals($receipt->request_hash, $requestHash), 409, 'Identificativo di invio già utilizzato.');
                return json_decode($receipt->response, true, 512, JSON_THROW_ON_ERROR);
            }

            $row = $dossier->dossierrows()->without('formrow')->lockForUpdate()->findOrFail($input['row_id']);
            $formrow = Formrow::getProjectClassName()::query()->lockForUpdate()->findOrFail($row->formrow_id);
            $row->setRelation('formrow', $formrow);
            $row->setRelation('dossier', $dossier);
            $field = $this->questionnaire->field($row);

            if (! hash_equals($field['schema'], $input['schema']) || ! hash_equals($field['revision'], $input['revision']))
                throw new HttpResponseException(response()->json([
                    'message' => 'Il campo è cambiato sul server. Scegliere quale risposta conservare.',
                    'field' => $field,
                ], 409));

            abort_unless($field['editable'], 422, 'Questo campo richiede la compilazione online.');
            $value = $input['value'];
            $rules = $row->getFormfieldRules();
            $validation = ['value' => $rules];
            if ($field['type'] === 'multipleSelect')
            {
                // The legacy row stores JSON text; validate each choice before encoding it.
                $validation['value'] = array_values(array_filter($rules, fn ($rule) => ! is_string($rule) || ! str_starts_with($rule, 'in:')));
                $validation['value.*'] = ['string', Rule::in(array_column($field['options'], 'value'))];
            }
            Validator::make(['value' => $value], $validation)->validate();
            if ($field['type'] === 'multipleSelect' && $value !== null)
                $value = json_encode(array_values($value), JSON_THROW_ON_ERROR);
            elseif ($field['type'] === 'datetime' && $value !== null)
                $value = str_replace('T', ' ', $value);

            abort_unless($row->storeRowValue($value), 500, 'Salvataggio non riuscito.');
            // Preserve the existing dossier completion and filecabinet population hooks.
            $dossier->unsetRelation('dossierrows');
            $dossier->touch();
            $response = ['operation_id' => $input['operation_id'], 'field' => $this->questionnaire->field($row->fresh('formrow'))];
            $connection->table(self::TABLE)->insert([
                'id' => $receiptId,
                'request_hash' => $requestHash,
                'response' => json_encode($response, JSON_THROW_ON_ERROR),
                'created_at' => now(),
            ]);

            return $response;
        });
    }
}
