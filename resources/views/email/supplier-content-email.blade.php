<x-mail::header :url="$url" />
<x-mail::message>
{{-- header --}}
<h2>Dear Respective In-Charge,</h2>

A new information has been sent to your SMS Portal. Please login to check the same.

{{-- Content --}}
<x-mail::panel>
{{-- @dd($data) --}}
    @if (isset($data) && count($data) > 0)
    <p>Purchase Order Open Today:</p>
    @foreach ($data as $index => $po)
        <p>{{ $index + 1 }}. {{ $po['po_no'] }}</p>
    @endforeach
    @else
    <p>No Purchase Order Open</p>
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
