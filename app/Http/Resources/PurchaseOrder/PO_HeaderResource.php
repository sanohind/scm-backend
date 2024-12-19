<?php

namespace App\Http\Resources\PurchaseOrder;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PO_HeaderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'po_no' => $this->po_no,
            'po_date' => $this->po_date,
            'planned_receipt_date' => $this->planned_receipt_date,
            'note' => $this->note(),
            'po_revision_no' => $this->po_revision_no,
            'po_revision_date' => $this->po_revision_date,
            'po_status' => $this->statusPo(),
            'response' => $this->statusResponse(),
            'reason' => $this->reason,
        ];
    }

    private function note(){
        $value = ($this->reference_2 == null || $this->reference_2 == '') ? $this->reference_1 : $this->reference_2;

        return $value;
    }

    private function statusResponse(){
        if ($this->response == null || '' && $this->status != null || '') {
            switch ($this->po_status) {
                case 'In Process':
                    $value = 'Accepted';
                    break;

                case 'Declined':
                    $value = 'Declined';
                    break;

                case 'Cancelled':
                    $value = 'Declined';
                    break;

                default:
                    $value = 'No Data';
                    break;
            }
            return $value;
        } else {
            return $this->response;
        }
    }

    private function statusPo() {

        switch ($this->response) {
            case 'Accepted':
                $value = 'In Process';
                break;

            case 'Declined':
                $value = 'Declined';
                break;

            case null:
                $value = $this->po_status;
                break;

            default:
                throw new \Exception("Only accept request 'Accepted','Declined',and 'null'", 500);
        }

        return $value;
    }
}

