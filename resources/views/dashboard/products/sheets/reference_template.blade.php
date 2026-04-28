@php
    // Pure table tanpa wrapping elements
    // FromView Excel parser butuh table yang clean
@endphp

<table>
    <thead>
        <tr>
            <th
                style="background-color: #C0504D; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #000000; padding: 8px; width: 80px;">
                ID KATEGORI
            </th>
            <th
                style="background-color: #C0504D; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #000000; padding: 8px;">
                NAMA KATEGORI
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categories as $category)
            <tr>
                <td
                    style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #F2DCDB; padding: 8px;">
                    {{ $category->id }}
                </td>
                <td style="border: 1px solid #000000; padding: 8px; font-weight: bold;">
                    — {{ $category->name }}
                </td>
            </tr>

            @if ($category->children->count() > 0)
                @foreach ($category->children as $child)
                    <tr>
                        <td style="border: 1px solid #000000; text-align: center; padding: 8px;">
                            {{ $child->id }}
                        </td>
                        <td
                            style="border: 1px solid #000000; padding: 8px; padding-left: 20px; color: #595959; font-style: italic;">
                            {{ $child->name }}
                        </td>
                    </tr>
                @endforeach
            @endif
        @endforeach
    </tbody>
</table>
