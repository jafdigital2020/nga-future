<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/' . $themeSettings->logo))) }}"
                alt="Company Logo">
            @else
            {{ $slot }}
            @endif
        </a>
    </td>
</tr>
