<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
            @php
            $logoPath = public_path('images/' . $themeSettings->logo);
            @endphp

            @if (file_exists($logoPath) && !empty($themeSettings->logo))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="Company Logo">
            @else
            <!-- You can replace this with a default placeholder image or text if the logo is not available -->
            <img src="path/to/default/logo.png" alt="Default Logo">
            @endif
            @else
            {{ $slot }}
            @endif
        </a>

    </td>
</tr>
