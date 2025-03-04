<?php

namespace App\Http\Controllers\Api\V1\PurchaseOrder;

use App\Http\Requests\PurchaseOrder\PoUpdateRequest;
use App\Trait\AuthorizationRole;
use Carbon\Carbon;
use App\Trait\ResponseApi;
use Illuminate\Http\Request;
use App\Mail\PoResponseInternal;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\PurchaseOrder\PoHeader;
use Illuminate\Support\Facades\Validator;
use App\Service\User\UserGetEmailInternalPurchasing;
use App\Http\Resources\PurchaseOrder\PoHeaderResource;

class PoHeaderController
{
    /**
     * -------TRAIT---------
     * Mandatory:
     * 1. ResponseApi = Response api should use ResponseApi trait template
     * 2. AuthorizationRole = for checking permissible user role
     */
    use ResponseApi, AuthorizationRole;

    /**
     * List of service used
     * @param \App\Service\User\UserGetEmailInternalPurchasing $userGetEmailInternalPurchasing
     */
    public function __construct(
        protected UserGetEmailInternalPurchasing $userGetEmailInternalPurchasing
    ) {
    }

    /**
     * Get list PO Header user
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($this->permissibleRole('5', '6')) {
            $user = Auth::user()->bp_code;
        } elseif ($this->permissibleRole('2', '9')) {
            $user = $request->bp_code;
        }

        if (! isset($user)) {
            return $this->returnCustomFailedResponseApi('error', 'User Not Found', null, 404);
        }

        $poData = PoHeader::with('poDetail')
            ->where('supplier_code', $user)
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
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function indexAll()
    {
        $poData = PoHeader::with('poDetail')->get();
        if ($poData->isEmpty()) {
            return $this->returnResponseApi(true, 'PO Header data not found / empty / all PO data is Closed', null, 200);
        }

        return $this->returnCustomSuccessResponseApi('success', 'Success Display List PO Header', PoHeaderResource::collection($poData), 200);
    }


    public function update(PoUpdateRequest $request, $poNo)
    {
        $request->validated();

        $poHeader = PoHeader::with('poDetail')->find($poNo);
        if (!$poHeader) {
            return $this->returnCustomFailedResponseApi('error', 'PO Header Not Found', null, 404);
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
                Mail::to($email)->send(new PoResponseInternal( $poHeader));
            }
        } catch (\Throwable $th) {
            Log::warning("Failed to send email to PT Sanoh Indonesia Internal. Please check the server configuration / ENV. Error: $th");

            return $this->returnCustomFailedResponseApi('email error', 'Purchase order confirm process successfully, but notification email to PT Sanoh Indonesia error', null, 200);
        }

        return $this->returnCustomSuccessResponseApi('success', 'PO Edited Successfully', new PoHeaderResource($poHeader), 200);
    }
}
