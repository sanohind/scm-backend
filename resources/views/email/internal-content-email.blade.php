<x-mail::header :url="$url" />
<x-mail::message>
{{-- header --}}
<h2>Purchase Order Response</h2>

Purchase Order Response:

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
</x-mail::message>
