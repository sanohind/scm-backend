{{-- <x-mail::header :url="$url" hidden/> --}}
<x-mail::message>
{{-- header --}}
<h2>Dear Respective In-Charge,</h2>

A new notification Delivery Note order has been confirmed by Supplier. Please login to check the same.

{{-- Content --}}
<x-mail::panel>
    <p>Supplier : {{ $data['supplier_code'] }} - {{ $data['supplier_name'] }}</p>
    <p>Delivery Note Number : {{ $data['no_dn'] }}</p>
</x-mail::panel>

<x-mail::button :url="$url">
View Delivery Note
</x-mail::button>

{{-- Footer --}}
Thanks,<br>
<p>PT. SANOH INDONESIA</p>
{{-- {{ config('app.name') }} --}}
<br>
<p>Note : This is a system generated e-mail. We request that you do not reply to this mail ID.</p>
</x-mail::message>
