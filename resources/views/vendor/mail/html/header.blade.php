@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
                <span style="color: #2563eb; font-weight: bold; font-size: 24px;">SINAKERTRANS JATIM</span>
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
