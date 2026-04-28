<table>
    <thead>
        <tr>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold; text-align: center;">
                No</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold; width: 200px;">
                Nama Produk</th>
            <th style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold;">Harga
            </th>
            <th style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold;">Stok
            <th style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold;">Species
            </th>
            <th style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold;">Kategori
            </th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold; width: 300px;">
                Detail</th>
            <th style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold;">Status
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
            <tr>
                <td style="border: 1px solid #000000; background-color: {{ $loop->odd % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }}; text-align: center;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $loop->odd % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }};">{{ $product->name }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $loop->odd % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }}; text-align: right;">{{ $product->price }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $loop->odd % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }}; text-align: center;">{{ $product->stock }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $loop->odd % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }};">{{ $product->category->parent->name ?? 'Tanpa Species' }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $loop->odd % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }};">{{ $product->category->name ?? '-' }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $loop->odd % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }};">{{ $product->detail }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $loop->odd % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }}; text-align: center;">{{ ucfirst($product->status) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
