<table>
    <thead>
        <tr>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                No</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                Nama</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                Email</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                Kota</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                Alamat</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                Telepon</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                Status</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                Tanggal Dibuat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($suppliers as $supplier)
            <tr>
                <td
                    style="border: 1px solid #000000; background-color: {{ $loop->odd ? '#DCE6F1' : '#FFFFFF' }}; text-align: center;">
                    {{ $loop->iteration }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $loop->odd ? '#DCE6F1' : '#FFFFFF' }};">
                    {{ $supplier->name }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $loop->odd ? '#DCE6F1' : '#FFFFFF' }};">
                    {{ $supplier->email }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $loop->odd ? '#DCE6F1' : '#FFFFFF' }};">
                    {{ $supplier->city }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $loop->odd ? '#DCE6F1' : '#FFFFFF' }};">
                    {{ $supplier->address }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $loop->odd ? '#DCE6F1' : '#FFFFFF' }}; text-align: left;">
                    {{ $supplier->phone }}</td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $loop->odd ? '#DCE6F1' : '#FFFFFF' }}; text-align: center;">
                    {{ $supplier->status }}</td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $loop->odd ? '#DCE6F1' : '#FFFFFF' }}; text-align: center;">
                    {{ $supplier->created_at->format('d F Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
