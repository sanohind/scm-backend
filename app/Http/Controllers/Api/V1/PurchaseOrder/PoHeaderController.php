<?php

namespace App\Http\Controllers\Api\V1\PurchaseOrder;

use App\Http\Requests\PurchaseOrder\PoUpdateRequest;
use App\Http\Resources\PurchaseOrder\PoHeaderResource;
use App\Mail\PoResponseInternal;
use App\Models\PurchaseOrder\PoHeader;
use App\Service\User\UserGetEmailInternalPurchasing;
use App\Service\User\BusinessPartnerUnifiedService;
use App\Trait\AuthorizationRole;
use App\Trait\ResponseApi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PoHeaderController
{
    /**
     * -------TRAIT---------
     * Mandatory:
     * 1. ResponseApi = Response api should use ResponseApi trait template
     * 2. AuthorizationRole = for checking permissible user role
     */
    use AuthorizationRole, ResponseApi;

    /**
     * List of service used
     */
    public function __construct(
        protected UserGetEmailInternalPurchasing $userGetEmailInternalPurchasing,
        protected BusinessPartnerUnifiedService $businessPartnerUnifiedService
    ) {
    }

    /**
     * Get list po based on user (unified search)
     *
     * @param  mixed  $bpCode
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getListPoUser($bpCode = null)
    {
        if ($this->permissibleRole('5', '6')) {
            $user = Auth::user()->bp_code;
        } elseif ($this->permissibleRole('2', '9')) {
            $user = $bpCode;
        }

        if (!isset($user)) {
            return $this->returnCustomResponseApi('error', 'User Not Found', null, 404);
        }

        // Get all related bp_codes (parent and children)
        $relatedBpCodes = $this->businessPartnerUnifiedService->getRelatedBusinessPartners($user);
        $supplierCodes = $relatedBpCodes->pluck('bp_code')->toArray();

        // If no related codes found, use the original bp_code
        if (empty($supplierCodes)) {
            $supplierCodes = [$user];
        }

        $poData = PoHeader::with('poDetail')
            ->whereIn('supplier_code', $supplierCodes)
            ->whereIn('po_status', ['In Process', 'in process'])
            ->orderBy('po_date', 'desc')
            ->get();

        if ($poData->isEmpty()) {
            return $this->returnResponseApi(true, 'PO Header data not found / empty / all PO data is Closed', null, 200);
        }

        return $this->returnResponseApi(true, 'Success Display List PO Header', PoHeaderResource::collection($poData), 200);
    }

    /**
     * Get All Po Header
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function indexAll()
    {
        $poData = PoHeader::with('poDetail')->get();
        if ($poData->isEmpty()) {
            return $this->returnResponseApi(true, 'PO Header data not found / empty / all PO data is Closed', null, 200);
        }

        return $this->returnCustomResponseApi('success', 'Success Display List PO Header', PoHeaderResource::collection($poData), 200);
    }

    /**
     * Update status response
     *
     * @param  mixed  $poNo
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function updateResponse(PoUpdateRequest $request, $poNo)
    {
        $request->validated();

        $poHeader = PoHeader::with('poDetail')->find($poNo);
        if (!$poHeader) {
            return $this->returnCustomResponseApi('error', 'PO Header Not Found', null, 404);
        }

        switch ($request->response) {
            case 'Accepted':
                $poHeader->update([
                    'response' => $request->response,
                    'accept_at' => Carbon::now()->format('Y-m-d H:i'),
                ]);
                break;
            case 'Declined':
                $poHeader->update([
                    'response' => $request->response,
                    'reason' => $request->reason,
                    'decline_at' => Carbon::now()->format('Y-m-d H:i'),
                ]);
                break;
            default:
                return $this->returnResponseApi(false, 'Response Column Not Valid', null, 404);
        }

        try {
            $emailPurchasing = $this->userGetEmailInternalPurchasing->getEmailPurchasing();

            foreach ($emailPurchasing as $email) {
                Mail::to($email)->send(new PoResponseInternal($poHeader));
            }
        } catch (\Throwable $th) {
            Log::warning(
                "Failed to send email to PT Sanoh Indonesia Internal.
                        Please check the server configuration / ENV. Error: $th"
            );

            return $this->returnCustomResponseApi(
                'email error',
                'Purchase order confirm process successfully,
                        but notification email to PT Sanoh Indonesia error',
                null,
                200
            );
        }

        return $this->returnResponseApi(true, 'Success Update Response PO Header', PoHeaderResource::collection([$poHeader]), 200);
    }
}
