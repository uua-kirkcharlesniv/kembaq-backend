<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Business Name</th>
            <th>Address</th>
            <th>Category</th>
            <th>Start Date</th>
            <th>Logo</th>
            <th>Hero</th>
            <th>Background</th>
            <th>Button</th>
            <th>Text</th>
            <th>Border</th>
            <th>Points</th>
            <th>Loyalty Type</th>
            <th>Currency Value</th>
        </tr>

    </thead>
    <tbody>
        @foreach ($merchants as $merchant)
        <tr>
            <td>{{ $merchant->id }}</td>
            <td>{{ $merchant->business_name }}</td>
            <td>{{ $merchant->business_address }}</td>
            <td>{{ $merchant->category }}</td>
            <td>{{ $merchant->created_at }}</td>
            <td>{{ $merchant->logo }}</td>
            <td>{{ $merchant->hero }}</td>
            <td bgcolor="{{ $merchant->background_color }}">       </td>
            <td bgcolor="{{ $merchant->button_color }}">       </td>
            <td bgcolor="{{ $merchant->text_color }}">       </td>
            <td bgcolor="{{ $merchant->border_color }}">       </td>
            <td bgcolor="{{ $merchant->points_color }}">       </td>
            <td>{{ $merchant->loyalty_type == 0 ? 'Stamps' : 'Points' }}</td>
            <td>{{ $merchant->currency }} {{ $merchant->loyalty_value }} = 1</td>
        </tr>
        @endforeach
    </tbody>
</table>