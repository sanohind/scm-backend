<?php

namespace App\Http\Controllers\Api\V1\DeliveryNote;

use App\Http\Resources\DeliveryNote\DnDetailListResource;
use Carbon\Carbon;
use App\Trait\ResponseApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\DeliveryNote\DnDetail;
use App\Models\DeliveryNote\DnHeader;
use App\Http\Resources\DeliveryNote\DnDetailResource;
use App\Service\DeliveryNote\DeliveryNoteUpdateTransaction;
use App\Http\Requests\DeliveryNote\UpdateDeliveryNoteRequest;

class DnDetailController extends Controller
{
    /**
     * -------TRAIT---------
     * Mandatory:
     * 1. ResponseApi = Response api should use ResponseApi trait template
     */
    use ResponseApi;

    public function __construct(
        protected DeliveryNoteUpdateTransaction $deliveryNoteUpdateTransaction,) {}

    /**
     * Get list of detail DN based on no_dn
     * @param mixed $no_dn
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getListDetailDnUser($no_dn)
    {
        $dnDetailData = DnDetail::with('dnOutstanding')
            ->where('no_dn', $no_dn)
            ->orderBy('plan_delivery_date', direction: 'asc')
            ->orderBy('dn_line', 'asc')
            ->get();
        if ($dnDetailData->isEmpty()) {
            return $this->returnResponseApi(false, 'DN detail not found', null, 404);
        }

        $dnHeader = $dnDetailData->first()->dnHeader;
        if (!$dnHeader) {
            return $this->returnResponseApi(false, 'DN Header not found', null, 404);
        }

        $date = Carbon::parse($dnHeader->plan_delivery_date)->format('Y-m-d');
        $time = Carbon::parse($dnHeader->plan_delivery_time)->format('H:i');
        $dateTime = "$date $time";

        // Logic to get value confirm_at based on wave/sequence
        $confirmation = [];
        $uniqueTimestamp = [];
        foreach ($dnDetailData as $dnDetail) {
            if ($dnDetail->dnOutstanding) {
                $groupedWave = $dnDetail->dnOutstanding->groupBy('wave');
                foreach ($groupedWave as $wave => $group) {
                    $firstItem = $group->first();

                    $timestamp = "$firstItem->add_outstanding_date $firstItem->add_outstanding_time";

                    // Prevent duplication timestamp
                    if (!in_array($timestamp, $uniqueTimestamp)) {
                        $uniqueTimestamp[] = $timestamp;
                        $confirmation['confirm_' . ($wave + 1) . '_at'] = $timestamp;
                    }
                }
            }
        }
        return $this->returnResponseApi(
            true,
            'Display List DN Detail Successfully',
            new DnDetailListResource(
                $dnHeader,
                $dnDetailData,
                $dateTime,
                $confirmation
            ),
            200
        );
    }

    /**
     * Get all dn detail data
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function indexAll()
    {
        $data_podetail = DnDetail::with('dnHeader')->get();
        if ($data_podetail->isEmpty()) {
            return $this->returnCustomResponseApi(false, 'PO Detail Not Found', null, 404, null, 'success');
        }

        return $this->returnCustomResponseApi(true, 'Display List PO Detail Successfully', DnDetailResource::collection($data_podetail), 200, null, 'success');
    }

    /**
     * Get data dn detail for edit
     * @param mixed $dnDetailNo
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function edit($dnDetailNo)
    {
        $data = DnDetail::with('dnOutstanding')->findOrFail($dnDetailNo);

        return $this->returnResponseApi(true, 'Get DN Edit Successfully', new DnDetailResource($data), 200);
    }

    /**
     * Update dn detail quantity confirm
     * @param \App\Http\Requests\DeliveryNote\UpdateDeliveryNoteRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateDeliveryNoteRequest $request)
    {
        try {
            $request->validated();

            $result = $this->deliveryNoteUpdateTransaction->updateQuantity($request->no_dn, $request->updates);

            return $this->returnResponseApi(true, $result, null, 200);
        } catch (\Throwable $th) {
            return $this->returnResponseApi(false, $th->getMessage() . ' (On line ' . $th->getLine() . ')', null, 500);
        }
    }
}
