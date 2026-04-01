<table>
    <thead>
        <tr>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                Nama</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                Email</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                Password</th>
        </tr>
    </thead>
    <tbody>
        @for ($i = 1; $i <= 5; $i++)
            <tr>
                <td style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }};">
                    User Ke-{{ $i }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }};">
                    Email User Ke-{{ $i }}@gmail.com</td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }}; text-align: center;">
                    Password User Ke-{{ $i }}</td>
            </tr>
        @endfor
    </tbody>
</table>
