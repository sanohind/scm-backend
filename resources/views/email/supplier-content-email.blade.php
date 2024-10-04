<x-mail::header :url="$url" />
<x-mail::message>
{{-- header --}}
<h2>Purchase Order Open</h2>

Purchase Order Open Today:

{{-- Content --}}
<x-mail::panel>
{{-- @dd($data) --}}
    @if (isset($data) && count($data) > 0)
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
</x-mail::message>
