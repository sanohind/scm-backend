{{-- @props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ asset('images/logo-sanoh.png') }}"
     class="logo"
     alt="Sanoh Logo"
     style="width: 200px; height: 100px; max-width: 100%; max-height: 100%; border: none;">
@else
{{ $slot }}
@endif
</a>
</td>
</tr> --}}
