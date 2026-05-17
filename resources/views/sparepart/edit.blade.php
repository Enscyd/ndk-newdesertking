<h2>Edit Spare Part</h2>

<form method="POST" action="{{ route('sparepart.update', $sparepart->id) }}">
    @csrf
    @method('PUT')

    <input name="name" value="{{ $sparepart->name }}"><br><br>

    <input name="part_number" value="{{ $sparepart->part_number }}"><br><br>

    <select name="category_id">
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}"
                {{ $sparepart->category_id == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select><br><br>

    <input name="quantity" type="number" value="{{ $sparepart->quantity }}"><br><br>

    <input name="price" type="number" step="0.01" value="{{ $sparepart->price }}"><br><br>

    <button type="submit">Update</button>
</form>