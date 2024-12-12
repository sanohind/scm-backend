{{-- <x-mail::header :url="$url" hidden/> --}}
<x-mail::message>
{{-- header --}}
<h2>Dear Respective In-Charge,</h2>

A new notification status Purchase Order has been submit by Supplier. Please login to check the same.

{{-- Content --}}
<x-mail::panel>
    <p>Supplier code = {{ $data['supplier_code'] }}</p>
    <p>PO number = {{ $data['po_no'] }}</p>
    <p>Status PO = {{ $data['response'] }}</p>
    @if ( $data['response'] == 'Declined')
        <p>Reason = {{ $data['reason'] }}</p> {{-- column for reason--}}
    @endif
</x-mail::panel>

<x-mail::button :url="$url">
View Purchase Order
</x-mail::button>

{{-- Footer --}}
Thanks,<br>
<p>PT. SANOH INDONESIA</p>
{{-- {{ config('app.name') }} --}}
<br>
<p>Note : This is a system generated e-mail. We request that you do not reply to this mail ID.</p>
</x-mail::message>
