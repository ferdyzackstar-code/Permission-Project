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
                Roles</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                Tanggal Dibuat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
                <td
                    style="border: 1px solid #000000; background-color: {{ $loop->odd ? '#DCE6F1' : '#FFFFFF' }}; text-align: center;">
                    {{ $loop->iteration }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $loop->odd ? '#DCE6F1' : '#FFFFFF' }};">
                    {{ $user->name }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $loop->odd ? '#DCE6F1' : '#FFFFFF' }};">
                    {{ $user->email }}</td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $loop->odd ? '#DCE6F1' : '#FFFFFF' }}; text-align: center;">
                    {{ $user->getRoleNames()->implode(', ') }}</td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $loop->odd ? '#DCE6F1' : '#FFFFFF' }}; text-align: center;">
                    {{ $user->created_at->format('d F Y') }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="3"
                style="border: 1px solid #000000; background-color: #4F81BD; color: #ffffff; font-weight: bold; text-align: center;">
                TOTAL USERS :</td>
            <td colspan="2"
                style="border: 1px solid #000000; background-color: #4F81BD; color: #ffffff; font-weight: bold; text-align: center;">
                {{ $users->count() }}</td>
        </tr>

        @foreach ($roles as $role)
            <tr>
                <td colspan="3"
                    style="border: 1px solid #000000; background-color: #DCE6F1; font-weight: bold; text-align: center;">
                    TOTAL ROLE {{ strtoupper($role->name) }} :
                </td>
                <td colspan="2"
                    style="border: 1px solid #000000; background-color: #DCE6F1; font-weight: bold; text-align: center;">
                    {{ $users->filter(fn($u) => $u->roles->contains('id', $role->id))->count() }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
