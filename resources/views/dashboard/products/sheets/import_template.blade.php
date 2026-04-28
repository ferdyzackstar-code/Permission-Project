<table>
    <thead>
        <tr>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold; width: 200px;">
                name</th>
            <th style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold;">price
            </th>
            <th style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold;">stock
            </th>
            <th style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold;">
                species_id</th>
            <th style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold;">
                category_id</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold; width: 300px;">
                detail</th>
        </tr>
    </thead>
    <tbody>
        @for ($i = 1; $i <= 5; $i++)
            <tr>
                <td style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }};">
                    Whiskas Adult {{ $i }}kg
                </td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }}; text-align: right;">
                    {{ 50000 + $i * 1000 }}
                </td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }}; text-align: right;">
                    {{ 10 * $i }}
                </td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }}; text-align: center;">
                    1
                </td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }}; text-align: center;">
                    1
                </td>
                <td style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }};">
                    Contoh detail produk ke-{{ $i }}
                </td>
            </tr>
        @endfor
    </tbody>
</table>
